<?php

namespace App\Http\Controllers\Api\ReminderApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Kreait\Firebase\Factory;

// Model
use App\Models\ReminderModel;
use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\GoogleTokensModel;

// Helper
use App\Helpers\Generator;
use App\Helpers\Validation;
use App\Helpers\Firebase;
use App\Helpers\GoogleCalendar;

class Commands extends Controller
{
    private $module;
    private $max_size_file;
    private $allowed_file_type;

    public function __construct()
    {
        $this->module = "reminder";
        $this->max_size_file = 5000000; // 10 Mb
        $this->allowed_file_type = ['jpg','jpeg','gif','png'];
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/reminder/destroy/{id}",
     *     summary="Delete reminder by id",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Reminder ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="reminder permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="reminder permentally deleted")
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
     *         description="reminder failed to permentally deleted",
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
    public function hardDeleteReminderById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            $check_admin = AdminModel::find($user_id);
            if($check_admin){
                $user_id = null;
            }

            $old_reminder = ReminderModel::find($id);
            $rows = ReminderModel::hardDeleteReminderById($id, $user_id);
            if($rows > 0){
                // Delete Firebase Uploaded Image
                if($old_reminder->reminder_attachment){
                    $attachments = $old_reminder->reminder_attachment;
                    foreach ($attachments as $att) {
                        if ($att['attachment_type'] === 'image') {
                            $image_url = $att['attachment_value'];
                            if(!Firebase::deleteFile($image_url)){
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => Generator::getMessageTemplate("not_found", 'failed to delete reminder image'),
                                ], Response::HTTP_NOT_FOUND);
                            }
                            break;
                        }
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("permentally delete", $this->module),
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
     * @OA\POST(
     *     path="/api/v1/reminder",
     *     summary="Create a reminder",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="reminder created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="reminder created")
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
     *         response=400,
     *         description="reminder failed to validated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="[failed validation message]")
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
    public function postReminder(Request $request){
        try{
            $factory = (new Factory)->withServiceAccount(base_path('/firebase/myride-a0077-firebase-adminsdk-7x7j4-6b7da5321a.json'));
            $user_id = $request->user()->id;

            $validator = Validation::getValidateReminder($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $reminder_image = null;
                if ($request->hasFile('reminder_image')) {
                    $file = $request->file('reminder_image');
                    if ($file->isValid()) {
                        $file_ext = $file->getClientOriginalExtension();
                        // Validate file type
                        if (!in_array($file_ext, $this->allowed_file_type)) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("custom", 'The file must be a '.implode(', ', $this->allowed_file_type).' file type'),
                            ], Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                        // Validate file size
                        if ($file->getSize() > $this->max_size_file) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("custom", 'The file size must be under '.($this->max_size_file/1000000).' Mb'),
                            ], Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
        
                        // Helper: Upload reminder image
                        try {
                            $user = UserModel::find($user_id);
                            $reminder_image = Firebase::uploadFile('reminder', $user_id, $user->username, $file, $file_ext); 
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => Generator::getMessageTemplate("unknown_error", null),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }
                }

                $reminder_attachment = [];
                if ($reminder_image) {
                    $reminder_attachment[] = (object)[
                        'attachment_type' => 'image',
                        'attachment_value' => $reminder_image,
                    ];
                }
                if ($request->reminder_location) {
                    $reminder_attachment[] = (object)[
                        'attachment_type' => 'location',
                        'attachment_value' => $request->reminder_location,
                    ];
                }
                if (empty($reminder_attachment)) {
                    $reminder_attachment = null;
                }

                $data = [
                    'vehicle_id' => $request->vehicle_id, 
                    'reminder_title' => $request->reminder_title, 
                    'reminder_context' => $request->reminder_context,  
                    'reminder_body' => $request->reminder_body,  
                    'reminder_attachment' => $reminder_attachment,  
                    'remind_at' => $request->remind_at, 
                ];

                $rows = ReminderModel::createReminder($data, $user_id);
                if($rows){
                    $google_token = GoogleTokensModel::getGoogleTokensByUserId($user_id);
                    if($google_token){
                        $reminder_desc = "$request->reminder_context | $request->reminder_title\n$request->reminder_body";
                        GoogleCalendar::createSingleEvent($google_token->access_token, $reminder_desc, $request->remind_at, 60);
                    }
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("create", $this->module),
                    ], Response::HTTP_CREATED);
                } else {
                    return response()->json([
                        'status' => 'error',
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
