<?php

namespace App\Http\Controllers\Api\VehicleApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Models
use App\Models\VehicleModel;
use App\Models\TripModel;
use App\Models\CleanModel;
// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "vehicle";
    }

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
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => [
                        'detail' => $res,
                        'trip' => $res_trip,
                        'clean' => $res_clean,
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
