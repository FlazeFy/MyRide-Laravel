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
                    'message' => Generator::getMessageTemplate("fetch", 'reminder'),
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", 'reminder'),
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
