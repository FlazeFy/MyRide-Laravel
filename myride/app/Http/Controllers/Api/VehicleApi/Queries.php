<?php

namespace App\Http\Controllers\Api\VehicleApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\VehicleModel;
use App\Models\TripModel;
use App\Models\WashModel;
use App\Models\DriverModel;
// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "vehicle";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/vehicle/header",
     *     summary="Get All Vehicle",
     *     description="This request is used to get all vehicle (header format). This request interacts with the MySQL database, has a protected routes, and a pagination.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle fetched successfully. Ordered in descending order by `updated_at` and `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                         @OA\Property(property="vehicle_desc", type="string", example="snowy"),
     *                         @OA\Property(property="vehicle_merk", type="string", example="Honda"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                         @OA\Property(property="vehicle_transmission", type="string", example="Manual"),
     *                         @OA\Property(property="vehicle_distance", type="integer", example=45000),
     *                         @OA\Property(property="vehicle_category", type="string", example="Personal Car"),
     *                         @OA\Property(property="vehicle_status", type="string", example="Available"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                         @OA\Property(property="vehicle_fuel_status", type="string", example="Normal"),
     *                         @OA\Property(property="vehicle_default_fuel", type="string", example="Shell Super"),
     *                         @OA\Property(property="vehicle_color", type="string", example="White"),
     *                         @OA\Property(property="vehicle_capacity", type="integer", example=5),
     *                         @OA\Property(property="vehicle_img_url", type="string", nullable=true, example=null),
     *                         @OA\Property(property="updated_at", type="string", format="datetime", example="2025-01-29 15:04:18")
     *                     )
     *                 ),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=14),
     *                 @OA\Property(property="total", type="integer", example=1)
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
     *     ),
     * )
     */
    public function getAllVehicleHeader(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Get all vehicle header
            $res = VehicleModel::getAllVehicleHeader($user_id,$limit);
            if ($res) {
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
     *     path="/api/v1/vehicle/readiness",
     *     summary="Get Vehicle Readiness",
     *     description="This request is used to get all vehicle that ready to drive. This request interacts with the MySQL database, has a protected routes, and a pagination.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched successfully. Ordered in descending order by `readiness` and `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                         @OA\Property(property="vehicle_status", type="string", example="Available"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                         @OA\Property(property="vehicle_fuel_status", type="string", example="Normal"),
     *                         @OA\Property(property="vehicle_capacity", type="integer", example=5),
     *                         @OA\Property(property="vehicle_transmission", type="string", example="CVT"),
     *                         @OA\Property(property="vehicle_readiness", type="integer", example=8),
     *                         @OA\Property(property="deleted_at", type="string", format="datetime", example="2025-01-29 15:04:18")
     *                     )
     *                 ),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=14),
     *                 @OA\Property(property="total", type="integer", example=1)
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
     *     ),
     * )
     */
    public function getVehicleReadiness(Request $request)
    {
        try{
            $limit = $request->query("limit",14);

            // Check whether authentication is attached. If yes, retrieve vehicle by user; if not, retrieve all vehicle for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }

            // Get vehicle readiness status
            $res = VehicleModel::getVehicleReadiness($user_id,$limit);
            if ($res) {
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
     *     path="/api/v1/vehicle/name",
     *     summary="Get All Vehicle Name",
     *     description="This request is used to get all vehicle name. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Vehicle fetched successfully. Ordered in ascending order by `deleted_at` and descending by `vehicle_name` and `vehicle_plate_number`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                     @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                     @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                     @OA\Property(property="deleted_at", type="string", format="datetime", nullable=true, example="2025-01-29 15:04:18"),
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
     *     ),
     * )
     */
    public function getAllVehicleName(Request $request){
        try{
            $user_id = $request->user()->id;

            // Get all vehicle name
            $res = VehicleModel::getAllVehicleName($user_id);
            if (count($res) > 0) {
                // Return success respone
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
     *     path="/api/v1/vehicle/fuel",
     *     summary="Get All Vehicle Fuel",
     *     description="This request is used to get all vehicle fuel status. This request interacts with the MySQL database, and has a protected routes",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched successfully. Ordered in format ('Empty', 'Low', 'Normal', 'High', 'Full', 'Not Monitored') for column `vehicle_fuel_status`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                     @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                     @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                     @OA\Property(property="vehicle_fuel_status", type="string", example="Normal"),
     *                     @OA\Property(property="vehicle_fuel_capacity", type="integer", example=40),
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
     *     ),
     * )
     */
    public function getAllVehicleFuel(Request $request){
        try{
            $user_id = $request->user()->id;

            // Get all vehicle fuel
            $res = VehicleModel::getAllVehicleFuel($user_id);
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
     *     path="/api/v1/vehicle/detail/{id}",
     *     summary="Get Vehicle Detail By ID",
     *     description="This request is used to get vehicle detail by `id`. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="4f33d5e4-de9f-11ed-b5ea-0242ac120002"
     *         ),
     *         description="Vehicle ID",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="4f33d5e4-de9f-11ed-b5ea-0242ac120002"),
     *                 @OA\Property(property="vehicle_name", type="string", example="Kijang Innova 2.0 Type G MT"),
     *                 @OA\Property(property="vehicle_merk", type="string", example="Toyota"),
     *                 @OA\Property(property="vehicle_type", type="string", example="Minibus"),
     *                 @OA\Property(property="vehicle_price", type="integer", example=275000000),
     *                 @OA\Property(property="vehicle_desc", type="string", example="sudah jarang digunakan 2"),
     *                 @OA\Property(property="vehicle_distance", type="integer", example=90000),
     *                 @OA\Property(property="vehicle_category", type="string", example="Parents Car"),
     *                 @OA\Property(property="vehicle_status", type="string", example="Available"),
     *                 @OA\Property(property="vehicle_year_made", type="integer", example=2011),
     *                 @OA\Property(property="vehicle_plate_number", type="string", example="PA 1060 VZ"),
     *                 @OA\Property(property="vehicle_fuel_status", type="string", example="Not Monitored"),
     *                 @OA\Property(property="vehicle_fuel_capacity", type="integer", example=50),
     *                 @OA\Property(property="vehicle_default_fuel", type="string", example="Pertamina Pertalite"),
     *                 @OA\Property(property="vehicle_color", type="string", example="White"),
     *                 @OA\Property(property="vehicle_transmission", type="string", example="Manual"),
     *                 @OA\Property(property="vehicle_img_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="vehicle_other_img_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="vehicle_capacity", type="integer", example=8),
     *                 @OA\Property(property="vehicle_document", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="fL9eDk"),
     *                         @OA\Property(property="attach_type", type="string", example="attachment_image"),
     *                         @OA\Property(property="attach_name", type="string", example="ini gambar"),
     *                         @OA\Property(property="attach_url", type="string", format="uri", example="https://firebasestorage.googleapis.com/...")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="datetime", example="2024-03-27 04:03:34"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime", example="2025-07-08 02:13:45"),
     *                 @OA\Property(property="deleted_at", type="string", nullable=true, example=null)
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
     *     ),
     * )
     */
    public function getVehicleDetailById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Get vehicle detail
            $res = VehicleModel::getVehicleDetailById($user_id,$id);
            if ($res) {
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
     *     path="/api/v1/vehicle/detail/full/{id}",
     *     summary="Get Vehicle Full Detail By ID",
     *     description="This request is used to get vehicle detail by `id`, it comes with Wash and Trip History. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="4f33d5e4-de9f-11ed-b5ea-0242ac120002"
     *         ),
     *         description="Vehicle ID",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="detail", type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                     @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                     @OA\Property(property="vehicle_merk", type="string", example="Honda"),
     *                     @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                     @OA\Property(property="vehicle_price", type="integer", example=188000000),
     *                     @OA\Property(property="vehicle_desc", type="string", example="snowy"),
     *                     @OA\Property(property="vehicle_distance", type="integer", example=45000),
     *                     @OA\Property(property="vehicle_category", type="string", example="Personal Car"),
     *                     @OA\Property(property="vehicle_status", type="string", example="Available"),
     *                     @OA\Property(property="vehicle_year_made", type="integer", example=2020),
     *                     @OA\Property(property="vehicle_plate_number", type="string", example="D 1060 ZBC"),
     *                     @OA\Property(property="vehicle_fuel_status", type="string", example="Normal"),
     *                     @OA\Property(property="vehicle_fuel_capacity", type="integer", example=35),
     *                     @OA\Property(property="vehicle_default_fuel", type="string", example="Shell Super"),
     *                     @OA\Property(property="vehicle_color", type="string", example="White"),
     *                     @OA\Property(property="vehicle_transmission", type="string", example="CVT"),
     *                     @OA\Property(property="vehicle_img_url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="vehicle_other_img_url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="vehicle_capacity", type="integer", example=5),
     *                     @OA\Property(property="vehicle_document", type="string", nullable=true, example=null),
     *                     @OA\Property(property="created_at", type="string", format="datetime", example="2024-03-27 04:03:34"),
     *                     @OA\Property(property="updated_at", type="string", format="datetime", example="2025-01-29 15:04:18"),
     *                     @OA\Property(property="deleted_at", type="string", nullable=true, example=null)
     *                 ),
     *                 @OA\Property(property="trip", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="data", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="id", type="string", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                             @OA\Property(property="trip_desc", type="string", example="jalan2"),
     *                             @OA\Property(property="trip_category", type="string", example="Others"),
     *                             @OA\Property(property="trip_person", type="string", nullable=true, example="budi"),
     *                             @OA\Property(property="trip_origin_name", type="string", example="Place A"),
     *                             @OA\Property(property="trip_origin_coordinate", type="string", example="-6.226828716225759, 106.82152290589822"),
     *                             @OA\Property(property="trip_destination_name", type="string", example="Place B"),
     *                             @OA\Property(property="trip_destination_coordinate", type="string", example="-6.230792280916382, 106.81781530380249"),
     *                             @OA\Property(property="created_at", type="string", format="datetime", example="2025-01-29 16:46:45")
     *                         )
     *                     ),
     *                     @OA\Property(property="last_page", type="integer", example=1),
     *                     @OA\Property(property="per_page", type="integer", example=14),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                 ),
     *                 @OA\Property(property="driver", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="id", type="string", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                             @OA\Property(property="username", type="string", example="jhondoe"),
     *                             @OA\Property(property="fullname", type="string", example="test123"),
     *                             @OA\Property(property="email", type="string", example="jhondoe@gmail.com"),
     *                             @OA\Property(property="telegram_user_id", type="string", example="123456789"),
     *                             @OA\Property(property="telegram_is_valid", type="integer", example=1),
     *                             @OA\Property(property="phone", type="string", example="08123456789"),
     *                             @OA\Property(property="notes", type="string", nullable=true, example="lorem ipsum"),
     *                             @OA\Property(property="assigned_at", type="string", format="datetime", example="2025-01-29 16:46:45")
     *                         )
     *                 ),
     *                 @OA\Property(property="wash", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="data", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="id", type="string", example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"),
     *                             @OA\Property(property="wash_desc", type="string", example="Cuci mobil"),
     *                             @OA\Property(property="wash_by", type="string", example="Carwash"),
     *                             @OA\Property(property="is_wash_body", type="integer", example=1),
     *                             @OA\Property(property="is_wash_window", type="integer", example=1),
     *                             @OA\Property(property="is_wash_dashboard", type="integer", example=1),
     *                             @OA\Property(property="is_wash_tires", type="integer", example=1),
     *                             @OA\Property(property="is_wash_trash", type="integer", example=1),
     *                             @OA\Property(property="is_wash_engine", type="integer", example=1),
     *                             @OA\Property(property="is_wash_seat", type="integer", example=1),
     *                             @OA\Property(property="is_wash_carpet", type="integer", example=1),
     *                             @OA\Property(property="is_wash_pillows", type="integer", example=0),
     *                             @OA\Property(property="wash_address", type="string", example="AutoService Jl. Kapten Tandean"),
     *                             @OA\Property(property="wash_start_time", type="string", format="datetime", example="2024-02-29 21:30:00"),
     *                             @OA\Property(property="wash_end_time", type="string", format="datetime", example="2024-02-29 22:20:00"),
     *                             @OA\Property(property="is_fill_window_washing_water", type="integer", example=0),
     *                             @OA\Property(property="is_wash_hollow", type="integer", example=1),
     *                             @OA\Property(property="created_at", type="string", format="datetime", example="2024-03-27 12:33:05"),
     *                             @OA\Property(property="updated_at", type="string", nullable=true, example=null)
     *                         )
     *                     ),
     *                     @OA\Property(property="last_page", type="integer", example=1),
     *                     @OA\Property(property="per_page", type="integer", example=14),
     *                     @OA\Property(property="total", type="integer", example=1)
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
     *     ),
     * )
     */
    public function getVehicleFullDetailById(Request $request, $id){
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Get vehicle detail
            $res = VehicleModel::getVehicleDetailById($user_id,$id);
            if ($res) {
                // Get trip history by vehicle ID
                $res_trip = TripModel::getTripByVehicleId($user_id,$id,$limit);
                // Get wash history by vehicle ID
                $res_wash = WashModel::getWashByVehicleId($user_id,$id,$limit);
                // Get driver by its vehicle ID
                $res_driver = DriverModel::getDriverByVehicleId($user_id, $id);

                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => [
                        'detail' => $res,
                        'trip' => $res_trip,
                        'wash' => $res_wash && count($res_wash) > 0 ? $res_wash : null,
                        'driver' => $res_driver && count($res_driver) > 0 ? $res_driver : null
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

    /**
     * @OA\GET(
     *     path="/api/v1/vehicle/trip/summary/{id}",
     *     summary="Get Vehicle Trip Summary By ID",
     *     description="This request is used to get vehicle trip history summary by vehicle's `id`. This request interacts with the MySQL database, and has a protected routes",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="4f33d5e4-de9f-11ed-b5ea-0242ac120002"
     *         ),
     *         description="Vehicle ID",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="detail", type="object",
     *                     @OA\Property(property="most_person_with", type="string", example="budi"),
     *                     @OA\Property(property="vehicle_total_trip_distance", type="decimal", example=30.46),
     *                     @OA\Property(property="most_origin", type="string", example="Place A"),
     *                     @OA\Property(property="most_destination", type="string", example="Place B"),
     *                     @OA\Property(property="most_category", type="string", example="Office")
     *                 ),
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
     *     ),
     * )
     */
    public function getVehicleTripSummaryById(Request $request, $id){
        try{
            $user_id = $request->user()->id;

            // Get person name that most travelled with
            $res_most_person_with = TripModel::getPersonWithMostTripWith($user_id,$id);
            // Get trip total stats
            $res_most_context_total_trip = TripModel::getMostContext($user_id,$id);
            $res_vehicle_total_trip_distance = TripModel::getTotalTripDistance($user_id,$id);
            $res_most_origin = $res_most_context_total_trip->most_origin;
            $res_most_destination = $res_most_context_total_trip->most_destination;
            $res_most_category = $res_most_context_total_trip->most_category;
            
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => Generator::getMessageTemplate("fetch", $this->module),
                'data' => [
                    'most_person_with' => $res_most_person_with ? $res_most_person_with[0]->context : null,
                    'vehicle_total_trip_distance' => round($res_vehicle_total_trip_distance,2),
                    'most_origin' => $res_most_origin,
                    'most_destination' => $res_most_destination,
                    'most_category' => $res_most_category,
                ]
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
