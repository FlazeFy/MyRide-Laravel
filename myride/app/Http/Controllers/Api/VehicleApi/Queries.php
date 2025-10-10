<?php

namespace App\Http\Controllers\Api\VehicleApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Models
use App\Models\VehicleModel;
use App\Models\TripModel;
use App\Models\CleanModel;
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
     *     summary="Get all vehicle",
     *     description="This request is used to get all vehicle with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                         @OA\Property(property="vehicle_desc", type="string", example="snowy"),
     *                         @OA\Property(property="vehicle_merk", type="string", example="Honda"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
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

            // Model
            $res = VehicleModel::getAllVehicleHeader($user_id,$limit);

            // Response
            if ($res) {
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
     *     summary="Get vehicle readiness",
     *     description="This request is used to get all vehicle that ready to drive with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
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
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model
            $res = VehicleModel::getVehicleReadiness($user_id,$limit);

            // Response
            if ($res) {
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
     *     summary="Get all vehicle name",
     *     description="This request is used to get all vehicle name. This request is using MySql database, and have a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="2d98f524-de02-11ed-b5ea-0242ac120002"),
     *                     @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                     @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
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

            // Model
            $res = VehicleModel::getAllVehicleName($user_id);

            // Response
            if (count($res) > 0) {
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
     *     summary="Get all vehicle fuel",
     *     description="This request is used to get all vehicle fuel status. This request is using MySql database, and have a protected routes.",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
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

            // Model
            $res = VehicleModel::getAllVehicleFuel($user_id);

            // Response
            if (count($res) > 0) {
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
     *     summary="Get vehicle detail by id",
     *     description="This request is used to get vehicle detail by id. This request is using MySql database, and have a protected routes.",
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
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
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

            // Model
            $res = VehicleModel::getVehicleDetailById($user_id,$id);

            // Response
            if ($res) {
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
     *     summary="Get vehicle full detail by id",
     *     description="This request is used to get vehicle detail by id, it comes with Clean and Trip History. This request is using MySql database, and have a protected routes.",
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
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
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
     *                 @OA\Property(property="clean", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="data", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="id", type="string", example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"),
     *                             @OA\Property(property="clean_desc", type="string", example="Cuci mobil"),
     *                             @OA\Property(property="clean_by", type="string", example="Carwash"),
     *                             @OA\Property(property="clean_tools", type="string", nullable=true, example=null),
     *                             @OA\Property(property="is_clean_body", type="integer", example=1),
     *                             @OA\Property(property="is_clean_window", type="integer", example=1),
     *                             @OA\Property(property="is_clean_dashboard", type="integer", example=1),
     *                             @OA\Property(property="is_clean_tires", type="integer", example=1),
     *                             @OA\Property(property="is_clean_trash", type="integer", example=1),
     *                             @OA\Property(property="is_clean_engine", type="integer", example=1),
     *                             @OA\Property(property="is_clean_seat", type="integer", example=1),
     *                             @OA\Property(property="is_clean_carpet", type="integer", example=1),
     *                             @OA\Property(property="is_clean_pillows", type="integer", example=0),
     *                             @OA\Property(property="clean_address", type="string", example="AutoService Jl. Kapten Tandean"),
     *                             @OA\Property(property="clean_start_time", type="string", format="datetime", example="2024-02-29 21:30:00"),
     *                             @OA\Property(property="clean_end_time", type="string", format="datetime", example="2024-02-29 22:20:00"),
     *                             @OA\Property(property="is_fill_window_cleaning_water", type="integer", example=0),
     *                             @OA\Property(property="is_clean_hollow", type="integer", example=1),
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

            // Model : Show Detail
            $res = VehicleModel::getVehicleDetailById($user_id,$id);

            // Response
            if ($res) {
                // Model : Show Trip History
                $res_trip = TripModel::getTripByVehicleId($user_id,$id,$limit);
                // Model : Show Clean History
                $res_clean = CleanModel::getCleanByVehicleId($user_id,$id,$limit);
                // Model : Show Driver
                $res_driver = DriverModel::getDriverByVehicleId($user_id, $id);
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => [
                        'detail' => $res,
                        'trip' => $res_trip,
                        'clean' => $res_clean,
                        'driver' => $res_driver
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
     *     summary="Get vehicle trip summary by id",
     *     description="This request is used to get vehicle trip history summary by id. This request is using MySql database, and have a protected routes.",
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
     *         description="vehicle fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="vehicle fetched"),
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

            // Model : Show Most Person Trip With
            $res_most_person_with = TripModel::getMostPersonTripWith($user_id,$id);

            // Model : Show Vehicle Trip Most Origin, Destination, and Category
            $res_most_context_total_trip = TripModel::getMostContext($user_id,$id);
            $res_vehicle_total_trip_distance = TripModel::getTotalTripDistance($user_id,$id);
            $res_most_origin = $res_most_context_total_trip->most_origin;
            $res_most_destination = $res_most_context_total_trip->most_destination;
            $res_most_category = $res_most_context_total_trip->most_category;
            
            return response()->json([
                'status' => 'success',
                'message' => Generator::getMessageTemplate("fetch", $this->module),
                'data' => [
                    'most_person_with' => $res_most_person_with ? $res_most_person_with[0]->context : null,
                    'vehicle_total_trip_distance' => $res_vehicle_total_trip_distance,
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
