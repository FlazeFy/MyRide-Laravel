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
use App\Models\HistoryModel;
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
     *     summary="Hard Delete Reminder By ID",
     *     description="This request is used to permanently delete a reminder based on the provided `ID`. This request interacts with the MySQL database, firebase storage, has a protected routes, and audited activity (history).",
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

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get reminder by ID
            $old_reminder = ReminderModel::find($id);
            // Hard Delete reminder by ID
            $rows = ReminderModel::hardDeleteReminderById($id, $user_id);
            if($rows > 0){
                // Delete Firebase Uploaded Image
                if($old_reminder->reminder_attachment){
                    $attachments = $old_reminder->reminder_attachment;
                    foreach ($attachments as $att) {
                        if ($att['attachment_type'] === 'image') {
                            $image_url = $att['attachment_value'];
                            // Delete failed if file not found (already gone)
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

                // Create history
                HistoryModel::createHistory(['history_type' => 'Reminder', 'history_context' => "removed a reminder"], $user_id);

                // Return success response
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
     *     summary="Post Create Reminder",
     *     description="This request is used to create a reminder by using given `vehicle_id`, `reminder_title`, `reminder_context`, `reminder_body`, `reminder_location`, `reminder_image`, and `reminder_at`. This request interacts with the MySQL database, firebase storage, google calendar API, has a protected routes, and audited activity (history).",
     *     tags={"Reminder"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"vehicle_id", "reminder_title", "reminder_context", "reminder_body", "remind_at"},
     *                  @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *                  @OA\Property(property="reminder_title", type="string", example="Routine service KM 50000"),
     *                  @OA\Property(property="reminder_context", type="string", example="Service"),
     *                  @OA\Property(property="reminder_body", type="string", example="Lorem ipsum"),
     *                  @OA\Property(property="reminder_location", type="string", example="-6.230333799218126, 106.81866017790138"),
     *                  @OA\Property(property="reminder_image", type="string", format="binary"),
     *                  @OA\Property(property="remind_at", type="string", example="2025-09-05 00:00:00")
     *              )
     *          )
     *     ),
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
    public function postCreateReminder(Request $request){
        try{
            // Init firebase factory to use in Google Calendar
            $factory = (new Factory)->withServiceAccount(base_path('/firebase/myride-a0077-firebase-adminsdk-7x7j4-6b7da5321a.json'));
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateReminder($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $reminder_image = null;
                // Check if file attached
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
        
                        try {
                            // Get user data
                            $user = UserModel::getSocial($user_id);
                            // Upload file to Firebase storage
                            $reminder_image = Firebase::uploadFile('reminder', $user_id, $user->username, $file, $file_ext); 
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => Generator::getMessageTemplate("unknown_error", null),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }
                }

                // Build reminder array of object (store location or file attachment)
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
                // If reminder attachment empty make it null
                if (empty($reminder_attachment)) {
                    $reminder_attachment = null;
                }

                // Create reminder
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
                    // Get google token by user ID
                    $google_token = GoogleTokensModel::getGoogleTokensByUserId($user_id);
                    if($google_token){
                        // Sync reminder with user Google Calendar account
                        $reminder_desc = "$request->reminder_context | $request->reminder_title\n$request->reminder_body";
                        GoogleCalendar::createSingleEvent($google_token->access_token, $reminder_desc, $request->remind_at, 60);
                    }

                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Reminder', 'history_context' => "added a reminder"], $user_id);
                    
                    // Return success response
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
