<?php

namespace App\Http\Controllers\Api\WashApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

//  Models
use App\Models\WashModel;
// Helpers
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "wash history";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/wash",
     *     summary="Get all wash history",
     *     description="This request is used to get all wash history with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="wash fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Honda - Brio RS MT"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZRB"),
     *                         @OA\Property(property="wash_desc", type="string", example="Cuci mobil"),
     *                         @OA\Property(property="wash_by", type="string", example="Carwash"),
     *                         @OA\Property(property="wash_tools", type="string", nullable=true, example=null),
     *                         @OA\Property(property="is_wash_body", type="integer", example=1),
     *                         @OA\Property(property="is_wash_window", type="integer", example=1),
     *                         @OA\Property(property="is_wash_dashboard", type="integer", example=1),
     *                         @OA\Property(property="is_wash_tires", type="integer", example=1),
     *                         @OA\Property(property="is_wash_trash", type="integer", example=1),
     *                         @OA\Property(property="is_wash_engine", type="integer", example=1),
     *                         @OA\Property(property="is_wash_seat", type="integer", example=1),
     *                         @OA\Property(property="is_wash_carpet", type="integer", example=1),
     *                         @OA\Property(property="is_wash_pillows", type="integer", example=0),
     *                         @OA\Property(property="wash_address", type="string", example="AutoService Jl. Kapten Tandean"),
     *                         @OA\Property(property="wash_start_time", type="string", format="datetime", example="2024-02-29 21:30:00"),
     *                         @OA\Property(property="wash_end_time", type="string", format="datetime", example="2024-02-29 22:20:00"),
     *                         @OA\Property(property="is_fill_window_washing_water", type="integer", example=0),
     *                         @OA\Property(property="is_wash_hollow", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="datetime", example="2024-03-27 12:33:05"),
     *                         @OA\Property(property="updated_at", type="string", nullable=true, example=null)
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
     *         description="wash failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="wash history not found")
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
    public function getAllWashHistory(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model 
            $res = WashModel::getAllWashHistory($user_id,$limit);

            // Response
            if($res && count($res) > 0) {
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
     *     path="/api/v1/wash/last",
     *     summary="Get last wash",
     *     description="This request is used to get last wash history by vehicle id. This request is using MySql database, and have a protected routes.",
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="wash fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash fetched"),
     *             @OA\Property(property="data", type="object",
     *                         @OA\Property(property="wash_desc", type="string", example="Cuci mobil"),
     *                         @OA\Property(property="wash_by", type="string", example="Carwash"),
     *                         @OA\Property(property="is_wash_body", type="integer", example=1),
     *                         @OA\Property(property="is_wash_window", type="integer", example=1),
     *                         @OA\Property(property="is_wash_dashboard", type="integer", example=1),
     *                         @OA\Property(property="is_wash_tires", type="integer", example=1),
     *                         @OA\Property(property="is_wash_trash", type="integer", example=1),
     *                         @OA\Property(property="is_wash_engine", type="integer", example=1),
     *                         @OA\Property(property="is_wash_seat", type="integer", example=1),
     *                         @OA\Property(property="is_wash_carpet", type="integer", example=1),
     *                         @OA\Property(property="is_wash_pillows", type="integer", example=0),
     *                         @OA\Property(property="wash_address", type="string", example="AutoService Jl. Kapten Tandean"),
     *                         @OA\Property(property="is_fill_window_washing_water", type="integer", example=0),
     *                         @OA\Property(property="is_wash_hollow", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="datetime", example="2024-03-27 12:33:05"),
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
     *         description="wash failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="wash history not found")
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
    public function getLastWashByVehicleId(Request $request){
        try{
            $user_id = $request->user()->id;
            $vehicle_id = $request->query('vehicle_id') ?? null;

            // Model 
            $res = WashModel::getLastWashByVehicleId($user_id,$vehicle_id);

            // Response
            if($res) {
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
     *     path="/api/v1/wash/summary",
     *     summary="Get wash summary",
     *     description="This request is used to get wash summary by vehicle id or all vehicle. This request is using MySql database, and have a protected routes.",
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="wash fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash fetched"),
     *             @OA\Property(property="data", type="array",
     *                    @OA\Items(
     *                         @OA\Property(property="vehicle_name", type="string", example="Honda - Brio RS MT"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZRB"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                         @OA\Property(property="total_wash", type="integer", example=5),
     *                         @OA\Property(property="is_wash_body", type="integer", example=5),
     *                         @OA\Property(property="is_wash_window", type="integer", example=5),
     *                         @OA\Property(property="is_wash_dashboard", type="integer", example=4),
     *                         @OA\Property(property="is_wash_tires", type="integer", example=5),
     *                         @OA\Property(property="is_wash_trash", type="integer", example=5),
     *                         @OA\Property(property="is_wash_engine", type="integer", example=1),
     *                         @OA\Property(property="is_wash_seat", type="integer", example=4),
     *                         @OA\Property(property="is_wash_carpet", type="integer", example=4),
     *                         @OA\Property(property="is_wash_pillows", type="integer", example=0),
     *                         @OA\Property(property="is_fill_window_washing_water", type="integer", example=5),
     *                         @OA\Property(property="is_wash_hollow", type="integer", example=3),
     *                         @OA\Property(property="total_price", type="integer", example=475000),
     *                         @OA\Property(property="avg_price_per_wash", type="integer", example=95000)
     *                   )
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
     *         description="wash failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="wash summary not found")
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
    public function getWashSummaryByVehicleId(Request $request){
        try{
            $user_id = $request->user()->id;
            $vehicle_id = $request->query('vehicle_id') ?? null;

            // Model 
            $res = WashModel::getWashSummaryByVehicleId($user_id,$vehicle_id);

            // Response
            if($res && count($res) > 0) {
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
