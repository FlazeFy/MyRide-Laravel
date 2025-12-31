<?php

namespace App\Http\Controllers\Api\WashApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Model
use App\Models\WashModel;
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
        $this->module = "wash";
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/wash/destroy/{id}",
     *     summary="Hard Delete Wash By ID",
     *     description="This request is used to permanently delete a wash history based on the provided `ID`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Wash ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="wash permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash permentally deleted")
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
     *         description="wash failed to permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="wash not found")
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
    public function hardDeleteWashById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Hard Delete wash by ID
            $rows = WashModel::hardDeleteWashById($id, $user_id);
            if($rows > 0){
                // Create history
                HistoryModel::createHistory(['history_type' => 'Wash', 'history_context' => "removed a wash history"], $user_id);
                
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
     *     path="/api/v1/wash",
     *     summary="Post Create Wash",
     *     description="This request is used to create a wash history based on the provided `vehicle_id`, `wash_desc`, `wash_by`, `is_wash_body`, `is_wash_window`, `is_wash_dashboard`, `is_wash_tires`, `is_wash_trash`, `is_wash_engine`, `is_wash_seat`, `is_wash_carpet`, `is_wash_pillows`, `wash_address`, `wash_start_time`, `wash_end_time`, `wash_price`, `is_fill_window_washing_water`, and `is_wash_hollow`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",    
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"vehicle_id", "wash_by", "is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "wash_start_time", "is_fill_window_washing_water","is_wash_hollow"},
     *              @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *              @OA\Property(property="wash_desc", type="string", example="Full body and interior wash"),
     *              @OA\Property(property="wash_by", type="string", example="Car Wash"),
     *              @OA\Property(property="is_wash_body", type="boolean", example=true),
     *              @OA\Property(property="is_wash_window", type="boolean", example=true),
     *              @OA\Property(property="is_wash_dashboard", type="boolean", example=false),
     *              @OA\Property(property="is_wash_tires", type="boolean", example=true),
     *              @OA\Property(property="is_wash_trash", type="boolean", example=true),
     *              @OA\Property(property="is_wash_engine", type="boolean", example=false),
     *              @OA\Property(property="is_wash_seat", type="boolean", example=true),
     *              @OA\Property(property="is_wash_carpet", type="boolean", example=true),
     *              @OA\Property(property="is_wash_pillows", type="boolean", example=false),
     *              @OA\Property(property="wash_address", type="string", example="Jl. Raya No. 12"),
     *              @OA\Property(property="wash_start_time", type="string", format="date-time", example="2025-12-16T14:00:00Z"),
     *              @OA\Property(property="wash_end_time", type="string", format="date-time", example="2025-12-16T15:30:00Z"),
     *              @OA\Property(property="wash_price", type="integer", example=150000),
     *              @OA\Property(property="is_fill_window_washing_water", type="boolean", example=true),
     *              @OA\Property(property="is_wash_hollow", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="wash created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash created")
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
     *         description="wash failed to validated",
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
    public function postCreateWash(Request $request){
        try{
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateWash($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Create wash
                $data = [
                    'vehicle_id' => $request->vehicle_id, 
                    'wash_desc' => $request->wash_desc, 
                    'wash_by' => $request->wash_by, 
                    'is_wash_body' => $request->is_wash_body,
                    'is_wash_window' => $request->is_wash_window,
                    'is_wash_dashboard' => $request->is_wash_dashboard,
                    'is_wash_tires' => $request->is_wash_tires,
                    'is_wash_trash' => $request->is_wash_trash,
                    'is_wash_engine' => $request->is_wash_engine,
                    'is_wash_seat' => $request->is_wash_seat,
                    'is_wash_carpet' => $request->is_wash_carpet,
                    'is_wash_pillows' => $request->is_wash_pillows,
                    'wash_address' => $request->wash_address,
                    'wash_start_time' => $request->wash_start_time,
                    'wash_end_time' => $request->wash_end_time,
                    'wash_price' => $request->wash_price,
                    'is_fill_window_washing_water' => $request->is_fill_window_washing_water,
                    'is_wash_hollow' => $request->is_wash_hollow
                ];
                $rows = WashModel::createWash($data, $user_id);
                if($rows){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Wash', 'history_context' => "added a wash history"], $user_id);

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
     * @OA\PUT(
     *     path="/api/v1/wash/finish/{id}",
     *     summary="Put Update Wash Finish Status By ID",
     *     description="This request is used to update a wash history based on the provided `ID` and the updated fields `wash_end_time`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",     
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"wash_end_time"},
     *              @OA\Property(property="wash_end_time", type="string", format="date-time", example="2025-12-16T15:30:00Z")
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="wash updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash updated")
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
     *         description="wash failed to validated",
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
    public function putFinishWashById(Request $request,$id){
        try{
            $user_id = $request->user()->id;

            // Update wash by ID
            $rows = WashModel::updateWashById(['wash_end_time' => date('Y-m-d H:i:s')], $user_id, $id);
            if($rows > 0){
                // Return success response
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("update", $this->module),
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/v1/wash/{id}",
     *     summary="Put Update Wash By ID",
     *     description="This request is used to update a wash history based on the provided `ID`. The updated fields are `vehicle_id`, `wash_desc`, `wash_by`, `is_wash_body`, `is_wash_window`, `is_wash_dashboard`, `is_wash_tires`, `is_wash_trash`, `is_wash_engine`, `is_wash_seat`, `is_wash_carpet`, `is_wash_pillows`, `wash_address`, `wash_start_time`, `wash_end_time`, `wash_price`, `is_fill_window_washing_water`, and `is_wash_hollow`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Wash"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"vehicle_id", "wash_by", "is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "wash_start_time", "is_fill_window_washing_water","is_wash_hollow"},
     *              @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *              @OA\Property(property="wash_desc", type="string", example="Full body and interior wash"),
     *              @OA\Property(property="wash_by", type="string", example="Car Wash"),
     *              @OA\Property(property="is_wash_body", type="boolean", example=true),
     *              @OA\Property(property="is_wash_window", type="boolean", example=true),
     *              @OA\Property(property="is_wash_dashboard", type="boolean", example=false),
     *              @OA\Property(property="is_wash_tires", type="boolean", example=true),
     *              @OA\Property(property="is_wash_trash", type="boolean", example=true),
     *              @OA\Property(property="is_wash_engine", type="boolean", example=false),
     *              @OA\Property(property="is_wash_seat", type="boolean", example=true),
     *              @OA\Property(property="is_wash_carpet", type="boolean", example=true),
     *              @OA\Property(property="is_wash_pillows", type="boolean", example=false),
     *              @OA\Property(property="wash_address", type="string", example="Jl. Raya No. 12"),
     *              @OA\Property(property="wash_start_time", type="string", format="date-time", example="2025-12-16 14:00:00"),
     *              @OA\Property(property="wash_end_time", type="string", format="date-time", example="2025-12-16 15:30:00"),
     *              @OA\Property(property="wash_price", type="integer", example=150000),
     *              @OA\Property(property="is_fill_window_washing_water", type="boolean", example=true),
     *              @OA\Property(property="is_wash_hollow", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="wash updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="wash updated")
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
     *         description="wash failed to validated",
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
    public function putUpdateWashById(Request $request,$id){
        try{
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateWash($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Update wash by ID
                $data = [
                    'vehicle_id' => $request->vehicle_id, 
                    'wash_desc' => $request->wash_desc, 
                    'wash_by' => $request->wash_by, 
                    'is_wash_body' => $request->is_wash_body,
                    'is_wash_window' => $request->is_wash_window,
                    'is_wash_dashboard' => $request->is_wash_dashboard,
                    'is_wash_tires' => $request->is_wash_tires,
                    'is_wash_trash' => $request->is_wash_trash,
                    'is_wash_engine' => $request->is_wash_engine,
                    'is_wash_seat' => $request->is_wash_seat,
                    'is_wash_carpet' => $request->is_wash_carpet,
                    'is_wash_pillows' => $request->is_wash_pillows,
                    'wash_address' => $request->wash_address,
                    'wash_start_time' => $request->wash_start_time,
                    'wash_end_time' => $request->wash_end_time,
                    'wash_price' => $request->wash_price,
                    'is_fill_window_washing_water' => $request->is_fill_window_washing_water,
                    'is_wash_hollow' => $request->is_wash_hollow
                ];
                $rows = WashModel::updateWashById($data, $user_id, $id);
                if($rows > 0){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Wash', 'history_context' => "edited a wash history"], $user_id);

                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("update", $this->module),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", $this->module),
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
}
