<?php

namespace App\Http\Controllers\Api\FuelApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Model
use App\Models\FuelModel;
use App\Models\AdminModel;

// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "fuel";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/fuel",
     *     summary="Get all fuel",
     *     description="This request is used to get all fuel purchase history. This request is using MySql database, have a protected routes, and have template pagination.",
     *     tags={"Fuel"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Fuel fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="fuel fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="6f59235e-c398-8a83-2f95-3f1fbe95ca6e"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="B 1234 CD"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                         @OA\Property(property="fuel_volume", type="number", format="float", example=45.5),
     *                         @OA\Property(property="fuel_price_total", type="number", format="float", example=325000),
     *                         @OA\Property(property="fuel_brand", type="string", example="Pertamina"),
     *                         @OA\Property(property="fuel_type", type="string", example="Pertamax"),
     *                         @OA\Property(property="fuel_ron", type="integer", example=92),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                         @OA\Property(property="fuel_bill", type="string", format="uri", example="https://example.com/uploads/fuel_bills/bill123.jpg")
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
     *         description="fuel failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="fuel not found")
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
    public function getAllFuel(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $check_admin = AdminModel::find($user_id);
            $paginate = $request->query('per_page_key') ?? 12;
            $vehicle_id = $request->query('vehicle_id') ?? null;

            $res = FuelModel::getAllFuel($user_id, $vehicle_id, $paginate);
            
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
     *     path="/api/v1/fuel/last",
     *     summary="Get last fuel",
     *     description="This request is used to get last fuel purchase history. This request is using MySql database, have a protected routes, and have template pagination.",
     *     tags={"Fuel"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Fuel record fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="fuel fetched"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="string", example="6f59235e-c398-8a83-2f95-3f1fbe95ca6e"),
     *                  @OA\Property(property="vehicle_plate_number", type="string", example="B 1234 CD"),
     *                  @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                  @OA\Property(property="fuel_volume", type="number", format="float", example=45.5),
     *                  @OA\Property(property="fuel_price_total", type="number", format="float", example=325000),
     *                  @OA\Property(property="fuel_brand", type="string", example="Pertamina"),
     *                  @OA\Property(property="fuel_type", type="string", example="Pertamax"),
     *                  @OA\Property(property="fuel_ron", type="integer", example=92),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                  @OA\Property(property="fuel_bill", type="string", format="uri", example="https://example.com/uploads/fuel_bills/bill123.jpg")
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
     *         description="fuel failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="fuel not found")
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
    public function getLastFuel(Request $request){
        try{
            $user_id = $request->user()->id;
            $vehicle_id = $request->query('vehicle_id') ?? null;

            $res = FuelModel::getLastFuel($user_id, $vehicle_id);
            
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
     *     path="/api/v1/fuel/summary/{month_year}",
     *     summary="Get fuel summary monthly",
     *     description="This request is used to get summary of fuel consume in the specific month period. This request is using MySql database, and have a protected routes.",
     *     tags={"Fuel"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Fuel record fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="fuel fetched"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="total_fuel_volume", type="integer", example=40),
     *                  @OA\Property(property="total_fuel_price", type="integer", example=600000),
     *                  @OA\Property(property="total_refueling", type="integer", example=2),
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
     *         description="fuel failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="fuel not found")
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
    public function getMonthlyFuelSummary(Request $request, $month_year){
        try{
            $user_id = $request->user()->id;
            $vehicle_id = $request->query('vehicle_id') ?? null;

            $res = FuelModel::getMonthlyFuelSummary($user_id, $vehicle_id, $month_year);
            
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
}
