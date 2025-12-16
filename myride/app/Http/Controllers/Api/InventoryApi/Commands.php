<?php

namespace App\Http\Controllers\Api\InventoryApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Model
use App\Models\InventoryModel;
use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\HistoryModel;
// Helper
use App\Helpers\Generator;
use App\Helpers\Validation;
use App\Helpers\Firebase;

class Commands extends Controller
{
    private $module;
    private $max_size_file;
    private $allowed_file_type;

    public function __construct()
    {
        $this->module = "inventory";
        $this->max_size_file = 5000000; // 5 Mb
        $this->allowed_file_type = ['jpg','jpeg','gif','png'];
    }

    /**
     * @OA\DELETE(
     *     path="/api/v1/inventory/destroy/{id}",
     *     summary="Delete Inventory By ID",
     *     description="This request is used to permanently delete an inventory based on the provided `ID`. This request interacts with the MySQL database, firebase storage (for remove uploaded `inventory_image_url`), has a protected routes, and audited activity (history).",
     *     tags={"Inventory"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Inventory ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="inventory permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="inventory permentally deleted")
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
     *         description="inventory failed to permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="inventory not found")
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
    public function hardDeleteInventoryById(Request $request, $id)
    {
        try{
            $user_id = $request->user()->id;

            // Define user id by role
            $check_admin = AdminModel::find($user_id);
            $user_id = $check_admin ? null : $user_id;

            // Get inventory data
            $inventory = InventoryModel::find($id);

            // Hard Delete inventory by ID
            $rows = InventoryModel::hardDeleteInventoryById($id, $user_id);
            if($rows > 0){
                // Delete Firebase uploaded image
                if($inventory->inventory_image_url){
                    // Delete failed if file not found (already gone)
                    if(!Firebase::deleteFile($inventory->inventory_image_url)){
                        return response()->json([
                            'status' => 'failed',
                            'message' => Generator::getMessageTemplate("not_found", 'failed to delete inventory image'),
                        ], Response::HTTP_NOT_FOUND);
                    }
                }

                // Create history
                HistoryModel::createHistory(['history_type' => 'Inventory', 'history_context' => "remove an inventory"], $user_id);

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
     *     path="/api/v1/inventory",
     *     summary="Post Create Inventory",
     *     description="This request is used to create an inventory by using given `vehicle_id`, `inventory_name`, `inventory_category`, `inventory_storage`, `inventory_qty`, and `inventory_image_url`. This request interacts with the MySQL database, firebase storage, has a protected routes, and audited activity (history).",
     *     tags={"Inventory"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"vehicle_id", "inventory_name", "inventory_category", "inventory_storage", "inventory_qty"},
     *                  @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *                  @OA\Property(property="inventory_name", type="string", example="Secondary Tire"),
     *                  @OA\Property(property="inventory_category", type="string", example="Vehicle Component"),
     *                  @OA\Property(property="inventory_storage", type="string", example="Trunk"),
     *                  @OA\Property(property="inventory_qty", type="integer", example=1),
     *                  @OA\Property(property="inventory_image_url", type="string", format="binary"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="inventory created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="inventory created")
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
     *         description="inventory failed to validated",
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
    public function postInventory(Request $request){
        try{
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateInventory($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $inventory_image = null;
                // Check if file attached
                if ($request->hasFile('inventory_image_url')) {
                    $file = $request->file('inventory_image_url');
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
                            $inventory_image = Firebase::uploadFile('inventory', $user_id, $user->username, $file, $file_ext); 
                        } catch (\Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => Generator::getMessageTemplate("unknown_error", null),
                            ], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }
                }

                // Create inventory
                $data = [
                    'gudangku_inventory_id' => $request->gudangku_inventory_id, 
                    'vehicle_id' => $request->vehicle_id, 
                    'inventory_name' => $request->inventory_name, 
                    'inventory_category' => $request->inventory_category, 
                    'inventory_qty' => $request->inventory_qty, 
                    'inventory_storage' => $request->inventory_storage, 
                    'inventory_image_url' => $inventory_image
                ];
                $rows = InventoryModel::createInventory($data, $user_id);
                if($rows){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Inventory', 'history_context' => "added an inventory"], $user_id);

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
     *     path="/api/v1/inventory/{id}",
     *     summary="Put Update Inventory By ID",
     *     description="This request is used to update an inventory by using given `ID`. The updated field are `vehicle_id`, `inventory_name`, `inventory_category`, `inventory_storage`, and `inventory_qty`. This request interacts with the MySQL database, has a protected routes, and audited activity (history).",
     *     tags={"Inventory"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"vehicle_id", "inventory_name", "inventory_category", "inventory_storage", "inventory_qty"},
     *              @OA\Property(property="vehicle_id", type="string", example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9"),
     *              @OA\Property(property="inventory_name", type="string", example="Secondary Tire"),
     *              @OA\Property(property="inventory_category", type="string", example="Vehicle Component"),
     *              @OA\Property(property="inventory_storage", type="string", example="Trunk"),
     *              @OA\Property(property="inventory_qty", type="integer", example=1),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="inventory updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="inventory updated")
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
     *         description="inventory failed to validated",
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
    public function updateInventory(Request $request, $id){
        try{
            $user_id = $request->user()->id;

            // Validate request body
            $validator = Validation::getValidateInventory($request,'update');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Update inventory by ID
                $data = [
                    'vehicle_id' => $request->vehicle_id, 
                    'inventory_name' => $request->inventory_name, 
                    'inventory_category' => $request->inventory_category, 
                    'inventory_qty' => $request->inventory_qty, 
                    'inventory_storage' => $request->inventory_storage 
                ];
                $rows = InventoryModel::updateInventoryById($data, $user_id, $id);
                if($rows > 0){
                    // Create history
                    HistoryModel::createHistory(['history_type' => 'Inventory', 'history_context' => "edited an inventory"], $user_id);

                    // Return success response
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("update", $this->module),
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
