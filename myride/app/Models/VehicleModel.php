<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Vehicle",
 *     type="object",
 *     required={
 *         "id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_price", "vehicle_distance", "vehicle_category", "vehicle_status", "vehicle_year_made",
 *         "vehicle_plate_number", "vehicle_fuel_status", "vehicle_fuel_capacity", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission",
 *         "vehicle_capacity", "created_at", "created_by"
 *     },
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the vehicle"),
 *     @OA\Property(property="vehicle_name", type="string", maxLength=75, description="Name of the vehicle"),
 *     @OA\Property(property="vehicle_merk", type="string", maxLength=36, description="Brand or manufacturer of the vehicle"),
 *     @OA\Property(property="vehicle_type", type="string", maxLength=36, description="Type of the vehicle, referenced from dictionary"),
 *     @OA\Property(property="vehicle_price", type="integer", description="Purchase price of the vehicle"),
 *     @OA\Property(property="vehicle_desc", type="string", maxLength=500, nullable=true, description="Additional description of the vehicle"),
 *     @OA\Property(property="vehicle_distance", type="integer", description="Total distance traveled by the vehicle"),
 *     @OA\Property(property="vehicle_category", type="string", maxLength=36, description="Category of the vehicle, referenced from dictionary"),
 *     @OA\Property(property="vehicle_status", type="string", maxLength=36, description="Current status of the vehicle, referenced from dictionary"),
 *     @OA\Property(property="vehicle_year_made", type="integer", description="Manufacturing year of the vehicle"),
 *     @OA\Property(property="vehicle_plate_number", type="string", maxLength=14, description="Vehicle plate number"),
 *     @OA\Property(property="vehicle_fuel_status", type="string", maxLength=36, description="Fuel status of the vehicle, referenced from dictionary"),
 *     @OA\Property(property="vehicle_fuel_capacity", type="integer", description="Fuel tank capacity of the vehicle"),
 *     @OA\Property(property="vehicle_default_fuel", type="string", maxLength=36, description="Default fuel type for the vehicle, referenced from dictionary"),
 *     @OA\Property(property="vehicle_color", type="string", maxLength=36, description="Color of the vehicle"),
 *     @OA\Property(property="vehicle_transmission", type="string", maxLength=14, description="Transmission type of the vehicle, referenced from dictionary"),
 *     @OA\Property(property="vehicle_img_url", type="string", format="url", maxLength=1000, nullable=true, description="Main image URL of the vehicle"),
 *     @OA\Property(property="vehicle_other_img_url", type="object", nullable=true, description="Additional image URLs of the vehicle"),
 *     @OA\Property(property="vehicle_capacity", type="integer", description="Passenger capacity of the vehicle"),
 *     @OA\Property(property="vehicle_document", type="object", nullable=true, description="Additional documents related to the vehicle"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the vehicle was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the vehicle"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the vehicle was last updated"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, description="Timestamp when the vehicle was soft deleted")
 * )
 */

class VehicleModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'vehicle';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_name', 'vehicle_merk', 'vehicle_type', 'vehicle_price', 'vehicle_desc', 'vehicle_distance', 'vehicle_category', 'vehicle_status', 'vehicle_year_made', 'vehicle_plate_number', 'vehicle_fuel_status', 'vehicle_fuel_capacity', 'vehicle_default_fuel', 'vehicle_color', 'vehicle_transmission', 'vehicle_img_url', 'vehicle_other_img_url', 'vehicle_capacity', 'vehicle_document', 'created_by', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'vehicle_document' => 'array',
        'vehicle_other_img_url' => 'array'
    ];

    public static function createVehicle($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = null;
        $data['deleted_at'] = null;
        $data['id'] = Generator::getUUID();
            
        return VehicleModel::create($data);
    }

    public static function getVehicleByIdAndUserId($id, $user_id){
        return VehicleModel::where('id',$id)->where('created_by',$user_id)->first();
    }

    public static function updateVehicleById($data, $id, $user_id){
        $keys = array_keys($data);
        if (!(count($keys) === 1 && $keys[0] === 'deleted_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        return VehicleModel::where('id',$id)->where('created_by',$user_id)->update($data);
    }

    public static function getTotalVehicleByCategory($user_id){
        return VehicleModel::selectRaw('vehicle_category as context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('vehicle_category')
            ->limit(6)
            ->get();
    }

    public static function getAllVehicleName($user_id){
        return VehicleModel::select('id','vehicle_name','vehicle_plate_number','deleted_at')
            ->where('created_by', $user_id)
            ->orderBy('deleted_at','ASC')
            ->orderBy('vehicle_name','DESC')
            ->orderBy('vehicle_plate_number','DESC')
            ->get();
    }

    public static function getAllVehicleFuel($user_id){
        return VehicleModel::select('id','vehicle_name','vehicle_plate_number','vehicle_fuel_status', 'vehicle_fuel_capacity')
            ->where('created_by', $user_id)
            ->orderByRaw("FIELD(vehicle_fuel_status, 'Empty', 'Low', 'Normal', 'High', 'Full', 'Not Monitored')")
            ->get();
    }

    public static function getAllVehicleHeader($user_id = null,$limit){
        $res = VehicleModel::selectRaw("
            id, vehicle_name, vehicle_desc, vehicle_merk, vehicle_type, vehicle_distance, vehicle_category, vehicle_status, vehicle_plate_number, 
            vehicle_fuel_status, vehicle_default_fuel, vehicle_color, vehicle_capacity, vehicle_img_url, vehicle_transmission, updated_at
        ");

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        $res = $res->orderBy('updated_at','desc')
            ->orderBy('created_at','desc')
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getVehicleReadiness($user_id = null,$limit){
        $res = VehicleModel::select('id','vehicle_name','vehicle_type','vehicle_status','vehicle_plate_number','vehicle_fuel_status','vehicle_capacity', 'vehicle_transmission', 'deleted_at',
                DB::raw("
                    (CASE vehicle_status
                        WHEN 'Available' THEN 5
                        WHEN 'Reserved' THEN 3
                        WHEN 'Damaged' THEN 0
                        ELSE 0
                    END) +
                    (CASE vehicle_fuel_status
                        WHEN 'Full' THEN 5
                        WHEN 'High' THEN 4
                        WHEN 'Normal' THEN 3
                        WHEN 'Low' THEN 2
                        WHEN 'Empty' THEN 0
                        WHEN 'Not Monitored' THEN 1
                        ELSE 0
                    END) as readiness
                ")
            );
        
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        $res = $res->orderBy('readiness','desc')
            ->orderBy('created_at','desc')
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getVehicleDetailById($user_id = null,$id){
        $res = VehicleModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        $res = $res->first();

        unset($res->created_by);

        return $res;
    }

    public static function getVehicleIdentity($user_id,$id){
        $res = VehicleModel::select('vehicle_name','vehicle_plate_number')
            ->where('id',$id)
            ->where('created_by',$user_id)
            ->first();

        return $res;
    }

    public static function getContextTotalStats($context,$user_id = null){
        $res = VehicleModel::selectRaw("$context as context, COUNT(1) as total");

        if($user_id){
            $res = $res->where('created_by', $user_id);
        }

        $res = $res->groupby($context)
            ->orderby('total','desc')
            ->limit(7)
            ->get();
        
        if ($res->isEmpty()) {
            return null;
        }
    
        return $res->map(function ($row) {
            $row->total = (int) $row->total;
            return $row;
        });
    }

    public static function hardDeleteVehicleById($user_id = null,$id){
        $res = VehicleModel::whereNotNull("deleted_at")->where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        return $res->delete();
    }

    // For Seeder
    public static function getRandom($null, $user_id){
        if($null == 0){
            $data = VehicleModel::where('created_by',$user_id)->inRandomOrder()->take(1)->first();
            $res = $data->id;
        } else {
            $res = null;
        }
        
        return $res;
    }

    public static function getVehiclePlanDestroy($days){
        return VehicleModel::select('vehicle.id','vehicle_name','deleted_at','username','telegram_user_id','telegram_is_valid','created_by','vehicle_plate_number','vehicle_merk','vehicle_img_url')
            ->join('users','users.id','=','vehicle.created_by')
            ->whereDate('deleted_at', '<', Carbon::now()->subDays($days))
            ->orderby('username','asc')
            ->get();
    }
}
