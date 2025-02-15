<?php

namespace App\Http\Controllers\Api\UserApi;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;

// Helper
use App\Helpers\Generator;
use App\Helpers\Validation;

// Models
use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\ValidateRequestModel;

// Mailer
use App\Jobs\UserMailer;

class Commands extends Controller
{
    /**
     * @OA\PUT(
     *     path="/api/v1/user/update_telegram_id",
     *     summary="Update telegram token id",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="telegram id updated! and validation has been sended to you",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="telegram id updated! and validation has been sended to you")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="telegram id failed to update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="user not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="telegram user id has been used",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="telegram ID has been used. try another")
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
    public function update_telegram_id(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $new_telegram_id = $request->telegram_user_id;

            $check = UserModel::selectRaw('1')
                ->where('telegram_user_id', $new_telegram_id)
                ->first();

            if($check == null){
                $res = UserModel::where('id',$user_id)
                    ->update([
                        'telegram_user_id' => $new_telegram_id,
                        'telegram_is_valid' => 0
                    ]);
                
                if ($res) {
                    $token_length = 6;
                    $token = Generator::getTokenValidation($token_length);

                    ValidateRequestModel::create([
                        'id' => Generator::getUUID(), 
                        'request_type' => 'telegram_id_validation',
                        'request_context' => $token, 
                        'created_at' => date('Y-m-d H:i:s'), 
                        'created_by' => $user_id
                    ]);

                    $user = UserModel::find($user_id);

                    $response = Telegram::sendMessage([
                        'chat_id' => $new_telegram_id,
                        'text' => "Hello,\n\nWe received a request to validate GudangKu apps's account with username <b>$user->username</b> to sync with this Telegram account. If you initiated this request, please confirm that this account belongs to you by clicking the button YES.\n\nAlso we provided the Token :\n$token\n\nIf you did not request this, please press button NO.\n\nThank you, GudangKu",
                        'parse_mode' => 'HTML'
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("custom", 'telegram id updated! and validation has been sended to you'),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", 'user'),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("conflict", 'telegram ID'),
                ], Response::HTTP_CONFLICT);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/user/validate_telegram_id",
     *     summary="Validate telegram id change",
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="telegram id has been validated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="validation token is not valid"
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
    public function validate_telegram_id(Request $request){
        try{
            $user_id = $request->user()->id;
            $res = ValidateRequestModel::where('request_type','telegram_id_validation')
                ->where('created_by',$user_id)
                ->where('request_context',$request->request_context)
                ->delete();
            if($res > 0){
                $user = UserModel::find($user_id);
                UserModel::where('id', $user_id)
                    ->update([
                        'telegram_is_valid' => 1
                    ]);

                $response = Telegram::sendMessage([
                    'chat_id' => $user->telegram_user_id,
                    'text' => "Validation success.\nWelcome <b>{$user->username}</b>!,",
                    'parse_mode' => 'HTML'
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("custom", 'telegram id has been validated'),
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("custom", 'validation token is not valid'),
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