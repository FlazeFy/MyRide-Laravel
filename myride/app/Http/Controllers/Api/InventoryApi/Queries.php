<?php

namespace App\Http\Controllers\Api\InventoryApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Model
use App\Models\InventoryModel;
use App\Models\AdminModel;

// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "inventory";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/inventory",
     *     summary="Get all inventory",
     *     description="This request is used to get all inventory purchase history. This request is using MySql database, have a protected routes, and have template pagination.",
     *     tags={"Inventory"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Inventory records fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="inventory fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", example="6f59235e-c398-8a83-2f95-3f1fbe95ca6e"),
     *                         @OA\Property(property="inventory_name", type="string", example="Spare Tire"),
     *                         @OA\Property(property="inventory_category", type="string", example="Accessories"),
     *                         @OA\Property(property="inventory_qty", type="integer", example=2),
     *                         @OA\Property(property="inventory_storage", type="string", example="Trunk"),
     *                         @OA\Property(property="inventory_image_url", type="string", format="uri", example="https://example.com/uploads/inventory/spare_tire.jpg"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-20 22:53:47"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-21 09:15:12"),
     *                         @OA\Property(property="vehicle_plate_number", type="string", example="B 1234 CD"),
     *                         @OA\Property(property="vehicle_type", type="string", example="City Car"),
     *                     )
     *                 )
     *             )
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
     *         description="inventory failed to fetched",
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
    public function getAllInventory(Request $request)
    {
        try{
            $user_id = $request->user()->id;
            $check_admin = AdminModel::find($user_id);
            $paginate = $request->query('per_page_key') ?? 12;
            $vehicle_id = $request->query('vehicle_id') ?? null;

            $res = InventoryModel::getAllInventory($user_id, $vehicle_id, $paginate);
            
            if (count($res) > 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => $res
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
}
