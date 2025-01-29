<?php

namespace App\Http\Controllers\Api\TripApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
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

    public function postTrip(Request $request)
    {
        try{
            $validator = Validation::getValidateTrip($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
