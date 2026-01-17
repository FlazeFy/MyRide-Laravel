<?php

namespace App\Http\Controllers\Api\ServiceApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Model
use App\Models\ServiceModel;
use App\Models\AdminModel;

// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "service";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/service",
     *     summary="Get All Service",
     *     description="This request is used to get all service history. This request interacts with the MySQL database, has a protected routes, and pagination.",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Service fetched successfully. Ordered in descending order by `remind_at` and `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="6f59235e-c398-8a83-2f95-3f1fbe95ca6e"),
     *                         @OA\Property(property="service_category", type="string", example="Routine"),
     *                         @OA\Property(property="service_price_total", type="integer", example=15000000),
     *                         @OA\Property(property="service_location", type="string", example="Honda Tandean"),
     *                         @OA\Property(property="service_note", type="string", example="Lorem ipsum"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-21 09:15:12"),
     *                         @OA\Property(property="remind_at", type="string", format="date-time", example="2025-01-20 09:15:12"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="B 1234 CD"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
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
     *         description="service failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="service not found")
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
    public function getAllService(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $paginate = $request->query('per_page_key') ?? 15;
            $search = $request->query("search",null);
            // This will get all service if vehicle_id not attached
            $vehicle_id = $request->query('vehicle_id') ?? null;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get all service
            $res = ServiceModel::getAllService($user_id, $vehicle_id, $paginate, $search);            
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
     *     path="/api/v1/service/vehicle/{vehicle_id}",
     *     summary="Get Service By Vehicle ID",
     *     description="This request is used to get all service history. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Vehicle ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service fetched successfully. Ordered in descending order by `remind_at` and `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service fetched"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="service_category", type="string", example="Routine"),
     *                      @OA\Property(property="service_note", type="string", example="Lorem ipsum"),
     *                      @OA\Property(property="service_price_total", type="integer", example=15000000),
     *                      @OA\Property(property="service_location", type="string", example="Honda Tandean"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                      @OA\Property(property="remind_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                  )
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
     *         description="service failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="service not found")
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
    public function getServiceByVehicle(Request $request,$vehicle_id)
    {
        try{
            $user_id = $request->user()->id;

            // Get service by vehicle ID
            $res = ServiceModel::getServiceByVehicle($user_id,$vehicle_id);
            if ($res && count($res) > 0) {
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
     *     path="/api/v1/service/spending",
     *     summary="Get All Service Spending",
     *     description="This request is used to get all service spending per vehicle. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Service fetched successfully. Ordered in descending order by `total`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service fetched"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="vehicle_plate_number", type="string", example="C 800 CN"),
     *                      @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                      @OA\Property(property="total", type="integer", example=35000000),
     *                  )
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
     *         description="service failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="service not found")
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
    public function getAllServiceSpending(Request $request){
        try{
            $user_id = $request->user()->id;

            // Get all service spending 
            $res = ServiceModel::getAllServiceSpending($user_id);
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
     *     path="/api/v1/service/next",
     *     summary="Get Next Service",
     *     description="This request is used to get the nearest service. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Service fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service fetched"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="service_note", type="string", example="service berkala"),
     *                     @OA\Property(property="service_category", type="string", example="Routine"),
     *                     @OA\Property(property="service_location", type="string", example="Honda Tandean"),
     *                     @OA\Property(property="service_price_total", type="integer", example=24000000),
     *                     @OA\Property(property="remind_at", type="string", example="2025-09-05 00:00:00"),
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
     *         description="service failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="service not found")
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
    public function getNextService(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            
            // Get next / upcoming service
            $res = ServiceModel::getNextService($user_id);
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
}
