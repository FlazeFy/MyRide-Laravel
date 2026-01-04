<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Inventory",
 *     type="object",
 *     required={"id","vehicle_id","inventory_name","inventory_category","inventory_qty","inventory_storage","created_at","created_by"},
 *
 *     @OA\Property(property="id", type="integer", description="Primary key of the inventory record"),
 *     @OA\Property(property="gudangku_inventory_id", type="integer", nullable=true, description="Reference ID from MyRide system"),
 *     @OA\Property(property="vehicle_id", type="integer", description="Reference ID of the vehicle"),
 *     @OA\Property(property="inventory_name", type="string", description="Name of the inventory item"),
 *     @OA\Property(property="inventory_category", type="string", description="Category of the inventory item"),
 *     @OA\Property(property="inventory_qty", type="integer", description="Quantity of the inventory item"),
 *     @OA\Property(property="inventory_storage", type="string", description="Storage location of the inventory item"),
 *     @OA\Property(property="inventory_image_url", type="string", format="uri", nullable=true, description="URL to the inventory item image"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the inventory record was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the inventory record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the inventory record was last updated")
 * )
 */

class InventoryModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'inventory';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'gudangku_inventory_id', 'vehicle_id', 'inventory_name', 'inventory_category', 'inventory_qty', 'inventory_storage', 'inventory_image_url', 'created_at', 'created_by', 'updated_at'];
    protected $casts = [
        'inventory_qty' => 'integer',
    ];

    public static function getAllInventory($user_id = null, $limit){
        $res = InventoryModel::select('inventory.id','inventory_name', 'inventory_category', 'inventory_qty', 'inventory_storage', 'inventory_image_url', 'inventory.created_at', 'inventory.updated_at', 'vehicle_plate_number','vehicle_type')
            ->leftjoin('vehicle','vehicle.id','=','inventory.vehicle_id');

        if($user_id){
            $res = $res->where('inventory.created_by', $user_id);
        }
            
        return $res->orderByRaw('COALESCE(inventory.updated_at, inventory.created_at) DESC')->paginate($limit);                       
    }

    public static function hardDeleteInventoryById($id, $user_id = null){
        $res = InventoryModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = InventoryModel::where('vehicle_id',$vehicle_id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = InventoryModel::select("vehicle_name","vehicle_plate_number", "vehicle_type", 'inventory_name', 'inventory_category', 'inventory_qty', 'inventory_storage', 'inventory.created_at', 'inventory.updated_at')
            ->join('vehicle','vehicle.id','=','inventory.vehicle_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('inventory.created_by',$user_id)
            ->orderBy('inventory.created_at', 'desc');

        return $res->get();
    }

    public static function getInventoryByVehicle($user_id, $vehicle_id){
        $res = InventoryModel::select('inventory.id','inventory_name', 'inventory_category', 'inventory_qty', 'inventory_storage', 'inventory.created_at');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('inventory.created_by',$user_id)
            ->orderBy('inventory_name', 'desc');

        return $res->get();
    }

    public static function createInventory($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = null;
        $data['id'] = Generator::getUUID();
            
        return InventoryModel::create($data);
    }

    public static function updateInventoryById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return InventoryModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }
}
