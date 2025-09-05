<?php

namespace App\Http\Controllers\Api\CleanApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

//  Models
use App\Models\CleanModel;
// Helpers
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "clean history";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/clean",
     *     summary="Get all clean history",
     *     description="This request is used to get all clean history with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Clean"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="clean fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="clean fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="ab8b8d0e-d74d-11ed-afa1-0242ac120002"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Honda - Brio RS MT"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZRB"),
     *                         @OA\Property(property="clean_desc", type="string", example="Cuci mobil"),
     *                         @OA\Property(property="clean_by", type="string", example="Carwash"),
     *                         @OA\Property(property="clean_tools", type="string", nullable=true, example=null),
     *                         @OA\Property(property="is_clean_body", type="integer", example=1),
     *                         @OA\Property(property="is_clean_window", type="integer", example=1),
     *                         @OA\Property(property="is_clean_dashboard", type="integer", example=1),
     *                         @OA\Property(property="is_clean_tires", type="integer", example=1),
     *                         @OA\Property(property="is_clean_trash", type="integer", example=1),
     *                         @OA\Property(property="is_clean_engine", type="integer", example=1),
     *                         @OA\Property(property="is_clean_seat", type="integer", example=1),
     *                         @OA\Property(property="is_clean_carpet", type="integer", example=1),
     *                         @OA\Property(property="is_clean_pillows", type="integer", example=0),
     *                         @OA\Property(property="clean_address", type="string", example="AutoService Jl. Kapten Tandean"),
     *                         @OA\Property(property="clean_start_time", type="string", format="datetime", example="2024-02-29 21:30:00"),
     *                         @OA\Property(property="clean_end_time", type="string", format="datetime", example="2024-02-29 22:20:00"),
     *                         @OA\Property(property="is_fill_window_cleaning_water", type="integer", example=0),
     *                         @OA\Property(property="is_clean_hollow", type="integer", example=1),
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
     *         description="clean failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="clean history not found")
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
    public function getAllCleanHistory(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model 
            $res = CleanModel::getAllCleanHistory($user_id,$limit);

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
}
