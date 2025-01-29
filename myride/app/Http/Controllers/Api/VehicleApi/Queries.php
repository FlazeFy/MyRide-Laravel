<?php

namespace App\Http\Controllers\Api\VehicleApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Models
use App\Models\VehicleModel;

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
}
