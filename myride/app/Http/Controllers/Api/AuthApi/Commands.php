<?php

namespace App\Http\Controllers\Api\AuthApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// Models
use App\Models\UserModel;
use App\Models\AdminModel;
use App\Models\ValidateRequestModel;
// Helpers
use App\Helpers\Validation;
use App\Helpers\Generator;

// Mailer
use App\Jobs\UserJob;

/**
 * @OA\Info(
 *     title="MyRide",
 *     version="1.0.0",
 *     description="This document describes the MyRide API, built with Laravel (PHP), MySQL as the primary database, and Firebase for cloud storage and NoSQL data storage.",
 *     @OA\Contact(
 *         email="flazen.edu@gmail.com"
 *     )
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Authorization header using the Bearer scheme",
 * )
 */

class Commands extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/v1/login",
     *     summary="Post Login (Basic Auth)",
     *     description="This authentication request is used to access the application and obtain an authorization token for accessing all protected APIs. This request interacts with the MySQL database.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="flazefy"),
     *             @OA\Property(property="password", type="string", example="nopass123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="token", type="string", example="286|L5fqrLCDDCzPRLKngtm2FM9wq1IU2xFZSVAm10yp874a1a85"),
     *             @OA\Property(property="role", type="integer", example=1),
     *             @OA\Property(property="result", type="object",
     *                 @OA\Property(property="id", type="string", example="83ce75db-4016-d87c-2c3c-db1e222d0001"),
     *                 @OA\Property(property="username", type="string", example="flazefy"),
     *                 @OA\Property(property="email", type="string", example="flazen.edu@gmail.com"),
     *                 @OA\Property(property="telegram_user_id", type="string", example="123456789"),
     *                 @OA\Property(property="telegram_is_valid", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-14 02:28:37"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-25 09:37:20"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="{validation_msg}",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="result", type="string", example="{field validation message}")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="account is not found or have wrong password",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="result", type="string", example="wrong username or password")
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
    public function login(Request $request)
    {
        try {
            // Validate request body
            $validator = Validation::getValidateLogin($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'result' => $validator->messages(),
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Check for Admin account
                $user = AdminModel::where('username', $request->username)->first();
                $role = 1;
                if($user == null){
                    // Check for User account
                    $user = UserModel::where('username', $request->username)->first();
                    $role = 0;

                    if($user){
                        // Verify that the account has completed registration validation
                        $check_register = ValidateRequestModel::getCheckRegisterToken($request->username);
                        if($check_register){
                            return response()->json([
                                'status' => 'failed',
                                'result' => 'your account is not validated yet, check your email and validate again',
                            ], Response::HTTP_UNAUTHORIZED);
                        }
                    }
                }

                // Verify username and password
                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json([
                        'status' => 'failed',
                        'result' => 'wrong username or password',
                    ], Response::HTTP_UNAUTHORIZED);
                } else {
                    // Create Token
                    $token = $user->createToken('login')->plainTextToken;
                    unset($user->password);

                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'result' => $user,
                        'token' => $token,  
                        'role' => $role                  
                    ], Response::HTTP_OK);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/v1/register/token",
     *     summary="Post Register Token",
     *     description="This authentication request is used to get token validation after register. This request interacts with the MySQL database and using mailer.",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="the validation token has been sended to {email} email account",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="the validation token has been sended to flazen.edu@gmail.com email account")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="there already a request with same username / username has been used. try another",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="there already a request with same username")
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
    public function get_register_validation_token(Request $request)
    {
        try{
            // Check if account exist by username
            $username = $request->username;
            $check_user = UserModel::selectRaw('1')
                ->where('username',$username)
                ->first();

            if(!$check_user){
                // Check if request doesnt duplicate
                $valid = ValidateRequestModel::selectRaw('1')
                    ->where('request_type','register')
                    ->where('created_by',$username)
                    ->first();

                if(!$valid){
                    // Generate token
                    $token_length = 6;
                    $token = Generator::getTokenValidation($token_length);
                    $data_req = (object)[
                        'request_type' => 'register',
                        'request_context' => $token
                    ];
                    $valid_insert = ValidateRequestModel::createValidateRequest($data_req, $username);

                    if($valid_insert){
                        // Send token email
                        $ctx = 'Generate registration token';
                        $email = $request->email;
                        $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";
                        dispatch(new UserJob($ctx, $data, $username, $email));

                        // Return success response
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("custom", "the validation token has been sended to $email email account"),
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("custom", 'there already a request with same username'),
                    ], Response::HTTP_CONFLICT);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("conflict", 'username'),
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
     * @OA\POST(
     *     path="/api/v1/register/account",
     *     summary="Post Register Account",
     *     description="This authentication request is used to get token validation after register. This request interacts with the MySQL database and using mailer.",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="account is registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="account is registered"),
     *             @OA\Property(property="is_signed_in", type="bool", example=true),
     *             @OA\Property(property="token", type="string", example="123456ABCD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Token is invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="token is invalid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="username already used",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="username has been used. try another")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="{validation_msg}",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="{field validation message}")
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
    public function post_validate_register(Request $request)
    {
        try{
            // Validate request body
            $validator = Validation::getValidateUser($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $username = $request->username;

                // Verify that the account has valid registration validation
                $valid = ValidateRequestModel::selectRaw('id')
                    ->where('request_type','register')
                    ->where('request_context',$request->token)
                    ->where('created_by',$username)
                    ->first();

                if($valid){
                    // Verify the username not duplicated
                    $check_user = UserModel::selectRaw('1')
                        ->where('username',$username)
                        ->first();

                    if(!$check_user){
                        // Delete request after validation
                        ValidateRequestModel::destroy($valid->id);

                        // Create user
                        $data = (object)[
                            'username' => $request->username,
                            'password' => $request->password,
                            'email' => $request->email,
                            'telegram_user_id' => $request->telegram_user_id
                        ];
                        $user = UserModel::createUser($data);

                        if($user){
                            // Send email
                            $ctx = 'Register new account';
                            $email = $request->email;
                            $data = "Welcome to MyRide, happy explore!";
                            dispatch(new UserJob($ctx, $data, $username, $email));

                            // Check this ....
                            if(Hash::check($request->password, $user->password)){
                                $token = $user->createToken('login')->plainTextToken;

                                // Return success response
                                return response()->json([
                                    'is_signed_in' => true,
                                    'token' => $token,
                                    'status' => 'success',
                                    'message' => Generator::getMessageTemplate("custom", "account is registered"),
                                ], Response::HTTP_OK);   
                            } else {
                                // Return success response
                                return response()->json([
                                    'is_signed_in' => false,
                                    'status' => 'success',
                                    'message' => Generator::getMessageTemplate("custom", "account is registered"),
                                ], Response::HTTP_OK);   
                            }
                        } else {
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("unknown_error", null),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("conflict", 'username'),
                        ], Response::HTTP_CONFLICT);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("custom", 'token is invalid'),
                    ], Response::HTTP_NOT_FOUND);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/v1/register/regen_token",
     *     summary="Post Regenerate Registration Token",
     *     description="This authentication request is used to regenerate a token after user has failed to validate their previous token. This request interacts with the MySQL database and using mailer.",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="the validation token has been sended to {email} email account",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="the validation token has been sended to flazen.edu@gmail.com email account")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="request not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="the validation token has been sended to flazen.edu@gmail.com email account")
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
    public function regenerate_register_token(Request $request)
    {
        try{
            $username = $request->username;

            // Check if request valid
            $valid = ValidateRequestModel::select('id')
                ->where('request_type','register')
                ->where('created_by',$username)
                ->first();

            // Generate token
            $token_length = 6;
            $token = Generator::getTokenValidation($token_length);

            if($valid){
                // If token still fresh but user request a new one
                // Delete request after validation
                $delete = ValidateRequestModel::destroy($valid->id);

                if($delete > 0){
                    // Create register token
                    $valid_insert = ValidateRequestModel::createValidateRequest('register', $token, $username);

                    if($valid_insert){
                        // Send email regenerate token
                        $ctx = 'Generate registration token';
                        $email = $request->email;
                        $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";
                        dispatch(new UserJob($ctx, $data, $username, $email));

                         // Return success response
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("custom", "the validation token has been sended to $email email account"),
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => Generator::getMessageTemplate("not_found", 'request'),
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                // If token already deleted / expired
                // Create register token
                $valid_insert = ValidateRequestModel::createValidateRequest('register', $token, $username);

                if($valid_insert){
                    // Send email regenerate token
                    $ctx = 'Generate registration token';
                    $email = $request->email;
                    $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";
                    dispatch(new UserJob($ctx, $data, $username, $email));

                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("custom", "the validation token has been sended to $email email account"),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("unknown_error", null),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
