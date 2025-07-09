<?php

namespace App\Http\Controllers\Api\TripApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Telegram
use Telegram\Bot\Laravel\Facades\Telegram;
// Model
use App\Models\VehicleModel;
use App\Models\TripModel;
use App\Models\UserModel;
// Helper
use App\Helpers\Validation;
use App\Helpers\Generator;

class Commands extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "trip";
    }

    /**
     * @OA\POST(
     *     path="/api/v1/trip",
     *     summary="Post create trip",
     *     description="Create a new trip using `vehicle_id`, `trip_desc`, `trip_category`, `trip_origin_name`, `trip_person`, `trip_origin_coordinate`, `trip_destination_coordinate`, and `trip_destination_name`. This request is using MySQL database and send Telegram Message.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id","trip_category","trip_origin_name","trip_destination_name"},
     *             @OA\Property(property="vehicle_id", type="string", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *             @OA\Property(property="trip_desc", type="string", example="Business meeting"),
     *             @OA\Property(property="trip_category", type="string", example="Business"),
     *             @OA\Property(property="trip_origin_name", type="string", example="Office A"),
     *             @OA\Property(property="trip_person", type="string", example="John Doe"),
     *             @OA\Property(property="trip_origin_coordinate", type="string", example="-6.9175,107.6191"),
     *             @OA\Property(property="trip_destination_coordinate", type="string", example="-6.2000,106.8167"),
     *             @OA\Property(property="trip_destination_name", type="string", example="Office B")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trip created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip created"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="trip category must be at least 2 characters")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="trip category is a required field")
     *                 ),
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="vehicle not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     )
     * )
     */
    public function postTrip(Request $request)
    {
        try{
            $validator = Validation::getValidateTrip($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user_id = $request->user()->id;
                $vehicle_id = $request->vehicle_id;

                // Service : Validate existing vehicle and get the identity
                $vehicle = VehicleModel::getVehicleIdentity($user_id,$vehicle_id);
                if($vehicle){
                    $vehicle_plate_number = $vehicle->vehicle_plate_number;
                    $vehicle_name = $vehicle->vehicle_name;
                    $trip_origin_name = $request->trip_origin_name;
                    $trip_destination_name = $request->trip_destination_name;

                    // Service : Update
                    $rows = TripModel::create([
                        'id' => Generator::getUUID(), 
                        'vehicle_id' => $vehicle_id, 
                        'trip_desc' => $request->trip_desc, 
                        'trip_category' => $request->trip_category, 
                        'trip_person' => $request->trip_person, 
                        'trip_origin_name' => $trip_origin_name, 
                        'trip_origin_coordinate' => $request->trip_origin_coordinate, 
                        'trip_destination_name' => $trip_destination_name, 
                        'trip_destination_coordinate' => $request->trip_destination_coordinate, 
                        'created_at' => date('Y-m-d H:i:s'), 
                        'created_by' => $user_id, 
                        'updated_at' => null, 
                        'deleted_at' => null
                    ]);

                    // Respond
                    if($rows){
                        $user = UserModel::getSocial($user_id);
                        $message = "Hello $user->username, your have added trip history from $trip_origin_name to $trip_destination_name using $vehicle_name ($vehicle_plate_number)";
                        if($user->telegram_user_id){
                            $response = Telegram::sendMessage([
                                'chat_id' => $user->telegram_user_id,
                                'text' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        }
                        
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("create", $this->module),
                        ], Response::HTTP_CREATED);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("not_found", $this->module),
                        ], Response::HTTP_NOT_FOUND);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", 'vehicle'),
                    ], Response::HTTP_NOT_FOUND);
                }
            } 
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
