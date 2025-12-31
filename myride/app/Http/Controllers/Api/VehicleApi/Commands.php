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
use App\Models\HistoryModel;
// Helper
use App\Helpers\Validation;
use App\Helpers\Generator;
use App\Helpers\TelegramMessage;
use App\Helpers\Firebase;

class Commands extends Controller
{
    private $module;
    private $max_size_file;
    private $max_doc_size_file;
    private $allowed_file_type;
    private $allowed_doc_file_type;

    public function __construct()
    {
        $this->module = "vehicle";
        $this->max_size_file = 5000000; // 5 Mb
        $this->max_doc_size_file = 10000000; // 10 Mb
        $this->allowed_file_type = ['jpg','jpeg','gif','png'];
        $this->allowed_doc_file_type = ['jpg','jpeg','gif','png','pdf'];
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/vehicle/{id}",
     *     summary="Put Vehicle Detail By ID",
     *     description="This request is used to update a vehicle by using given `ID`. The updated fields are `vehicle_name`, `vehicle_merk`, `vehicle_type`, `vehicle_price`, `vehicle_distance`, `vehicle_category`, `vehicle_status`, `vehicle_year_made`, `vehicle_plate_number`, `vehicle_fuel_status`, `vehicle_default_fuel`, `vehicle_color`, `vehicle_transmission`, and `vehicle_capacity`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
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
            // Validate request body
            $validator = Validation::getValidateVehicle($request,'detail');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user_id = $request->user()->id;
                $vehicle_name = $request->vehicle_name." ".$request->vehicle_transmission;
                $vehicle_plate_number = $request->vehicle_plate_number;

                // Update vehicle by ID
                $rows = VehicleModel::updateVehicleById([
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
                    'vehicle_transmission' => $request->vehicle_transmission,
                    'vehicle_capacity' => $request->vehicle_capacity,
                ], $id, $user_id);
                if($rows > 0){
                    // Get user social contact
                    $user = UserModel::getSocial($user_id);
                    // Check if user's Telegram ID is valid
                    $message = "Hello $user->username, your vehicle with name $vehicle_name ($vehicle_plate_number) data has been updated";
                    if($user->telegram_user_id && $user->telegram_is_valid === 1){
                        if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                            // Send telegram message
                            $response = Telegram::sendMessage([
                                'chat_id' => $user->telegram_user_id,
                                'text' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        } else {
                            // Reset telegram from user account if not valid
                            UserModel::updateUserById(['telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                        }
                    }

                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Vehicle', 'history_context' => "edited a vehicle called $vehicle_name"], $user_id);

                    // Return success response
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
     * @OA\POST(
     *     path="/api/v1/vehicle/image/{id}",
     *     summary="Post Update Vehicle Image By ID",
     *     description="This request is used to update vehicle image by given vehicle's `ID`. And the updated field is `vehicle_img_url`. This request interacts with the MySQL database, firebase storage, has a protected routes, and audited activity (history).",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"vehicle_img_url"},
     *                  @OA\Property(property="vehicle_img_url", type="string", format="binary"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vehicle image update successfully",
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
     *                     @OA\Property(property="message", type="string", example="vehicle image is a required field")
     *                 )
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
    public function putVehicleImageById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Get vehicle by ID
            $vehicle = VehicleModel::getVehicleByIdAndUserId($id, $user_id);
            if ($vehicle){
                $vehicle_image = null;

                // Check if a vehicle image exists in the old vehicle data
                if ($vehicle->vehicle_img_url){
                    if(!Firebase::deleteFile($vehicle->vehicle_img_url)){
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("not_found", 'failed to delete vehicle image'),
                        ], Response::HTTP_NOT_FOUND);
                    }
                }

                // Check if file attached
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
        
                        try {
                            // Get user data
                            $user = UserModel::getSocial($user_id);
                            // Upload file to Firebase storage
                            $vehicle_image = Firebase::uploadFile('vehicle', $user_id, $user->username, $file, $file_ext); 
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => Generator::getMessageTemplate("unknown_error", null),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }

                // Update vehicle by ID
                $rows = VehicleModel::updateVehicleById([ 'vehicle_img_url' => $vehicle_image], $id, $user_id);
                if($rows > 0){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Vehicle', 'history_context' => "edited a vehicle image of $vehicle->vehicle_name"], $user_id);

                    // Return success response
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
     * @OA\POST(
     *     path="/api/v1/vehicle/image_collection/{id}",
     *     summary="Post Update Vehicle Image Collection By Id",
     *     description="This request is used to update vehicle image collection by given vehicle's `ID`. The updated field is `vehicle_other_img_url`. This request interacts with the MySQL database, firebase storage, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"vehicle_other_img_url"},
     *                  @OA\Property(property="vehicle_other_img_url", type="string", format="binary"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vehicle image collection update successfully",
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
     *                     @OA\Property(property="message", type="string", example="vehicle image collection is a required field")
     *                 )
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
    public function putVehicleImageCollectionById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Get vehicle by ID
            $vehicle = VehicleModel::getVehicleByIdAndUserId($id, $user_id);
            if($vehicle){
                $vehicle_other_img_url = [];
                // Check if file attached
                if($request->hasFile('vehicle_other_img_url')){
                    // Iterate to upload file
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
        
                            try {
                                // Get user data
                                $user = UserModel::getSocial($user_id);
                                // Upload file to Firebase storage
                                $vehicle_img_url = Firebase::uploadFile('vehicle', $user_id, $user->username, $file, $file_ext); 
                                $vehicle_other_img_url[] = (object)[
                                    'vehicle_img_id' => Generator::getUUID(),
                                    'vehicle_img_url' => $vehicle_img_url
                                ];
                            } catch (\Exception $e) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => Generator::getMessageTemplate("unknown_error", null),
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }
                        }
                    }
                } else if($vehicle->vehicle_other_img_url && !$request->hasFile('vehicle_other_img_url')){
                    // If file not attached and there is some image exist in the old data
                    foreach ($vehicle->vehicle_other_img_url as $dt) {
                        // Delete failed if file not found (already gone)
                        if(!Firebase::deleteFile($dt['vehicle_img_url'])){
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("not_found", 'failed to delete vehicle image'),
                            ], Response::HTTP_NOT_FOUND);
                        }
                    }
                }

                // Make null if array image empty
                if(count($vehicle_other_img_url) === 0){
                    $vehicle_other_img_url = null;
                } else {
                    if($vehicle->vehicle_other_img_url){
                        // If old image collection not empty, combine with the new image collection
                        $vehicle_other_img_url = array_merge($vehicle_other_img_url, $vehicle->vehicle_other_img_url);
                    }
                }

                // Update vehicle by ID
                $rows = VehicleModel::updateVehicleById([ 'vehicle_other_img_url' => $vehicle_other_img_url ], $id, $user_id);
                if($rows > 0){
                    // Return success response
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
     *     path="/api/v1/vehicle/image_collection/destroy/{vehicle_id}/{image_id}",
     *     summary="Delete Vehicle Image Collection By Image ID",
     *     description="This request is used to delete an image in vehicle by given `vehicle_id` and `image_id` for the image collection. Updated field is `vehicle_other_img_url`. This request interacts with the MySQL database, firebase storage, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Parameter(
     *         name="image_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle Image ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle image collection update successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle image update")
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
     *                     @OA\Property(property="message", type="string", example="vehicle image collection is a required field")
     *                 )
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
    public function hardDeleteVehicleImageCollectionById(Request $request, $vehicle_id, $image_id)
    {
        try{
            $user_id = $request->user()->id;

            // Get vehicle by ID
            $vehicle = VehicleModel::getVehicleByIdAndUserId($vehicle_id,$user_id);
            if($vehicle){
                $vehicle_other_img_urls = $vehicle->vehicle_other_img_url;
                // Iterate to delete file
                foreach ($vehicle_other_img_urls as $dt) {
                    if ($dt['vehicle_img_id'] === $image_id) {
                        // Delete failed if file not found (already gone)
                        if(!Firebase::deleteFile($dt['vehicle_img_url'])){
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("not_found", 'failed to delete vehicle image'),
                            ], Response::HTTP_NOT_FOUND);
                        }
                        break;
                    }
                }
            
                // Remove image from vehicle image collection by its image ID
                $vehicle_other_img_urls = array_filter($vehicle_other_img_urls, function ($dt) use ($image_id) {
                    return $dt['vehicle_img_id'] !== $image_id;
                });
                $vehicle_other_img_url = array_values($vehicle_other_img_urls);
                
                // Update vehicle by ID
                $rows = VehicleModel::updateVehicleById([
                    'vehicle_other_img_url' => count($vehicle_other_img_url) > 0 ? $vehicle_other_img_url : null,
                ], $vehicle_id, $user_id);

                if($rows > 0){
                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("delete", "$this->module image"),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", "$this->module image"),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", "$this->module image"),
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
     * @OA\POST(
     *     path="/api/v1/vehicle",
     *     summary="Post Create Vehicle",
     *     description="This request is used to create a new vehicle using `vehicle_name`, `vehicle_merk`, `vehicle_type`, `vehicle_price`, `vehicle_distance`, `vehicle_category`, `vehicle_status`, `vehicle_year_made`, `vehicle_plate_number`, `vehicle_fuel_status`, `vehicle_default_fuel`, `vehicle_color`, `vehicle_transmission`, `vehicle_capacity`, `vehicle_img_url`, and `vehicle_other_img_url`. This request interacts with the MySQL database, firebase storage (for vehicle_image and vehicle_other_img), broadcast using Telegram, has a protected routes, and audited activity (history).",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"vehicle_name","vehicle_merk","vehicle_type","vehicle_price","vehicle_distance","vehicle_category","vehicle_status","vehicle_year_made","vehicle_plate_number","vehicle_fuel_status","vehicle_default_fuel","vehicle_color","vehicle_transmission","vehicle_capacity"},
     *                  @OA\Property(property="vehicle_name", type="string", example="Kijang Innova 2.0 Type G MT"),
     *                  @OA\Property(property="vehicle_merk", type="string", example="Toyota"),
     *                  @OA\Property(property="vehicle_type", type="string", example="Minibus"),
     *                  @OA\Property(property="vehicle_price", type="integer", example=275000000),
     *                  @OA\Property(property="vehicle_desc", type="string", example="sudah jarang digunakan 2"),
     *                  @OA\Property(property="vehicle_distance", type="integer", example=90000),
     *                  @OA\Property(property="vehicle_category", type="string", example="Parents Car"),
     *                  @OA\Property(property="vehicle_status", type="string", example="Available"),
     *                  @OA\Property(property="vehicle_year_made", type="integer", example=2011),
     *                  @OA\Property(property="vehicle_plate_number", type="string", example="PA 1234 ZX"),
     *                  @OA\Property(property="vehicle_fuel_status", type="string", example="Not Monitored"),
     *                  @OA\Property(property="vehicle_fuel_capacity", type="integer", example=50),
     *                  @OA\Property(property="vehicle_default_fuel", type="string", example="Pertamina Pertalite"),
     *                  @OA\Property(property="vehicle_color", type="string", example="White"),
     *                  @OA\Property(property="vehicle_transmission", type="string", example="Manual"),
     *                  @OA\Property(property="vehicle_capacity", type="integer", example=8),
     *                  @OA\Property(property="vehicle_image", type="string", format="binary"),
     *                  @OA\Property(property="vehicle_other_img_url", type="string", format="binary")
     *              )
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
            // Validate request body
            $validator = Validation::getValidateVehicle($request,'detail');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user_id = $request->user()->id;
                $vehicle_name = $request->vehicle_name." ".$request->vehicle_transmission;
                $vehicle_plate_number = $request->vehicle_plate_number;
                $extra_msg = null;
                $vehicle_image = null;

                // Get user data
                $user = UserModel::getSocial($user_id);
                // Check if file attached
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
        
                        try {
                            // Upload file to Firebase storage
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
                    // Iterate to upload file
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
            
                            try {
                                // Upload file to Firebase storage
                                $vehicle_img_url = Firebase::uploadFile('vehicle', $user_id, $user->username, $file, $file_ext); 
                                $vehicle_other_img_url[] = (object)[
                                    'vehicle_img_id' => Generator::getUUID(),
                                    'vehicle_img_url' => $vehicle_img_url
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
                // Make vehicle image collection null if empty array
                if(count($vehicle_other_img_url) === 0){
                    $vehicle_other_img_url = null;
                }

                // Create vehicle
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
                if($rows){
                    $vehicle_plate_number_and_name = "$vehicle_name ($vehicle_plate_number)";

                    // Get user data
                    $user = UserModel::getSocial($user_id);
                    if($user->telegram_user_id && $user->telegram_is_valid === 1){
                        // Check if user Telegram ID is valid
                        if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                            $message = "Hello $user->username, your vehicle with name $vehicle_plate_number_and_name data has been created";
                            // Send telegram message
                            $response = Telegram::sendMessage([
                                'chat_id' => $user->telegram_user_id,
                                'text' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        } else {
                            // Reset telegram from user account if not valid
                            UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                            $extra_msg = ' Telegram ID is invalid. Please check your Telegram ID';
                        }
                    }

                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Vehicle', 'history_context' => "added a vehicle called $vehicle_plate_number_and_name"], $user_id);
                    
                    // Return success response
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
     * @OA\POST(
     *     path="/api/v1/vehicle/doc/{id}",
     *     summary="Post Update Vehicle Document By ID",
     *     description="This request is used to update vehicle document by given vehicle's `ID`. The updated field is `vehicle_document`. This request interacts with the MySQL database, firebase storage, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"vehicle_document"},
     *                  @OA\Property(property="vehicle_document", type="string", format="binary"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vehicle document create successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle document create")
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
     *                     @OA\Property(property="message", type="string", example="The file size must be under 10 mb")
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
    public function postVehicleDoc(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Get vehicle by ID
            $vehicle = VehicleModel::getVehicleByIdAndUserId($id,$user_id);
            if($vehicle){
                $vehicle_document = $vehicle->vehicle_document ?? [];
                // Check if file attached
                if ($request->hasFile('vehicle_document')) {
                    // Get user data
                    $user = UserModel::getSocial($user_id);

                    // Iterate to upload file
                    foreach ($request->file('vehicle_document') as $idx => $file) {
                        if ($file->isValid()) {
                            $file_ext = $file->getClientOriginalExtension();

                            // Validate file type
                            if (!in_array($file_ext, $this->allowed_doc_file_type)) {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => Generator::getMessageTemplate("custom", 'The file must be a '.implode(', ', $this->allowed_doc_file_type).' file type'),
                                ], Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                            // Validate file size
                            if ($file->getSize() > $this->max_doc_size_file) {
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => Generator::getMessageTemplate("custom", 'The file size must be under '.($this->max_doc_size_file/1000000).' Mb'),
                                ], Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
            
                            try {
                                if($request->has('vehicle_document_caption')){
                                    // Upload file to Firebase storage
                                    $vehicle_document_url = Firebase::uploadFile('vehicle_document', $user_id, $user->username, $file, $file_ext); 
                                    $vehicle_document[] = (object)[
                                        'vehicle_document_id' => Generator::getUUID(),
                                        'vehicle_document_url' => $vehicle_document_url,
                                        'vehicle_document_caption' => $request->vehicle_document_caption[$idx],
                                        'vehicle_document_type' => $file_ext === "pdf" ? "pdf" : "image"
                                    ];
                                } else {
                                    return response()->json([
                                        'status' => 'failed',
                                        'message' => Generator::getMessageTemplate("custom", "document caption can't be empty"),
                                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                                }
                            } catch (\Exception $e) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => Generator::getMessageTemplate("unknown_error", null),
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }
                        }
                    }
                }
                // Make null if array document empty
                if(count($vehicle_document) === 0){
                    $vehicle_document = null;
                }

                // Update vehicle by ID
                $rows = VehicleModel::updateVehicleById(['vehicle_document' => $vehicle_document], $id, $user_id);
                if($rows){
                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("create", "$this->module document"),
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
     *     path="/api/v1/vehicle/delete/{id}",
     *     summary="Soft Delete Vehicle By ID",
     *     description="This request is used to delete a vehicle based on the provided `ID`. This request interacts with the MySQL database, broadcast message with Telegram, has a protected routes, and audited activity (history).",
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

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Soft Delete vehicle by ID
            $rows = VehicleModel::softDeleteVehicleById($user_id,$id);
            if($rows > 0){
                // Get user data
                $user = UserModel::getSocial($user_id);
                // Get vehicle data
                $vehicle = VehicleModel::getVehicleByIdAndUserId($id,$user_id);
                if($user->telegram_user_id && $user->telegram_is_valid === 1){
                    // Check if user's Telegram ID is valid
                    if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                        $message = "Hello $user->username, your vehicle with name $vehicle->vehicle_name ($vehicle->vehicle_plate_number) data has been deleted. You can still recovered deleted vehicle before 30 days after deletion process";
                        // Send telegram message
                        $response = Telegram::sendMessage([
                            'chat_id' => $user->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        // Reset telegram from user account if not valid
                        UserModel::updateUserById(['telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                    }
                }

                // Create history
                HistoryModel::createHistory(['history_type' => 'Vehicle', 'history_context' => "deleted a vehicle called $vehicle->vehicle_name ($vehicle->vehicle_plate_number)"], $user_id);
                
                // Return success response
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
     *     path="/api/v1/vehicle/document/destroy/{vehicle_id}/{doc_id}",
     *     summary="Hard Delete Vehicle Document By ID",
     *     description="This request is used to permanently delete a vehicle document based on the provided `vehicle_id` and `doc_id`. This request interacts with the MySQL database, firebase storage, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Parameter(
     *         name="doc_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle Document ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle document deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle document deleted")
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
     *         description="vehicle document failed to deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="vehicle document not found")
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
    public function hardDeleteVehicleDocById(Request $request, $vehicle_id, $doc_id){
        try{
            $user_id = $request->user()->id;

            // Get vehicle by ID
            $vehicle = VehicleModel::getVehicleByIdAndUserId($vehicle_id,$user_id);
            if($vehicle){
                $vehicle_documents = $vehicle->vehicle_document;
                // Delete Firebase uploaded document
                foreach ($vehicle_documents as $dt) {
                    if ($dt['vehicle_document_id'] === $doc_id) {
                        // Delete failed if file not found (already gone)
                        if(!Firebase::deleteFile($dt['vehicle_document_url'])){
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("not_found", 'failed to delete inventory image'),
                            ], Response::HTTP_NOT_FOUND);
                        }
                        break;
                    }
                }
            
                // Remove item from vehicle document ID
                $vehicle_documents = array_filter($vehicle_documents, function ($dt) use ($doc_id) {
                    return $dt['vehicle_document_id'] !== $doc_id;
                });
                $vehicle_document = array_values($vehicle_documents);
                
                // Update vehicle by ID
                $rows = VehicleModel::updateVehicleById([
                    'vehicle_document' => count($vehicle_document) > 0 ? $vehicle_document : null,
                ], $vehicle_id, $user_id);

                if($rows > 0){
                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("delete", "$this->module document"),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", "$this->module document"),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", "$this->module document"),
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
     * @OA\PUT(
     *     path="/api/v1/vehicle/recover/{id}",
     *     summary="Recover Vehicle By ID",
     *     description="This request is used to recover deleted vehicle based on the provided `ID`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
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

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Update vehicle by ID
            $rows = VehicleModel::recoverVehicleById($user_id,$id);
            if($rows > 0){
                // Get vehicle by ID
                $vehicle = VehicleModel::getVehicleByIdAndUserId($id,$user_id);
                // Create history
                HistoryModel::createHistory(['history_type' => 'Vehicle', 'history_context' => "recovered a vehicle called $vehicle->vehicle_name ($vehicle->vehicle_plate_number)"], $user_id);

                // Return success response
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
     *     summary="Hard Delete Vehicle By ID",
     *     description="This request is used to permanently delete vehicle based on the provided `ID`. This request interacts with the MySQL database, firebase storage, has a protected routes, and audited activity (history).",
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
     *         description="vehicle permanently deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle permanently deleted")
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

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get vehicle data
            $vehicle = VehicleModel::getVehicleByIdAndUserId($id,$user_id);
            // Hard Delete vehicle by ID
            $rows = VehicleModel::hardDeleteVehicleById($user_id,$id);
            if($rows > 0){
                // Delete Firebase uploaded image
                if($vehicle->vehicle_img_url){
                    // Delete failed if file not found (already gone)
                    if(!Firebase::deleteFile($vehicle->vehicle_img_url)){
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("not_found", 'failed to delete vehicle image'),
                        ], Response::HTTP_NOT_FOUND);
                    }
                }

                // Hard Delete data related to vehicle module
                WashModel::hardDeleteByVehicleId($id);
                FuelModel::hardDeleteByVehicleId($id);
                InventoryModel::hardDeleteByVehicleId($id);
                ReminderModel::hardDeleteByVehicleId($id);
                ServiceModel::hardDeleteByVehicleId($id);
                TripModel::hardDeleteByVehicleId($id);

                // Get user data
                $user = UserModel::getSocial($user_id);
                $vehicle_plate_number_and_name = "$vehicle->vehicle_name ($vehicle->vehicle_plate_number)";

                // Check if user's Telegram ID is valid
                if($user->telegram_user_id && $user->telegram_is_valid === 1){
                    if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                        $message = "Hello $user->username, your vehicle $vehicle_plate_number_and_name is permanently deleted";
                        // Send telegram message
                        $response = Telegram::sendMessage([
                            'chat_id' => $user->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        // Reset telegram from user account if not valid
                        UserModel::updateUserById(['telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                    }
                }

                // Create history
                HistoryModel::createHistory(['history_type' => 'Vehicle', 'history_context' => "permanently deleted a vehicle called $vehicle_plate_number_and_name"], $user_id);

                // Return success response
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
