<?php

namespace App\Http\Controllers\Api\ChatApi;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// Model
use App\Models\ChatHistoryModel;
// Service
use App\Services\AIService;
// Helpers
use App\Helpers\Validation;
use App\Helpers\Generator;

class CommandsAI extends Controller
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * @OA\POST(
     *     path="/api/v1/chat/ai",
     *     summary="Post Chat (AI)",
     *     description="This AI request is used to do analyze and find data using command (prompt) to generate dynamic query. This request interacts with the MySQL database.",
     *     tags={"Chat"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question"},
     *             @OA\Property(property="question", type="string", example="can you find my vehicle with the most trip?"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="chat answered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="lorem ipsum")
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
    public function postChatAI(Request $request) {
        try {
            // Validate request body
            $validator = Validation::getValidateChat($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $user_id = $request->user()->id;
            $cacheKey = "ai_chat_{$user_id}_".md5(strtolower(trim($request->question)));

            // Caching pipeline (Generated SQL and narration)
            $result = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $user_id) {
                $sql = $this->ai->generateSQL($request->question, $user_id);
                $safeSql = $this->ai->validateSQL($sql, $user_id);
                $res = DB::select($safeSql);
                $text = $this->ai->generateNarration($request->question, $res);

                // Store the conversation
                ChatHistoryModel::createChatHistory([
                    'question' => $request->question, 
                    'chat_type' => 'ai', 
                    'is_success' => !empty($res) ? 1 : 0,
                    'answer' => $text,
                    'sql_query' => $safeSql
                ], $user_id);

                return [
                    'message' => $text
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => $result['message']
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}