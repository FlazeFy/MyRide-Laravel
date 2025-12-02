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
 *     description="API Documentation for MyRide",
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
     *     summary="Sign in to the Apps",
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
            $validator = Validation::getValidateLogin($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'status' => 'failed',
                    'result' => $errors,
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Check for Admin
                $user = AdminModel::where('username', $request->username)->first();
                $role = 1;
                if($user == null){
                    // Check for User
                    $user = UserModel::where('username', $request->username)->first();
                    $role = 0;

                    if($user){
                        $check_register = ValidateRequestModel::getCheckRegisterToken($request->username);
                        if($check_register){
                            return response()->json([
                                'status' => 'failed',
                                'result' => 'your account is not validated yet, check your email and validate again',
                            ], Response::HTTP_UNAUTHORIZED);
                        }
                    }
                }

                // Response
                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json([
                        'status' => 'failed',
                        'result' => 'wrong username or password',
                    ], Response::HTTP_UNAUTHORIZED);
                } else {
                    $token = $user->createToken('login')->plainTextToken;
                    unset($user->password);

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
     *     path="/api/v1/register",
     *     summary="Register an account",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "email"},
     *             @OA\Property(property="username", type="string", example="flazefy"),
     *             @OA\Property(property="password", type="string", example="nopass123"),
     *             @OA\Property(property="email", type="string", example="tester@gmail.com"),
     *             @OA\Property(property="telegram_user_id", type="string", example="1317625123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="register successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="user has been created"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="83ce75db-4016-d87c-2c3c-db1e222d0001"),
     *                 @OA\Property(property="username", type="string", example="flazefy"),
     *                 @OA\Property(property="email", type="string", example="flazen.work@gmail.com"),
     *                 @OA\Property(property="telegram_user_id", type="string", example="123456789"),
     *                 @OA\Property(property="telegram_is_valid", type="integer", example=0),
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
     *         response=409,
     *         description="user with same email or username had been registered, try using another",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="user with same email or username has been used")
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
    public function register(Request $request)
    {
        try {
            $validator = Validation::getValidateRegister($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'status' => 'failed',
                    'data' => $errors,
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $username = $request->username;
                $password = $request->password;
                $email = $request->email;
                $telegram_user_id = $request->telegram_user_id;

                // Check If Password & Password Confirmation is same
                if($password == $request->confirm_password){
                    // Check username or email avaiability
                    $is_waiting_validate_register = ValidateRequestModel::getCheckRegisterToken($username);                    
                    if(!$is_waiting_validate_register){
                        // Create User
                        $is_exist = UserModel::getCheckUserByUsernameAndEmail($username, $email);
                        if(!$is_exist){
                            $data = [
                                'username' => $username,
                                'password' => $password,
                                'email' => $email,
                                'telegram_user_id' => $telegram_user_id
                            ];
                            $user = UserModel::createUser((object)$data);

                            if($user){
                                // Create Token
                                $token_length = 6;
                                $token = Generator::getTokenValidation($token_length);
                                $valid_req = [
                                    'request_context' => $token,
                                    'request_type' => 'register',
                                ];
                                $validate_req = ValidateRequestModel::createValidateRequest((object)$valid_req, $user->username);

                                if($validate_req){
                                    // Send email
                                    $ctx = 'Generate registration token';
                                    $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";

                                    dispatch(new UserJob($ctx, $data, $username, $email));

                                    return response()->json([
                                        'status' => 'success',
                                        'message' => 'user has been created',
                                        'data' => $user,                
                                    ], Response::HTTP_CREATED);
                                } else {
                                    return response()->json([
                                        'status' => 'error',
                                        'message' => Generator::getMessageTemplate("unknown_error", null),
                                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                                }
                            } else {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => Generator::getMessageTemplate("unknown_error", null),
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }
                        } else {
                            return response()->json([
                                'status' => 'failed',
                                'message' => 'user with same email or username has been used',             
                            ], Response::HTTP_CONFLICT);
                        }
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'user has been registered but need validate first',           
                        ], Response::HTTP_CONFLICT);
                    }   
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'password confirmation must same',
                    ], Response::HTTP_BAD_REQUEST);
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
     *     path="/api/v1/register/validate",
     *     summary="Validate an account register's request",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "token"},
     *             @OA\Property(property="username", type="string", example="flazefy"),
     *             @OA\Property(property="token", type="string", example="ABC123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="validation successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="user has been validated")
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
     *         response=404,
     *         description="token is invalid or user request not found or has been accepted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="token is invalid or request not found")
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
    public function validate_register(Request $request){
        try {
            $validator = Validation::getValidateRegisterValidation($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'status' => 'failed',
                    'data' => $errors,
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $username = $request->username;
                $token = $request->token;

                $valid_req = ValidateRequestModel::getActiveRequestByCreatedByTokenAndType($username,$token,'register');                    
                if($valid_req){
                    // Delete Request
                    $is_deleted = ValidateRequestModel::destroy($valid_req);
                    if($is_deleted){
                        $user = UserModel::where('username',$username)->first();

                        // Send email
                        $ctx = 'Registration Complete!';
                        $data = "You have finished register your account. Welcome to MyRide $username, happy explore the world";

                        dispatch(new UserJob($ctx, $data, $username, $user->email));

                        return response()->json([
                            'status' => 'success',
                            'message' => 'user has been validated',
                        ], Response::HTTP_OK);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'token is invalid or request not found',           
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
     *     path="/api/v1/register/token",
     *     summary="Check and send validation token to the user who in registration process",
     *     tags={"User"},
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
            $username = $request->username;
            $check_user = UserModel::selectRaw('1')
                ->where('username',$username)
                ->first();

            if(!$check_user){
                $valid = ValidateRequestModel::selectRaw('1')
                    ->where('request_type','register')
                    ->where('created_by',$username)
                    ->first();

                if(!$valid){
                    $token_length = 6;
                    $token = Generator::getTokenValidation($token_length);

                    $data_req = (object)[
                        'request_type' => 'register',
                        'request_context' => $token
                    ];
                    $valid_insert = ValidateRequestModel::createValidateRequest($data_req, $username);

                    if($valid_insert){
                        // Send email
                        $ctx = 'Generate registration token';
                        $email = $request->email;
                        $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";

                        dispatch(new UserJob($ctx, $data, $username, $email));

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
     *     summary="Register account and accept validation",
     *     tags={"User"},
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
            $validator = Validation::getValidateUser($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $username = $request->username;
                $valid = ValidateRequestModel::selectRaw('id')
                    ->where('request_type','register')
                    ->where('request_context',$request->token)
                    ->where('created_by',$username)
                    ->first();

                if($valid){
                    $check_user = UserModel::selectRaw('1')
                        ->where('username',$username)
                        ->first();

                    if(!$check_user){
                        ValidateRequestModel::destroy($valid->id);

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

                            if(Hash::check($request->password, $user->password)){
                                $token = $user->createToken('login')->plainTextToken;

                                return response()->json([
                                    'is_signed_in' => true,
                                    'token' => $token,
                                    'status' => 'success',
                                    'message' => Generator::getMessageTemplate("custom", "account is registered"),
                                ], Response::HTTP_OK);   
                            } else {
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
     *     summary="Regenerate registration token",
     *     tags={"User"},
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
            $valid = ValidateRequestModel::select('id')
                ->where('request_type','register')
                ->where('created_by',$username)
                ->first();

            $token_length = 6;
            $token = Generator::getTokenValidation($token_length);

            if($valid){
                $delete = ValidateRequestModel::destroy($valid->id);

                if($delete > 0){
                    $valid_insert = ValidateRequestModel::createValidateRequest('register', $token, $username);

                    if($valid_insert){
                        // Send email
                        $ctx = 'Generate registration token';
                        $email = $request->email;
                        $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";

                        dispatch(new UserJob($ctx, $data, $username, $email));

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
                // already deleted
                $valid_insert = ValidateRequestModel::createValidateRequest('register', $token, $username);

                if($valid_insert){
                    // Send email
                    $ctx = 'Generate registration token';
                    $email = $request->email;
                    $data = "You almost finish your registration process. We provided you with this token <br><h5>$token</h5> to make sure this account is yours.<br>If you're the owner just paste this token into the Token's Field. If its not, just leave this message<br>Thank You, MyRide";

                    dispatch(new UserJob($ctx, $data, $username, $email));

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
