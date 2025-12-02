<?php

namespace App\Http\Controllers\Api\DictionaryApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

// Model
use App\Models\DictionaryModel;
// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "dictionary";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/dictionary/type/{type}",
     *     summary="Get dictionary by type",
     *     description="This request is used to get dictionary by its `dictionary_type`, that can be trip_category, vehicle_merk, vehicle_type, vehicle_category, vehicle_status, vehicle_default_fuel, vehicle_fuel_status, or vehicle_transmission_code. This request is using MySql database, and have a protected routes.",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="trip_category"
     *         ),
     *         description="Dictionary Type",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="dictionary fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="dictionary fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="dictionary_name", type="string", example="Others"),
     *                          @OA\Property(property="dictionary_type", type="string", example="trip_category")
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
     *         description="dictionary failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="dictionary not found")
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
    public function getDictionaryByType(Request $request,$type)
    {
        try{
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }

            // Model
            $res = DictionaryModel::select('dictionary_name','dictionary_type')
                ->where(function($query) use ($user_id){
                    $query->where('created_by',$user_id)
                        ->orwhereNull('created_by');
                });
            if(strpos($type, ',')){
                $dcts = explode(",", $type);
                $res = $res->where(function($query) use ($dcts) {
                    foreach ($dcts as $dt) {
                        $query->orWhere('dictionary_type', $dt);
                    }
                });
            } else {
                $res = $res->where('dictionary_type',$type); 
            }

            $res = $res->orderby('dictionary_type', 'ASC')
                ->orderby('dictionary_name', 'ASC')
                ->get();
            
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
}
