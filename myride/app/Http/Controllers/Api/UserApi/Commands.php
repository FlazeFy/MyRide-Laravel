<?php

namespace App\Http\Controllers\Api\UserApi;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

// Telegram
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"telegram_user_id"},
     *             @OA\Property(property="telegram_user_id", type="string", example="1234567890"),
     *         )
     *     ),
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
                        'text' => "Hello,\n\nWe received a request to validate MyRide apps's account with username <b>$user->username</b> to sync with this Telegram account. If you initiated this request, please confirm that this account belongs to you by clicking the button YES.\n\nAlso we provided the Token :\n$token\n\nIf you did not request this, please press button NO.\n\nThank you, MyRide",
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"request_context"},
     *             @OA\Property(property="request_context", type="string", example="A1B2C3"),
     *         )
     *     ),
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

    /**
     * @OA\PUT(
     *     path="/api/v1/user/update_profile",
     *     summary="Update profile",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","username"},
     *             @OA\Property(property="telegram_user_id", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", example="tester@gmail.com"),
     *             @OA\Property(property="username", type="string", example="flazefy"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="profile updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="profile updated")
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
     *         description="username / email has been used",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="username or email has been used. try another")
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
    public function update_profile(Request $request)
    {
        try{
            $user_id = $request->user()->id;

            $validator = Validation::getValidateUser($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $check = UserModel::selectRaw('1')
                    ->where(function ($query) use ($request) {
                        $query->where('email', $request->email)
                            ->orWhere('username', $request->username);
                    })
                    ->where('id', '!=', $user_id)
                    ->first();

                if($check == null){
                    $is_telegram_updated = false;
                    $old_data = UserModel::find($user_id);
                    $extra_msg = "";

                    $res = UserModel::where('id',$user_id)
                        ->update([
                            'email' => $request->email,
                            'username' => $request->username
                        ]);

                    // If there is a change in telegram ID
                    if($old_data->telegram_user_id != $request->telegram_user_id){
                        $is_telegram_updated = true;
                        $token_length = 6;
                        $token = Generator::getTokenValidation($token_length);

                        // Create new token validation
                        $create_new_req = ValidateRequestModel::create([
                            'id' => Generator::getUUID(), 
                            'request_type' => 'telegram_id_validation',
                            'request_context' => $token, 
                            'created_at' => date('Y-m-d H:i:s'), 
                            'created_by' => $user_id
                        ]);

                        // Response
                        if($create_new_req){
                            $res_update_user_token = UserModel::where('id',$user_id)
                                ->update([
                                    'telegram_user_id' => $request->telegram_user_id,
                                    'telegram_is_valid' => 0
                                ]);

                            if($res_update_user_token > 0){
                                $extra_msg = " and telegram id updated! and validation has been sended to you";
                            }
                        }

                        $response = Telegram::sendMessage([
                            'chat_id' => $new_telegram_id,
                            'text' => "Hello,\n\nWe received a request to validate MyRide apps's account with username <b>$old_data->username</b> to sync with this Telegram account. If you initiated this request, please confirm that this account belongs to you by clicking the button YES.\n\nAlso we provided the Token :\n$token\n\nIf you did not request this, please press button NO.\n\nThank you, MyRide",
                            'parse_mode' => 'HTML'
                        ]);
                    }
                    
                    if ($res) {
                        $user = UserModel::getSocial($user_id);

                        if($user->telegram_is_valid == 1 && !$is_telegram_updated){
                            $response = Telegram::sendMessage([
                                'chat_id' => $user->telegram_user_id,
                                'text' => "Hello,\n\nYour profile has been updated$extra_msg",
                                'parse_mode' => 'HTML'
                            ]);
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("update", 'profile'),
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
                        'message' => Generator::getMessageTemplate("conflict", 'email or username'),
                    ], Response::HTTP_CONFLICT);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}