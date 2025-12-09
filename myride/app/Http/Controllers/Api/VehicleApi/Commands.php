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
use App\Models\AdminModel;
use App\Models\TripModel;
use App\Models\ServiceModel;
use App\Models\InventoryModel;
use App\Models\FuelModel;
use App\Models\ReminderModel;
use App\Models\WashModel;

// Helper
use App\Helpers\Validation;
use App\Helpers\Generator;
use App\Helpers\TelegramMessage;
use App\Helpers\Firebase;

class Commands extends Controller
{
    private $module;
    private $max_size_file;
    private $allowed_file_type;

    public function __construct()
    {
        $this->module = "vehicle";
        $this->max_size_file = 5000000; // 10 Mb
        $this->allowed_file_type = ['jpg','jpeg','gif','png'];
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
                        if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                            $response = Telegram::sendMessage([
                                'chat_id' => $user->telegram_user_id,
                                'text' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        } else {
                            UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                        }
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

    /**
     * @OA\PUT(
     *     path="/api/v1/vehicle",
     *     summary="Post vehicle",
     *     description="Create a new vehicle using `vehicle_name`, `vehicle_merk`, `vehicle_type`, `vehicle_price`, `vehicle_distance`, `vehicle_category`, `vehicle_status`, `vehicle_year_made`, `vehicle_plate_number`, `vehicle_fuel_status`, `vehicle_default_fuel`, `vehicle_color`, `vehicle_transmission`, and `vehicle_capacity`. This request is using MySQL database and send Telegram Message.",
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
     *         response=201,
     *         description="Vehicle create successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle create")
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
    public function postVehicle(Request $request)
    {
        try{
            // Validator
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
                $extra_msg = null;

                $vehicle_image = null;
                if ($request->hasFile('vehicle_image')) {
                    $file = $request->file('vehicle_image');
                    if ($file->isValid()) {
                        $file_ext = $file->getClientOriginalExtension();
                        // Validate file type
                        if (!in_array($file_ext, $this->allowed_file_type)) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("custom", 'The file must be a '.implode(', ', $this->allowed_file_type).' file type'),
                            ], Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        // Validate file size
                        if ($file->getSize() > $this->max_size_file) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("custom", 'The file size must be under '.($this->max_size_file/1000000).' Mb'),
                            ], Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
        
                        // Helper: Upload vehicle image
                        try {
                            $user = UserModel::find($user_id);
                            $vehicle_image = Firebase::uploadFile('vehicle', $user_id, $user->username, $file, $file_ext); 
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => Generator::getMessageTemplate("unknown_error", null),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }
                }

                $vehicle_other_img_url = [];
                if ($request->hasFile('vehicle_other_img_url')) {
                    foreach ($request->file('vehicle_other_img_url') as $file) {
                        if ($file->isValid()) {
                            $file_ext = $file->getClientOriginalExtension();

                            // Validate file type
                            if (!in_array($file_ext, $this->allowed_file_type)) {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => Generator::getMessageTemplate("custom", 'The file must be a '.implode(', ', $this->allowed_file_type).' file type'),
                                ], Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            // Validate file size
                            if ($file->getSize() > $this->max_size_file) {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => Generator::getMessageTemplate("custom", 'The file size must be under '.($this->max_size_file/1000000).' Mb'),
                                ], Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
            
                            // Helper: Upload vehicle image
                            try {
                                $user = UserModel::find($user_id);
                                $vehicle_image = Firebase::uploadFile('vehicle', $user_id, $user->username, $file, $file_ext); 
                                $vehicle_other_img_url[] = (object)[
                                    'vehicle_image_url' => $vehicle_image
                                ];
                            } catch (\Exception $e) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => Generator::getMessageTemplate("unknown_error", null),
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }
                        }
                    }
                }
                if(count($vehicle_other_img_url) === 0){
                    $vehicle_other_img_url = null;
                }

                $rows = VehicleModel::createVehicle([
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
                    'vehicle_img_url' => $vehicle_image,
                    'vehicle_other_img_url' => $vehicle_other_img_url,
                    'vehicle_color' => $request->vehicle_color,
                    'vehicle_transmission' => $request->vehicle_transmission,
                    'vehicle_capacity' => $request->vehicle_capacity,
                ], $user_id);

                // Respond
                if($rows){
                    $user = UserModel::getSocial($user_id);
                    if($user->telegram_user_id){
                        if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                            $message = "Hello $user->username, your vehicle with name $vehicle_name ($vehicle_plate_number) data has been created";
                            $response = Telegram::sendMessage([
                                'chat_id' => $user->telegram_user_id,
                                'text' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        } else {
                            $extra_msg = ' Telegram ID is invalid. Please check your Telegram ID';
                        }
                    }
                    
                    if($extra_msg){
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("custom", "vehicle created, but$message"),
                        ], Response::HTTP_CREATED);
                    } else {
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("create", $this->module),
                        ], Response::HTTP_CREATED);
                    }
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

    /**
     * @OA\DELETE(
     *     path="/api/v1/vehicle/delete/{id}",
     *     summary="Soft Delete vehicle by Id",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to deleted",
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
     *     ),
     * )
     */
    public function softDeleteVehicleById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            $check_admin = AdminModel::find($user_id);
            if($check_admin){
                $user_id = null;
            }

            $rows = VehicleModel::softDeleteVehicleById($user_id,$id);
            if($rows > 0){
                $user = UserModel::getSocial($user_id);
                $vehicle = VehicleModel::find($id);
                if($user->telegram_user_id){
                    if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                        $message = "Hello $user->username, your vehicle with name $vehicle->vehicle_name ($vehicle->vehicle_plate_number) data has been deleted. You can still recovered deleted vehicle before 30 days after deletion process";
                        $response = Telegram::sendMessage([
                            'chat_id' => $user->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                    }
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("delete", $this->module),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", $this->module),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/vehicle/recover/{id}",
     *     summary="Recover vehicle by Id",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle recovered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle recovered")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to deleted",
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
     *     ),
     * )
     */
    public function recoverVehicleById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            $check_admin = AdminModel::find($user_id);
            if($check_admin){
                $user_id = null;
            }

            $rows = VehicleModel::recoverVehicleById($user_id,$id);
            if($rows > 0){
                return response()->json([
                    'status' => 'success',
                    'rows_affected' => $rows,
                    'message' => Generator::getMessageTemplate("recover", $this->module),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", $this->module),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/vehicle/destroy/{id}",
     *     summary="Hard Delete vehicle by Id",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle permanentelly deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle permanentelly deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to deleted",
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
     *     ),
     * )
     */
    public function hardDeleteVehicleById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            $check_admin = AdminModel::find($user_id);
            if($check_admin){
                $user_id = null;
            }

            $vehicle = VehicleModel::find($id);
            $rows = VehicleModel::hardDeleteVehicleById($user_id,$id);
            if($rows > 0){
                // Delete Firebase Uploaded Image
                if($vehicle->vehicle_img_url){
                    if(!Firebase::deleteFile($vehicle->vehicle_img_url)){
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("not_found", 'failed to delete vehicle image'),
                        ], Response::HTTP_NOT_FOUND);
                    }
                }

                WashModel::hardDeleteByVehicleId($id);
                FuelModel::hardDeleteByVehicleId($id);
                InventoryModel::hardDeleteByVehicleId($id);
                ReminderModel::hardDeleteByVehicleId($id);
                ServiceModel::hardDeleteByVehicleId($id);
                TripModel::hardDeleteByVehicleId($id);

                $user = UserModel::getSocial($user_id);
                if($user->telegram_user_id){
                    if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                        $message = "Hello $user->username, your vehicle $vehicle->vehicle_name ($vehicle->vehicle_plate_number) is permanently deleted";
                        // Report to user
                        $response = Telegram::sendMessage([
                            'chat_id' => $user->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("permentally delete", $this->module),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", $this->module),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
