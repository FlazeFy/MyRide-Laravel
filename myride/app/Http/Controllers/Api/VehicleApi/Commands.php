<?php

namespace App\Http\Controllers\Api\VehicleApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
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

    public function putVehicleDetailById(Request $request, $id)
    {
        try{
            // Validator
            $vehicle_transmission = "CVT"; 
            if ($request->vehicle_transmission === "MT") {
                $vehicle_transmission = "Manual";
            } else if ($request->vehicle_transmission === "AT") {
                $vehicle_transmission = "Automatic";
            }

            $request->merge(['vehicle_transmission' => $vehicle_transmission]);
            $validator = Validation::getValidateVehicle($request,'detail');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
