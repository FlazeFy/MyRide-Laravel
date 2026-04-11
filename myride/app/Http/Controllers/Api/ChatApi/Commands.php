<?php

namespace App\Http\Controllers\Api\ChatApi;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

// Model
use App\Models\ChatHistoryModel;
// Helpers
use App\Helpers\Generator;

class Commands extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = "chat";
    }
    /**
     * @OA\DELETE(
     *     path="/api/v1/chat/delete/{chat_type}",
     *     summary="Soft Delete All Chat History By Type",
     *     description="This chat request is used to soft delete all chat history by type. This means it will no longer be visible to the user, but it will still be retained and consumed by the system. This request interacts with the MySQL database.",
     *     tags={"Chat"},
     *     @OA\Parameter(
     *         name="chat_type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Chat Type",
     *         example="ai",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="chat deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="chat deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="{validation_msg}",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="{field validation message}")
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
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function softDeleteChatHistoryByType(Request $request, $chat_type) {
        try {   
            $user_id = $request->user()->id;

            // Chat type not valid
            if ($chat_type !== "ai" && $chat_type !== "nlp" ) {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("custom", "$chat_type is not available"),
                ], Response::HTTP_BAD_REQUEST);
            }

            // Soft delete chat history by type
            $rows = ChatHistoryModel::updateChatHistoryByType($user_id, $chat_type, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
            if ($rows > 0) {
                // Versioning cache
                $versionKey = "chat_history_version:{$user_id}:{$chat_type}";
                !Cache::has($versionKey) ? Cache::put($versionKey, 2) : Cache::increment($versionKey);

                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("delete", $this->module),
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