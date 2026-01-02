<?php

namespace App\Http\Controllers\Api\DriverApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;

// Model
use App\Models\DriverModel;
use App\Models\AdminModel;
use App\Models\VehicleModel;
use App\Models\UserModel;
use App\Models\HistoryModel;
use App\Models\DriverVehicleRelationModel;
// Helper
use App\Helpers\Generator;
use App\Helpers\Validation;
use App\Helpers\TelegramMessage;

class Commands extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "driver";
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/driver/destroy/{id}",
     *     summary="Hard Delete Driver By ID",
     *     description="This request is used to permanently delete a driver entry based on the provided `ID`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Driver ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="driver permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver permentally deleted")
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
     *         description="driver failed to permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="driver not found")
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
    public function hardDeleteDriverById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get driver's contect
            $driver = DriverModel::getDriverContact($id);

            // Permanently delete driver
            $rows = DriverModel::hardDeleteDriverById($id, $user_id);
            if($rows > 0){
                // Permanently delete driver relation with vehicle
                DriverVehicleRelationModel::hardDeleteDriverVehicleRelationByDriverId($id, $user_id);
                
                // Create history
                HistoryModel::createHistory(['history_type' => 'Driver', 'history_context' => "deleted $driver->username as a driver"], $user_id);

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

    /**
     * @OA\DELETE(
     *     path="/api/v1/driver/destroy/relation/{id}",
     *     summary="Hard Delete Driver Relation With Vehicle By ID",
     *     description="This request is used to permanently delete a driver relation with vehicle entry based on the provided driver relation `ID`. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Driver Relation ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="driver relation permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver relation permentally deleted")
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
     *         description="driver relation failed to permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="driver relation not found")
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
    public function hardDeleteDriverRelationById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Permanently delete driver relation with vehicle
            $rows = DriverVehicleRelationModel::hardDeleteDriverVehicleRelationById($id, $user_id);
            if($rows > 0){
                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("permentally delete", "$this->module relation"),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", "$this->module relation"),
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
     *     path="/api/v1/driver",
     *     summary="Post Create Driver",
     *     description="This request is used to create a driver by using given `username`, `fullname`, `password`, `telegram_user_id`, `email`, `phone`, and `notes`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username","fullname","email","password","password_confirmation","phone"},
     *              @OA\Property(property="username", type="string", example="tester_01"),
     *              @OA\Property(property="fullname", type="string", example="Tester User"),
     *              @OA\Property(property="email", type="string", example="tester@mail.com"),
     *              @OA\Property(property="phone", type="string", example="08123456789"),
     *              @OA\Property(property="notes", type="string", example="Lorem ipsum"),
     *              @OA\Property(property="telegram_user_id", type="string", example="1317625977"),
     *              @OA\Property(property="password", type="string", example="nopass123"),
     *              @OA\Property(property="password_confirmation", type="string", example="nopass123")
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="driver created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver created")
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
     *         response=400,
     *         description="driver failed to validated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="[failed validation message]")
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
    public function postCreateDriver(Request $request){
        try{
            // Validate request body
            $validator = Validation::getValidateDriver($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user_id = $request->user()->id;

                // Check if driver username is available
                $check = DriverModel::getDriverByUsernameOrEmail($request->username,$request->email,null);
                if(!$check){
                    // Create driver
                    $data = [
                        'username' => $request->username, 
                        'fullname' => $request->fullname, 
                        'password' => Hash::make($request->password), 
                        'email' => $request->email, 
                        'telegram_user_id' => $request->telegram_user_id, 
                        'telegram_is_valid' => 0, 
                        'phone' => $request->phone, 
                        'notes' => $request->notes,
                    ];
                    $row = DriverModel::createDriver($data, $user_id);
                    if($row){
                        // Create history
                        HistoryModel::createHistory(['history_type' => 'Driver', 'history_context' => "added $request->username as a driver"], $user_id);

                        // Return success response
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("create", $this->module),
                        ], Response::HTTP_CREATED);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("conflict", $this->module),
                    ], Response::HTTP_CONFLICT);
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
     *     path="/api/v1/driver/{id}",
     *     summary="Put Update Driver",
     *     description="This request is used to update a driver by using given `username`, `fullname`, `email`, `phone`, and `notes`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="driver updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver updated")
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
     *         response=400,
     *         description="driver failed to validated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="[failed validation message]")
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
    public function putUpdateDriverById(Request $request, $id){
        try{
            // Validate request body
            $validator = Validation::getValidateDriver($request,'update');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user_id = $request->user()->id;

                // Check if driver username is available
                $check = DriverModel::getDriverByUsernameOrEmail($request->username,$request->email,$id);   
                if(!$check){
                    // Update driver
                    $data = [
                        'username' => $request->username, 
                        'fullname' => $request->fullname, 
                        'email' => $request->email, 
                        'phone' => $request->phone, 
                        'notes' => $request->notes,
                    ];
                    $rows = DriverModel::updateDriverById($data, $user_id, $id);
                    if($rows > 0){
                        // Create history
                        HistoryModel::createHistory(['history_type' => 'Driver', 'history_context' => "updated $request->username driver's data"], $user_id);

                        // Return success response
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("update", $this->module),
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("conflict", $this->module),
                    ], Response::HTTP_CONFLICT);
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
     *     path="/api/v1/driver/vehicle",
     *     summary="Post Create Driver Vehicle Relation",
     *     description="This request is used to create a driver - vehicle relation by using given `vehicle_id`, `driver_id`, and `relation_note`. This request interacts with the MySQL database, has a protected routes, broadcast message with Telegram, and audited activity (history).",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"driver_id","vehicle_id"},
     *              @OA\Property(property="driver_id", type="string", example="830b1ba4-3e90-28d4-1f0b-aadcd406090f"),
     *              @OA\Property(property="vehicle_id", type="string", example="6c1ff866-ce85-fa03-21ce-b30905b43b1a"),
     *              @OA\Property(property="relation_note", type="string", example="Driver weekday")
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="driver relation created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver assigned")
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
     *         response=400,
     *         description="driver failed to validated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="[failed validation message]")
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
    public function postCreateDriverVehicle(Request $request){
        try{
            // Validate request body
            $validator = Validation::getValidateDriver($request,'create_relation');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Check if driver relation with vehicle already exist
                $check = DriverVehicleRelationModel::getRelationByVehicleAndDriver($request->vehicle_id,$request->driver_id);
                if(!$check){
                    // Create driver relation with vehicle
                    $data = [
                        'vehicle_id' => $request->vehicle_id, 
                        'driver_id' => $request->driver_id, 
                        'relation_note' => $request->relation_note,
                    ];
                    $row = DriverVehicleRelationModel::createDriverVehicleRelation($data);
                    if($row){
                        // Get driver contact to broadcast and check if its has valid telegram
                        $driver = DriverModel::getDriverContact($request->driver_id);
                        if($driver->telegram_user_id && $driver->telegram_is_valid === 1){
                            $user_id = $request->user()->id;
                            // Validate telegram id
                            if(TelegramMessage::checkTelegramID($driver->telegram_user_id)){
                                // Get username to put in message
                                $user = UserModel::getSocial($user_id);
                                // Get vehicle plate number to put in message
                                $vehicle = VehicleModel::getVehicleDetailById(null,$request->vehicle_id);
                                
                                // Send telegram message
                                $message = "Hello $driver->username, you have been assigned by $user->username to become the driver of '$vehicle->vehicle_plate_number'";
                                $response = Telegram::sendMessage([
                                    'chat_id' => $driver->telegram_user_id,
                                    'text' => $message,
                                    'parse_mode' => 'HTML'
                                ]);
                            } else {
                                // Reset telegram from user account if not valid
                                $res = UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                            }
                        }

                        // Return success response
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("create", "$this->module relation"),
                        ], Response::HTTP_CREATED);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("conflict", $this->module),
                    ], Response::HTTP_CONFLICT);
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
