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
use App\Models\WashModel;
use App\Models\FuelModel;
use App\Models\MultiModel;

class Queries extends Controller
{
    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/trip/{context}",
     *     summary="Get Total Trip By Context",
     *     description="This request is used to get total trip by `context`, that can be trip_category, trip_origin_name, and trip_destination_name. This request interacts with the MySQL database, and has a protected routes.",
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

            $res = null;
            // Check if context contain multiple item that separate using comma
            if(str_contains($context,",")){
                $list_context = explode(",",$context);
                foreach ($list_context as $dt) {
                    if($dt == "trip_category" || $dt == "trip_origin_name" || $dt == "trip_destination_name"){
                        // Get the total by context in the trip table
                        $res[] = [
                            'context' => $dt,
                            'data' => MultiModel::getContextTotalStats($dt,$user_id,'trip')
                        ];
                    } else {
                        // Context not valid
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("custom", "$dt is not available"),
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            } else {
                // Get the total by context in the trip table
                $res = MultiModel::getContextTotalStats($context,$user_id,'trip');
            }
                
            if ($res) {
                // Return success response
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
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/inventory/{context}",
     *     summary="Get Total Inventory By Context",
     *     description="This request is used to get total inventory by `context`, that can be inventory_category, and inventory_storage. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="inventory_category"
     *         ),
     *         description="Inventory Context",
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
    public function getTotalInventoryByContext(Request $request, $context)
    {
        try{
            $user_id = $request->user()->id;

            $res = null;
            // Check if context contain multiple item that separate using comma
            if(str_contains($context,",")){
                $list_context = explode(",",$context);
                foreach ($list_context as $dt) {
                    if($dt == "inventory_category" || $dt == "inventory_storage"){
                        // Get the total by context in the inventory table
                        $res[] = [
                            'context' => $dt,
                            'data' => MultiModel::getContextTotalStats($dt,$user_id,'inventory')
                        ];
                    } else {
                        // Context not valid
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("custom", "$dt is not available"),
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            } else {
                // Get the total by context in the inventory table
                $res = MultiModel::getContextTotalStats($context,$user_id,'inventory');
            }
                
            if ($res) {
                // Return success response
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
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/service/{context}",
     *     summary="Get Total Service Price By Context",
     *     description="This request is used to get total service by `context`, that can be service_category, and service_location. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="service_category"
     *         ),
     *         description="Service Context",
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
     *                          @OA\Property(property="total", type="integer", example=2500000)
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
    public function getTotalServicePriceByContext(Request $request, $context){
        try{
            $user_id = $request->user()->id;

            $res = null;
            // Check if context contain multiple item that separate using comma
            if(str_contains($context,",")){
                $list_context = explode(",",$context);
                foreach ($list_context as $dt) {
                    if($dt == "service_category" || $dt == "service_location"){
                        // Get the total by context in the service table
                        $res[] = [
                            'context' => $dt,
                            'data' => MultiModel::getContextTotalStats($dt,$user_id,'service',"CAST(SUM(service_price_total) AS SIGNED)")
                        ];
                    } else {
                        // Context not valid
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("custom", "$dt is not available"),
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            } else {
                // Get the total by context in the service table
                $res = MultiModel::getContextTotalStats($context,$user_id,'service',"CAST(SUM(service_price_total) AS SIGNED)");
            }
                
            if ($res) {
                // Return success response
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
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/vehicle/{context}",
     *     summary="Get Total Vehicle By Context",
     *     description="This request is used to get total vehicle by `context`, that can be vehicle_merk, vehicle_type, vehicle_status, vehicle_fuel_status, vehicle_transmission, and vehicle_color. This request interacts with the MySQL database, and has a protected routes.",
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
            // Check if context contain multiple item that separate using comma
            if(str_contains($context, ",")){
                $context_list = explode(",", $context);
                $res = [];
                foreach ($context_list as $dt) {
                    if (in_array($dt, $vehicleContext)) {
                        // Get the total by context in the vehicle table
                        $res[] = [
                            'context' => $dt,
                            'data' => MultiModel::getContextTotalStats($dt,$user_id,'vehicle')
                        ];
                    } else {
                        // Context not valid
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("custom", "$dt is not available"),
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            } else {
                if (in_array($context, $vehicleContext)) {
                    // Get the total by context in the vehicle table
                    $res = MultiModel::getContextTotalStats($context,$user_id,'vehicle');
                } else {
                    // Context not valid
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("custom", "$context is not available"),
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            if ($res) {
                // Return success response
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
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/trip/{year}/{vehicle_id}",
     *     summary="Get Total Trip By Vehicle Per Month",
     *     description="This request is used to get total trip by vehicle per month by given `year` and `vehicle_id`. This request interacts with the MySQL database, and has a protected routes.",
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
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"
     *         ),
     *         description="Vehicle ID",
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
            
            // Get total trip by its vehicle per year period
            $res = TripModel::getTotalTripByVehiclePerYear($user_id, $vehicle_id, $year);
            if ($res && count($res) > 0) {
                $res_final = [];
                // Mapping per month
                for ($i=1; $i <= 12; $i++) { 
                    $total = 0;
                    foreach ($res as $idx => $val) {
                        if($i == $val->context){
                            $total = $val->total;
                            break;
                        }
                    }
                    // Get month name short
                    array_push($res_final, [
                        'context' => Generator::generateMonthName($i,'short'),
                        'total' => $total,
                    ]);
                }

                // Return success response
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
     *     path="/api/v1/stats/total/trip/monthly/{year}",
     *     summary="Get Total Trip Per Year",
     *     description="This request is used to get total trip per month by given `year`. This request interacts with the MySQL database, and has a protected routes.",
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
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"
     *         ),
     *         description="Vehicle ID",
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
    public function getTotalTripPerYear(Request $request, $year)
    {
        try{
            // Check whether authentication is attached. If yes, retrieve statistics by user; if not, retrieve statistics for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }

            // Get total trip per year period
            $res = TripModel::getTotalTripByVehiclePerYear($user_id, null, $year);
            if ($res && count($res) > 0) {
                $res_final = [];
                // Mapping per month
                for ($i=1; $i <= 12; $i++) { 
                    $total = 0;
                    foreach ($res as $idx => $val) {
                        if($i == $val->context){
                            $total = (int)$val->total;
                            break;
                        }
                    }
                    // Get month name short
                    array_push($res_final, [
                        'context' => Generator::generateMonthName($i,'short'),
                        'total' => $total,
                    ]);
                }

                // Return success response
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
     *     path="/api/v1/stats/total/fuel/monthly/{context}/{year}",
     *     summary="Get Total Fuel Per Year",
     *     description="This request is used to get total fuel per month by given `year` and `context`. This request interacts with the MySQL database, and has a protected routes.",
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
     *         description="Fuel consumption created year",
     *     ),
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="fuel_volume"
     *         ),
     *         description="Context can be `fuel_volume` or `fuel_price_total`",
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"
     *         ),
     *         description="Vehicle ID",
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
    public function getTotalFuelPerYear(Request $request, $context, $year)
    {
        try{
            // Check whether authentication is attached. If yes, retrieve statistics by user; if not, retrieve statistics for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }
            $vehicle_id = $request->query('vehicle_id') ?? null;

            // Check if context valid
            if($context !== "fuel_volume" || $context !== "fuel_price_total"){
                // Get total fuel for specific year per vehicle by vehicle_id or all vehicle
                $res = FuelModel::getTotalFuelByVehiclePerYear($user_id, $vehicle_id, $context, $year);
                
                if ($res && count($res) > 0) {
                    $res_final = [];
                    // Mapping per month
                    for ($i=1; $i <= 12; $i++) { 
                        $total = 0;
                        foreach ($res as $idx => $val) {
                            if($i == $val->context){
                                $total = (int)$val->total;
                                break;
                            }
                        }
                        // Get month name short
                        array_push($res_final, [
                            'context' => Generator::generateMonthName($i,'short'),
                            'total' => $total,
                        ]);
                    }

                    // Return success response
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
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("validation_failed", 'context must be fuel_volume or fuel_price_total'),
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
     *     path="/api/v1/stats/total/service/monthly/{context}/{year}",
     *     summary="Get Total Service Per Year",
     *     description="This request is used to get total service per month by given `year` and `context`. This request is using MySql database.",
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
     *         description="Service created year",
     *     ),
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="total_item"
     *         ),
     *         description="Context can be `total_item` or `total_price`",
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
    public function getTotalServicePerYear(Request $request, $context, $year)
    {
        try{
            // Check whether authentication is attached. If yes, retrieve statistics by user; if not, retrieve statistics for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }

            // Check if context valid
            if($context !== "total_item" || $context !== "total_price"){
                // Get total service per year by context
                $res = ServiceModel::getTotalServicePerYear($user_id, $context, $year);
                if ($res && count($res) > 0) {
                    $res_final = [];
                    // Mapping per month
                    for ($i=1; $i <= 12; $i++) { 
                        $total = 0;
                        foreach ($res as $idx => $val) {
                            if($i == $val->context){
                                $total = (int)$val->total;
                                break;
                            }
                        }
                        // Get month name short
                        array_push($res_final, [
                            'context' => Generator::generateMonthName($i,'short'),
                            'total' => $total,
                        ]);
                    }

                    // Return success response
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
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("validation_failed", 'context must be total_item or total_price'),
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
     *     path="/api/v1/stats/total/wash/{context}/{year}",
     *     summary="Get Total Wash Per Month",
     *     description="This request is used to get total wash per month by given `year` and `context`. This request is using MySql database.",
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
     *         description="Wash created year",
     *     ),
     *     @OA\Parameter(
     *         name="context",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="total_item"
     *         ),
     *         description="Context can be `total_item` or `total_price`",
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"
     *         ),
     *         description="Vehicle ID",
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
    public function getTotalWashPerYear(Request $request, $context, $year)
    {
        try{
            // Check whether authentication is attached. If yes, retrieve statistics by user; if not, retrieve statistics for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }
            $vehicle_id = $request->query('vehicle_id') ?? null;

            // Check if context valid
            if($context !== "total_item" || $context !== "total_price"){
                // Get total wash for specific year per vehicle by vehicle_id or all vehicle
                $res = WashModel::getTotalWashPerYear($user_id, $vehicle_id, $context, $year);
                
                if ($res && count($res) > 0) {
                    $res_final = [];
                    // Mapping per month
                    for ($i=1; $i <= 12; $i++) { 
                        $total = 0;
                        foreach ($res as $idx => $val) {
                            if($i == $val->context){
                                $total = (int)$val->total;
                                break;
                            }
                        }
                        // Get month name short
                        array_push($res_final, [
                            'context' => Generator::generateMonthName($i,'short'),
                            'total' => $total,
                        ]);
                    }

                    // Return success response
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
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("validation_failed", 'context must be total_item or total_price'),
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
     *     path="/api/v1/stats/summary",
     *     summary="Get Summary Of The Apps",
     *     description="This request is used to get summary of the apps. This request interacts with the MySQL database, and has a protected routes.",
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
     *                     @OA\Property(property="total_wash", type="integer", example=4),
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
            // Check whether authentication is attached. If yes, retrieve statistics by user; if not, retrieve statistics for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }

            // Get total item for all table (main module)
            $total_vehicle = MultiModel::countTotalContext('vehicle',$user_id);
            $total_wash = MultiModel::countTotalContext('wash',$user_id);
            $total_driver = MultiModel::countTotalContext('driver',$user_id);
            $total_service = MultiModel::countTotalContext('service',$user_id);
            $total_trip = MultiModel::countTotalContext('trip',$user_id);
            $data = [
                'total_vehicle' => $total_vehicle,
                'total_service' => $total_service,
                'total_wash' => $total_wash,
                'total_driver' => $total_driver,
                'total_trip' => $total_trip,
            ];

            // If authentication is not attached, also get total user
            if($user_id == null){
                $total_user = MultiModel::countTotalContext('users',$user_id);
                $data['total_user'] = $total_user;
            }

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => Generator::getMessageTemplate("fetch", 'stats'),
                'data' => $data
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/total/most_person_trip_with",
     *     summary="Get Person With Most Trip With",
     *     description="This request is used to get list of person that most trip with. This request is using MySql database, and has a protected routes",
     *     tags={"Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="stats fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="stats fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="context", type="string", example="John Doe"),
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
    public function getPersonWithMostTripWith(Request $request)
    {
        try {
            $user_id = $request->user()->id;

            // Get most person to travel with
            $res = TripModel::getPersonWithMostTripWith($user_id, null, 7);
            if ($res && count($res) > 0) {
                // Return success response
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
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/stats/journey/{vehicle_id}",
     *     summary="Get Journey By Vehicle ID",
     *     description="This request is used to get journey (trip, service, wash, and refuel history). This request is using MySql database, and has a protected routes",
     *     tags={"Stats"},
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
     *         description="stats fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="stats fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="journey_category", type="string", example="Fuel"),
     *                          @OA\Property(property="journey_context", type="string", example="You refueled 12L of Pertamina (92)"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
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
    public function getJourney(Request $request, $vehicle_id)
    {
        try {
            $user_id = $request->user()->id;

            $trips = TripModel::getJourney($user_id, $vehicle_id)->map(function ($item) {
                $person = $item->trip_person ? " with $item->trip_person" : "";
    
                return [
                    'journey_category' => 'trip',
                    'journey_context' => "You made a $item->trip_category trip$person from $item->trip_origin_name to $item->trip_destination_name",
                    'created_at' => $item->created_at
                ];
            });
    
            $washes = WashModel::getJourney($user_id, $vehicle_id)->map(function ($item) {
                $desc = $item->wash_desc ? " ({$item->wash_desc})" : "";
                $price = $item->wash_price ? " costing Rp " . number_format($item->wash_price) : "";

                return [
                    'journey_category' => 'wash',
                    'journey_context' => "You washed your vehicle at {$item->wash_address}{$desc}{$price}",
                    'created_at' => $item->created_at
                ];
            });
    
            $services = ServiceModel::getJourney($user_id, $vehicle_id)->map(function ($item) {
                $note = $item->service_note ? " ({$item->service_note})" : "";
                $price = $item->service_price_total ? " costing Rp " . number_format($item->service_price_total) : "";
    
                return [
                    'journey_category' => 'service',
                    'journey_context' => "You did $item->service_category service at {$item->service_location}{$note}{$price}",
                    'created_at' => $item->created_at
                ];
            });
    
            $fuels = FuelModel::getJourney($user_id, $vehicle_id)->map(function ($item) {
                if ($item->fuel_brand !== "Electric") {
                    $fuel_brand = "$item->fuel_brand (Ron $item->fuel_ron)";
                } else {
                    $fuel_brand = "Electric";
                }

                return [
                    'journey_category' => 'fuel',
                    'journey_context' => "You refueled {$item->fuel_volume}L of $fuel_brand",
                    'created_at' => $item->created_at
                ];
            });
    
            $journey = collect()->merge($trips)->merge($washes)->merge($services)->merge($fuels)->sortByDesc('created_at')->values();
    
            if ($journey->isNotEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", 'stats'),
                    'data' => $journey
                ], Response::HTTP_OK);
            }
    
            return response()->json([
                'status' => 'failed',
                'message' => Generator::getMessageTemplate("not_found", 'stats'),
            ], Response::HTTP_NOT_FOUND);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
