<?php

namespace App\Http\Controllers\Api\DriverApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Model
use App\Models\DriverModel;
use App\Models\VehicleModel;
use App\Models\AdminModel;
// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "driver";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/driver",
     *     summary="Get All Driver",
     *     description="This request is used to get all driver. This request interacts with the MySQL database, has a protected routes, and a pagination.",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Driver fetched successfully. Ordered in ascending order by `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="6f59235e-c398-8a83-2f95-3f1fbe95ca6e"),
     *                         @OA\Property(property="username", type="string", example="jhondoe"),
     *                         @OA\Property(property="fullname", type="string", example="Jhon Doe"),
     *                         @OA\Property(property="telegram_user_id", type="string", example="123456789"),
     *                         @OA\Property(property="telegram_is_valid", type="integer", example=1),
     *                         @OA\Property(property="email", type="string", example="jhondoe@gmail.com"),
     *                         @OA\Property(property="phone", type="string", example="08123456780"),
     *                         @OA\Property(property="total_trip", type="integer", example=10),
     *                         @OA\Property(property="notes", type="string", example="Lorem ipsum"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-21 09:15:12"),
     *                     )
     *                 )
     *             )
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
     *         description="driver failed to fetched",
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
    public function getAllDriver(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $paginate = $request->query('per_page_key') ?? 12;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get all driver with pagination
            $res = DriverModel::getAllDriver($user_id, $paginate);
            if (count($res) > 0) {
                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => $res
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
     * @OA\GET(
     *     path="/api/v1/driver/name",
     *     summary="Get All Driver Name",
     *     description="This request is used to get all driver name. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Driver fetched successfully. Ordered in ascending order by `fullname`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver fetched"),
     *             @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="6f59235e-c398-8a83-2f95-3f1fbe95ca6e"),
     *                         @OA\Property(property="username", type="string", example="jhondoe"),
     *                         @OA\Property(property="fullname", type="string", example="Jhon Doe"),
     *                     )
     *             )
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
     *         description="driver failed to fetched",
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
    public function getAllDriverName(Request $request)
    {
        try{
            $user_id = $request->user()->id;

            // Get all driver name
            $res = DriverModel::getAllDriverName($user_id);            
            if (count($res) > 0) {
                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => $res
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
     * @OA\GET(
     *     path="/api/v1/driver/vehicle",
     *     summary="Get All Driver - Vehicle Relation",
     *     description="This request is used to get all driver with his assigned vehicle. This request interacts with the MySQL database, has a protected routes, and a pagination.",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Driver fetched successfully. Ordered in descending order by driver relation's `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="username", type="string", example="jhondoe"),
     *                         @OA\Property(property="fullname", type="string", example="Jhon Doe"),
     *                         @OA\Property(property="email", type="string", example="jhondoe@gmail.com"),
     *                         @OA\Property(property="phone", type="string", example="08123456780"),
     *                         @OA\Property(property="telegram_user_id", type="string", example="123456789"),
     *                         @OA\Property(property="telegram_is_valid", type="integer", example=1),
     *                         @OA\Property(property="vehicle_list", type="string", example="D 1610 ZBC - Brio RS MT, D 4110 ADC - Toyota Innova"),
     *                     )
     *                 )
     *             )
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
     *         description="driver failed to fetched",
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
    public function getDriverVehicle(Request $request){
        try{
            $user_id = $request->user()->id;
            $paginate = $request->query('per_page_key') ?? 12;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get all driver - vehicle relation with pagination
            $res = DriverModel::getDriverVehicle($user_id, $paginate);            
            if (count($res) > 0) {
                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => $res
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
     * @OA\GET(
     *     path="/api/v1/driver/vehicle/list",
     *     summary="Get All Driver - Vehicle Relation For Management / Assigning",
     *     description="This request is used to get all driver with their assigned vehicle for management / assigning. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Driver"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Driver and vehicle fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="driver fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="vehicle", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="8585ab36-fc67-139c-35c6-608931244634"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Non dolorum velit corrupti"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="C 80 OON")
     *                     )
     *                 ),
     *                 @OA\Property(property="driver", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="a3f743ae-a373-11f0-86ad-3216422910e8"),
     *                         @OA\Property(property="username", type="string", example="tester_jhona"),
     *                         @OA\Property(property="fullname", type="string", example="asd")
     *                     )
     *                 ),
     *                 @OA\Property(property="assigned", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="1c8a4d88-d9b0-11ed-afa1-0242ac120002"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="C 80 OON"),
     *                         @OA\Property(property="vehicle_id", type="string", example="8585ab36-fc67-139c-35c6-608931244634"),
     *                         @OA\Property(property="driver_id", type="string", example="a3f743ae-a373-11f0-86ad-3216422910e8"),
     *                         @OA\Property(property="username", type="string", example="tester_jhona"),
     *                         @OA\Property(property="fullname", type="string", example="asd")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Protected route — requires sign-in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Driver or vehicle not found",
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
     *             @OA\Property(property="message", type="string", example="something went wrong. please contact admin")
     *         )
     *     )
     * )
     */
    public function getDriverVehicleManageList(Request $request){
        try{
            $user_id = $request->user()->id;

            // Get all vehicle name
            $res_vehicle = VehicleModel::getAllVehicleName($user_id);
            if (count($res_vehicle) > 0) {
                // Get all driver and define fetched column
                $res_driver = DriverModel::getAllDriver($user_id, 0, 'id,username,fullname');
                // Get all assigned driver
                $res_assigned = DriverModel::getDriverVehicleManageList($user_id);

                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => [
                        'vehicle' => $res_vehicle,
                        'driver' => $res_driver,
                        'assigned' => count($res_assigned) > 0 ? $res_assigned : null,
                    ]
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
