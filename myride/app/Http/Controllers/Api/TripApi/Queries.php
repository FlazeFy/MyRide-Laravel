<?php

namespace App\Http\Controllers\Api\TripApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\TripModel;
// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "trip";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/trip",
     *     summary="Get all trip history",
     *     description="This request is used to get all trip history with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="trip fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                         @OA\Property(property="driver_name", type="string", example="Jhon Doe"),
     *                         @OA\Property(property="trip_desc", type="string", example="jalan2"),
     *                         @OA\Property(property="trip_category", type="string", example="Others"),
     *                         @OA\Property(property="trip_origin_name", type="string", example="Budi House"),
     *                         @OA\Property(property="trip_person", type="string", nullable=true, example="Budi"),
     *                         @OA\Property(property="trip_origin_coordinate", type="string", example="-6.252640549671855, 106.76424433238519"),
     *                         @OA\Property(property="trip_destination_name", type="string", example="Central Park"),
     *                         @OA\Property(property="trip_destination_coordinate", type="string", example="-6.177464532426197, 106.7912179194768"),
     *                         @OA\Property(property="created_at", type="string", format="datetime", example="2025-06-19 07:54:42")
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
     *         description="trip failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="trip history not found")
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
    public function getAllTrip(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model
            $res = TripModel::getAllTrip($user_id,$limit);

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
     *     path="/api/v1/trip/driver/{driver_id}",
     *     summary="Get all trip history by driver id",
     *     description="This request is used to get all trip history by driver id with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="trip fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                         @OA\Property(property="trip_desc", type="string", example="jalan2"),
     *                         @OA\Property(property="trip_category", type="string", example="Others"),
     *                         @OA\Property(property="trip_origin_name", type="string", example="Budi House"),
     *                         @OA\Property(property="trip_person", type="string", nullable=true, example="Budi"),
     *                         @OA\Property(property="trip_origin_coordinate", type="string", example="-6.252640549671855, 106.76424433238519"),
     *                         @OA\Property(property="trip_destination_name", type="string", example="Central Park"),
     *                         @OA\Property(property="trip_destination_coordinate", type="string", example="-6.177464532426197, 106.7912179194768"),
     *                         @OA\Property(property="created_at", type="string", format="datetime", example="2025-06-19 07:54:42")
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
     *         description="trip failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="trip history not found")
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
    public function getAllTripByDriverId(Request $request,$driver_id)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model
            $res = TripModel::getAllTrip($user_id,$limit,$driver_id);

            // Response
            if(count($res) > 0) {
                $res->getCollection()->transform(function ($item) {
                    unset($item->id, $item->driver_fullname); 
                    return $item;
                });

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
     *     path="/api/v1/trip/last",
     *     summary="Get last trip",
     *     description="This request is used to get the last trip. This request is using MySql database, and have a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="trip fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="trip_destination_name", type="string", example="Location A"),
     *                     @OA\Property(property="trip_destination_coordinate", type="string", example="-6.177362076836449,106.79156507985539"),
     *                     @OA\Property(property="driver_username", type="string", example="jhondoe"),
     *                     @OA\Property(property="vehicle_plate_number", type="string", example="EY 28 BK"),
     *                     @OA\Property(property="created_at", type="string", example="2025-09-05 00:00:00"),
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
     *         description="trip failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="trip not found")
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
    public function getLastTrip(Request $request)
    {
        try{
            $user_id = $request->user()->id;

            // Model
            $res = TripModel::getLastTrip($user_id);

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
     *     path="/api/v1/trip/discovered",
     *     summary="Get trip discovered summary",
     *     description="This request is used to get trip discovered summary. This request is using MySql database, and have a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="trip fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="total_trip", type="integer", example=20),
     *                     @OA\Property(property="distance_km", type="string", example="10.000 km"),
     *                     @OA\Property(property="last_update", type="string", example="2025-09-05 00:00:00")
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
     *         description="trip failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="trip not found")
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
    public function getTripDiscovered(Request $request)
    {
        try{
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }
            $vehicle_id = $request->query("vehicle_id",null);

            // Model
            $res = TripModel::getTripDiscovered($user_id,$vehicle_id);

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
