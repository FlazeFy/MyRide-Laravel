<?php

namespace App\Http\Controllers\Api\StatsApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Helper
use App\Helpers\Generator;
// Models
use App\Models\TripModel;
use App\Models\VehicleModel;
use App\Models\UserModel;
use App\Models\ServiceModel;
use App\Models\DriverModel;
use App\Models\CleanModel;
use App\Models\MultiModel;

class Queries extends Controller
{
    /**`1
     * @OA\GET(
     *     path="/api/v1/stats/total/trip/{context}",
     *     summary="Get total trip by context",
     *     description="This request is used to get total trip by `context`, that can be trip_category, trip_origin_name, and trip_destination_name. This request is using MySql database, and have a protected routes.",
     *     tags={"Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="trip_category"
     *         ),
     *         description="Trip Context",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="stats fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="stats fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="context", type="string", example="Others"),
     *                          @OA\Property(property="total", type="integer", example=4)
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
     *         description="stats failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="stats not found")
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
    public function getTotalTripByContext(Request $request, $context)
    {
        try{
            $user_id = $request->user()->id;

            if($context == "trip_category" || $context == "trip_origin_name" || $context == "trip_destination_name"){

                $res = TripModel::getContextTotalStats($context,$user_id);
                
                if ($res && count($res) > 0) {
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("fetch", 'stats'),
                        'data' => $res
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", 'stats'),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("custom", "$context is not available"),
                ], Response::HTTP_BAD_REQUEST);
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
     *     path="/api/v1/stats/total/vehicle/{context}",
     *     summary="Get total vehicle by context",
     *     description="This request is used to get total vehicle by `context`, that can be vehicle_merk, vehicle_type, vehicle_status, vehicle_fuel_status, vehicle_transmission, and vehicle_color. This request is using MySql database, and have a protected routes.",
     *     tags={"Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="vehicle_merk"
     *         ),
     *         description="Vehicle Context",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="stats fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="stats fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="context", type="string", example="Honda"),
     *                          @OA\Property(property="total", type="integer", example=2)
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
     *         description="stats failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="stats not found")
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
    public function getTotalVehicleByContext(Request $request, $context)
    {
        try{
            $user_id = $request->user()->id;
            $vehicleContext = ["vehicle_merk","vehicle_type","vehicle_status","vehicle_category","vehicle_fuel_status","vehicle_transmission","vehicle_color"];
            
            $res = null;
            if(str_contains($context, ",")){
                $context_list = explode(",", $context);
                $res = [];
                foreach ($context_list as $dt) {
                    if (in_array($dt, $vehicleContext)) {
                        $res[] = [
                            'context' => $dt,
                            'data' => VehicleModel::getContextTotalStats($dt,$user_id)
                        ];
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("custom", "$dt is not available"),
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            } else {
                if (in_array($context, $vehicleContext)) {
                    $res = VehicleModel::getContextTotalStats($context,$user_id);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("custom", "$context is not available"),
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            
            if ($res) {
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", 'stats'),
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", 'stats'),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/trip/{year}",
     *     summary="Get total trip by vehicle per month",
     *     description="This request is used to get total trip by vehicle per month by given `year`. This request is using MySql database, and have a protected routes.",
     *     tags={"Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="year",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="2024"
     *         ),
     *         description="Trip created year",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="stats fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="stats fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="context", type="string", example="Jan"),
     *                          @OA\Property(property="total", type="integer", example=3)
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
     *         description="stats failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="stats not found")
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
    public function getTotalTripByVehiclePerYear(Request $request, $year, $vehicle_id = null)
    {
        try{
            $user_id = $request->user()->id;
            $res = TripModel::getTotalTripByVehiclePerYear($user_id, $vehicle_id, $year);
            
            if ($res && count($res) > 0) {
                $res_final = [];
                for ($i=1; $i <= 12; $i++) { 
                    $total = 0;
                    foreach ($res as $idx => $val) {
                        if($i == $val->context){
                            $total = $val->total;
                            break;
                        }
                    }
                    array_push($res_final, [
                        'context' => Generator::generateMonthName($i,'short'),
                        'total' => $total,
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", 'stats'),
                    'data' => $res_final
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", 'stats'),
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
     *     path="/api/v1/stats/summary",
     *     summary="Get summary of the apps",
     *     description="This request is used to get summary of the apps. This request is using MySql database, and have a protected routes.",
     *     tags={"Stats"},
     *     @OA\Response(
     *         response=200,
     *         description="stats fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="stats fetched"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="total_user", type="integer", example=2),
     *                     @OA\Property(property="total_vehicle", type="integer", example=3),
     *                     @OA\Property(property="total_service", type="integer", example=2),
     *                     @OA\Property(property="total_clean", type="integer", example=4),
     *                     @OA\Property(property="total_driver", type="integer", example=4)
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
     *         description="stats failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="stats not found")
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
    public function getSummaryApps(Request $request){
        try{
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }

            $total_vehicle = MultiModel::countTotalContext('vehicle',$user_id);
            $total_clean = MultiModel::countTotalContext('clean',$user_id);
            $total_driver = MultiModel::countTotalContext('driver',$user_id);
            $total_service = MultiModel::countTotalContext('service',$user_id);
            $total_trip = MultiModel::countTotalContext('trip',$user_id);

            $data = [
                'total_vehicle' => $total_vehicle,
                'total_service' => $total_service,
                'total_clean'   => $total_clean,
                'total_driver'  => $total_driver,
                'total_trip'    => $total_trip,
            ];

            if($user_id == null){
                $total_user = MultiModel::countTotalContext('users',$user_id);
                $data['total_user'] = $total_user;
            }

            return response()->json([
                'status' => 'success',
                'message' => Generator::getMessageTemplate("fetch", 'stats'),
                'data' => $data
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
