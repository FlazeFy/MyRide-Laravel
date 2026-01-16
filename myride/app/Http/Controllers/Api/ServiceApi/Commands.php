<?php

namespace App\Http\Controllers\Api\ServiceApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Model
use App\Models\ServiceModel;
use App\Models\AdminModel;
use App\Models\HistoryModel;
// Helper
use App\Helpers\Generator;
use App\Helpers\Validation;

class Commands extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "service";
    }

    /**
     * @OA\POST(
     *     path="/api/v1/service",
     *     summary="Post Create Service",
     *     description="This request is used to create a service by using given `vehicle_id`, `service_note`, `service_category`, `service_location`, `service_price_total`, and `remind_at`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"vehicle_id", "service_note", "service_category", "service_location"},
     *              @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *              @OA\Property(property="service_note", type="string", example="Routine service KM 50.000"),
     *              @OA\Property(property="service_category", type="string", example="Routine"),
     *              @OA\Property(property="service_location", type="string", example="Honda Autobest"),
     *              @OA\Property(property="service_price_total", type="integer", example=4500000),
     *              @OA\Property(property="remind_at", type="string", format="date-time", example="2025-01-20 09:15:00"),
     *              @OA\Property(property="created_at", type="string", format="date-time", nullable=true, example="2025-01-20 09:15:00"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="service created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service created")
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
     *         description="service failed to validated",
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
    public function postService(Request $request){
        try{
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateService($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Create service
                $data = [
                    'vehicle_id' => $request->vehicle_id, 
                    'service_note' => $request->service_note, 
                    'service_category' => $request->service_category, 
                    'service_location' => $request->service_location, 
                    'service_price_total' => $request->service_price_total, 
                    'remind_at' => $request->remind_at, 
                    'created_at' => $request->created_at ?? date('Y-m-d H:i:s')
                ];
                $rows = ServiceModel::createService($data, $user_id);
                if($rows){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Service', 'history_context' => "added a service history"], $user_id);

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

    /**
     * @OA\DELETE(
     *     path="/api/v1/service/destroy/{id}",
     *     summary="Hard Delete Service By ID",
     *     description="This request is used to permanently delete a service based on the provided `ID`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Service ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="service permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service permentally deleted")
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
     *         description="service failed to permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="service not found")
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
    public function hardDeleteServiceById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Hard Delete service by ID
            $rows = ServiceModel::hardDeleteServiceById($id, $user_id);
            if($rows > 0){
                // Create history
                HistoryModel::createHistory(['history_type' => 'Service', 'history_context' => "removed a service history"], $user_id);

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
     * @OA\PUT(
     *     path="/api/v1/service/{id}",
     *     summary="Put Update Service By ID",
     *     description="This request is used to update service by using given `ID`. The updated field are `vehicle_id`, `service_note`, `service_category`, `service_location`, `service_price_total`, and `remind_at`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Service"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"vehicle_id", "service_note", "service_category", "service_location"},
     *              @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *              @OA\Property(property="service_note", type="string", example="Routine service KM 50.000"),
     *              @OA\Property(property="service_category", type="string", example="Routine"),
     *              @OA\Property(property="service_location", type="string", example="Honda Autobest"),
     *              @OA\Property(property="service_price_total", type="integer", example=4500000),
     *              @OA\Property(property="remind_at", type="string", format="date-time", example="2025-01-20 09:15:00"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="service updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="service updated")
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
     *         description="service failed to validated",
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
    public function putUpdateServiceById(Request $request, $id){
        try{
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateService($request,'update');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Update service by ID
                $data = [
                    'vehicle_id' => $request->vehicle_id, 
                    'service_note' => $request->service_note, 
                    'service_price_total' => $request->service_price_total, 
                    'service_location' => $request->service_location, 
                    'service_category' => $request->service_category, 
                    'remind_at' => $request->remind_at 
                ];
                $rows = ServiceModel::updateServiceById($data, $user_id, $id);
                if($rows > 0){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Service', 'history_context' => "edited a service history"], $user_id);

                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("update", $this->module),
                    ], Response::HTTP_OK);
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
