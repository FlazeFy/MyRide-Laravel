<?php

namespace App\Http\Controllers\Api\VehicleApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Telegram
use Telegram\Bot\Laravel\Facades\Telegram;
// Model
use App\Models\VehicleModel;
use App\Models\UserModel;
// Helper
use App\Helpers\Validation;
use App\Helpers\Generator;

class Commands extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "vehicle";
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/vehicle/{id}",
     *     summary="Put Vehicle Detail By Id",
     *     description="Update a new vehicle using `vehicle_name`, `vehicle_merk`, `vehicle_type`, `vehicle_price`, `vehicle_distance`, `vehicle_category`, `vehicle_status`, `vehicle_year_made`, `vehicle_plate_number`, `vehicle_fuel_status`, `vehicle_default_fuel`, `vehicle_color`, `vehicle_transmission`, and `vehicle_capacity`. This request is using MySQL database and send Telegram Message.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_name","vehicle_merk","vehicle_type","vehicle_price","vehicle_distance","vehicle_category","vehicle_status","vehicle_year_made","vehicle_plate_number","vehicle_fuel_status","vehicle_default_fuel","vehicle_color","vehicle_transmission","vehicle_capacity"},
     *             @OA\Property(property="vehicle_name", type="string", example="Kijang Innova 2.0 Type G MT"),
     *             @OA\Property(property="vehicle_merk", type="string", example="Toyota"),
     *             @OA\Property(property="vehicle_type", type="string", example="Minibus"),
     *             @OA\Property(property="vehicle_price", type="integer", example=275000000),
     *             @OA\Property(property="vehicle_desc", type="string", example="sudah jarang digunakan 2"),
     *             @OA\Property(property="vehicle_distance", type="integer", example=90000),
     *             @OA\Property(property="vehicle_category", type="string", example="Parents Car"),
     *             @OA\Property(property="vehicle_status", type="string", example="Available"),
     *             @OA\Property(property="vehicle_year_made", type="integer", example=2011),
     *             @OA\Property(property="vehicle_plate_number", type="string", example="PA 1234 ZX"),
     *             @OA\Property(property="vehicle_fuel_status", type="string", example="Not Monitored"),
     *             @OA\Property(property="vehicle_fuel_capacity", type="integer", example=50),
     *             @OA\Property(property="vehicle_default_fuel", type="string", example="Pertamina Pertalite"),
     *             @OA\Property(property="vehicle_color", type="string", example="White"),
     *             @OA\Property(property="vehicle_transmission", type="string", example="Manual"),
     *             @OA\Property(property="vehicle_capacity", type="integer", example=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle update successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle update")
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
     *                     @OA\Property(property="message", type="string", example="vehicle name must be at least 2 characters")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="vehicle type is a required field")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="vehicle type must be one of the following values City Car, Minibus, Motorcycle, Hatchback, Sedan, SUV, Pickup Truck, Convertible, Coupe, Van, Wagon, Crossover, Electric")
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
    public function putVehicleDetailById(Request $request, $id)
    {
        try{
            // Validator
            $vehicle_transmission = null;
            if ($request->vehicle_transmission === "MT") {
                $vehicle_transmission = "Manual";
            } else if ($request->vehicle_transmission === "AT") {
                $vehicle_transmission = "Automatic";
            } else if ($request->vehicle_transmission === "CVT") {
                $vehicle_transmission = "CVT"; 
            }

            $request->merge(['vehicle_transmission' => $vehicle_transmission]);
            $validator = Validation::getValidateVehicle($request,'detail');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user_id = $request->user()->id;
                $vehicle_name = $request->vehicle_name." ".$request->vehicle_transmission_code;
                $vehicle_plate_number = $request->vehicle_plate_number;

                // Service : Update
                $rows = VehicleModel::where('id',$id)
                    ->where('created_by',$user_id)
                    ->update([
                        'vehicle_name' => $vehicle_name,
                        'vehicle_merk' => $request->vehicle_merk,
                        'vehicle_type' => $request->vehicle_type,
                        'vehicle_price' => $request->vehicle_price,
                        'vehicle_desc' => $request->vehicle_desc,
                        'vehicle_distance' => $request->vehicle_distance,
                        'vehicle_category' => $request->vehicle_category,
                        'vehicle_status' => $request->vehicle_status,
                        'vehicle_year_made' => $request->vehicle_year_made,
                        'vehicle_plate_number' => $vehicle_plate_number,
                        'vehicle_fuel_status' => $request->vehicle_fuel_status,
                        'vehicle_fuel_capacity' => $request->vehicle_fuel_capacity,
                        'vehicle_default_fuel' => $request->vehicle_default_fuel,
                        'vehicle_color' => $request->vehicle_color,
                        'vehicle_transmission' => $vehicle_transmission,
                        'vehicle_capacity' => $request->vehicle_capacity,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                // Respond
                if($rows > 0){
                    $user = UserModel::getSocial($user_id);
                    $message = "Hello $user->username, your vehicle with name $vehicle_name ($vehicle_plate_number) data has been updated";
                    if($user->telegram_user_id){
                        $response = Telegram::sendMessage([
                            'chat_id' => $user->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    }
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("update", $this->module),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", $this->module),
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
