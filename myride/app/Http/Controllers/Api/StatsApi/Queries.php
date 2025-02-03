<?php

namespace App\Http\Controllers\Api\StatsApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Helpers\Generator;

// Models
use App\Models\TripModel;
use App\Models\VehicleModel;
use App\Models\UserModel;

class Queries extends Controller
{
    public function getTotalTripByContext(Request $request, $context)
    {
        try{
            $user_id = $request->user()->id;

            if($context == "trip_category" || $context == "trip_origin_name" || $context == "trip_destination_name"){

                $res = TripModel::getContextTotalStats($context,$user_id);
                
                if (count($res) > 0) {
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
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalVehicleByContext(Request $request, $context)
    {
        try{
            $user_id = $request->user()->id;

            $vehicleContext = ["vehicle_merk","vehicle_type","vehicle_status","vehicle_fuel_status","vehicle_transmission","vehicle_color"];
            if (in_array($context, $vehicleContext)) {
                $res = VehicleModel::getContextTotalStats($context,$user_id);
                
                if (count($res) > 0) {
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
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
