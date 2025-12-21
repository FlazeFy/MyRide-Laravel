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
     *     summary="Get All Trip History",
     *     description="This request is used to get all trip history. This request interacts with the MySQL database, has a protected routes, and a pagination.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trip fetched successfully. Ordered in descending order by `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                         @OA\Property(property="vehicle_name", type="string", example="Brio RS MT"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
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

            // Get all trip with pagination
            $res = TripModel::getAllTrip($user_id,$limit);
            if($res && count($res) > 0) {
                // Return success response
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
     *     summary="Get All Trip History By Driver ID",
     *     description="This request is used to get all trip history by given `driver_id`. This request interacts with the MySQL database, has a protected routes, and a pagination.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trip fetched successfully. Ordered in descending order by `created_at`",
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

            // Get all trip with pagination by driver_id
            $res = TripModel::getAllTrip($user_id,$limit,$driver_id);
            if(count($res) > 0) {
                // Remove id and driver_fullname
                $res->getCollection()->transform(function ($item) {
                    unset($item->id, $item->driver_fullname); 
                    return $item;
                });

                // Return success response
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
     *     summary="Get Last Trip",
     *     description="This request is used to get the last trip. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trip fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="trip_destination_name", type="string", example="Location A"),
     *                     @OA\Property(property="trip_destination_coordinate", type="string", example="-6.177362076836449,106.79156507985539"),
     *                     @OA\Property(property="driver_username", type="string", example="jhondoe"),
     *                     @OA\Property(property="vehicle_type", type="string", example="City Car"),
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

            // Get last trip
            $res = TripModel::getLastTrip($user_id);
            if($res) {
                // Return success response
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
     *     summary="Get Trip Discovered Summary",
     *     description="This request is used to get trip discovered summary. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trip fetched successfully",
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
            // Check whether authentication is attached. If yes, retrieve statistics by user; if not, retrieve statistics for all users
            if ($request->hasHeader('Authorization')) {
                $user = Auth::guard('sanctum')->user(); 
                $user_id = $user ? $user->id : null;
            } else {
                $user_id = null;
            }
            
            // This will get all trip if vehicle_id not attached
            $vehicle_id = $request->query("vehicle_id",null);
            // Get trip discovered stats
            $res = TripModel::getTripDiscovered($user_id,$vehicle_id);
            if ($res && $res['last_update']) {
                // Return success response
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
     *     path="/api/v1/trip/coordinate/{trip_location_name}",
     *     summary="Get Trip History Coordinate By Location Name",
     *     description="This request is used to get trip history's coordinate by given `trip_location_name`. This request interacts with the MySQL database, and has a protected routes.",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trip fetched successfully. Ordered in ascending order by `trip_location_name`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="trip_location_coordinate", type="string", example="-6.177362076836449,106.79156507985539"),
     *                      @OA\Property(property="trip_location_name", type="string", example="Location A")
     *                  )
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
    public function getCoordinateByTripLocationName(Request $request, $trip_location_name)
    {
        try{
            $user_id = $request->user()->id;

            // Get trip history's coordinate by trip location name
            $res = TripModel::getCoordinateByTripLocationName($user_id,$trip_location_name);
            if(count($res) > 0) {
                // Return success response
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/trip/calendar",
     *     summary="Get Trip As Calendar Format",
     *     description="This request is used to get all trip data but in calendar format.  This request interacts with the MySQL database, and has protected routes",
     *     tags={"Trip"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Trip fetched successfully. Ordered in descending order by `created_at`",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="trip fetched"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="trip_location_name", type="string", example="Location A - Location B"),
     *                      @OA\Property(property="vehicle_plate_number", type="string", example="D 1710 PWT"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-19 02:37:58")
     *                  )
     *             ),
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
    public function getTripCalendar(Request $request)
    {
        try{
            $user_id = $request->user()->id;

            // Get trip calendar format
            $res = TripModel::getTripCalendar($user_id);
            if (count($res) > 0) {
                // Return success response
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
