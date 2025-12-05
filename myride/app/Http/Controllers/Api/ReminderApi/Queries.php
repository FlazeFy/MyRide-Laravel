<?php

namespace App\Http\Controllers\Api\ReminderApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Helper
use App\Helpers\Generator;
// Models
use App\Models\ReminderModel;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "reminder";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/reminder/next",
     *     summary="Get next reminder",
     *     description="This request is used to get the nearest reminder for now. This request is using MySql database, and have a protected routes.",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="reminder fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="reminder fetched"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="reminder_title", type="string", example="service berkala"),
     *                     @OA\Property(property="reminder_context", type="string", example="Service"),
     *                     @OA\Property(property="reminder_body", type="string", example="at 90.000 KM"),
     *                     @OA\Property(property="remind_at", type="string", example="2025-09-05 00:00:00"),
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
     *         description="reminder failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="reminder not found")
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
    public function getNextReminder(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            
            $res = ReminderModel::getNextReminder($user_id);
            
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
     *     path="/api/v1/reminder",
     *     summary="Get all reminder history",
     *     description="This request is used to get all reminder history with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="reminder fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="reminder fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                         @OA\Property(property="reminder_title", type="string", example="Routine service"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                         @OA\Property(property="reminder_context", type="string", example="Service"),
     *                         @OA\Property(property="reminder_body", type="string", example="Lorem ipsum"),
     *                         @OA\Property(property="reminder_attachment", type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="attachment_type", type="string", example="location"),
     *                                 @OA\Property(property="attachment_value", type="string", example="-6.223617982933017, 106.84620287273809")
     *                             ),
     *                             example={{
     *                                 "attachment_type": "location",
     *                                 "attachment_value": "-6.223617982933017, 106.84620287273809"
     *                             }}
     *                         ),
     *                         @OA\Property(property="remind_at", type="string", nullable=true, example="2025-06-24 10:54:42"),
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
     *         description="reminder failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="reminder history not found")
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
    public function getAllReminder(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model
            $res = ReminderModel::getAllReminder($user_id,$limit);

            // Response
            if(count($res) > 0) {
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
     *     path="/api/v1/reminder/recently",
     *     summary="Get recently reminder history",
     *     description="This request is used to get recently reminder history with pagination. This request is using MySql database, and have a protected routes.",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="reminder fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="reminder fetched"),
     *             @OA\Property(property="data",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                         @OA\Property(property="reminder_title", type="string", example="Routine service"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="D 1610 ZBC"),
     *                         @OA\Property(property="reminder_context", type="string", example="Service"),
     *                         @OA\Property(property="reminder_body", type="string", example="Lorem ipsum"),
     *                         @OA\Property(property="remind_at", type="string", nullable=true, example="2025-06-24 10:54:42")
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
     *         description="reminder failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="reminder history not found")
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
    public function getRecentlyReminder(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $limit = $request->query("limit",14);

            // Model
            $res = ReminderModel::getRecentlyReminder($user_id,$limit);

            // Response
            if(count($res) > 0) {
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
     *     path="/api/v1/reminder/vehicle/{vehicle_id}",
     *     summary="Get reminder by vehicle",
     *     description="This request is used to get all reminder by specific vehicle. This request is using MySql database, and have a protected routes.",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="reminder fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="reminder fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="28668090-5653-dff5-2d8f-af603fc36b45"),
     *                         @OA\Property(property="reminder_title", type="string", example="Routine service"),
     *                         @OA\Property(property="reminder_context", type="string", example="Service"),
     *                         @OA\Property(property="reminder_body", type="string", example="Lorem ipsum"),
     *                         @OA\Property(property="reminder_attachment", type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="attachment_type", type="string", example="location"),
     *                                 @OA\Property(property="attachment_value", type="string", example="-6.223617982933017, 106.84620287273809")
     *                             ),
     *                             example={{
     *                                 "attachment_type": "location",
     *                                 "attachment_value": "-6.223617982933017, 106.84620287273809"
     *                             }}
     *                         ),
     *                         @OA\Property(property="remind_at", type="string", nullable=true, example="2025-06-24 10:54:42"),
     *                     )
     *                 ),
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
     *         description="reminder failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="reminder history not found")
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
    public function getReminderByVehicle(Request $request, $vehicle_id)
    {
        try{
            $user_id = $request->user()->id;

            // Model
            $res = ReminderModel::getReminderByVehicle($user_id,$vehicle_id);

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
