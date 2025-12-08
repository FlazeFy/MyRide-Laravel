<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Helper
use App\Helpers\Generator;
use App\Helpers\Query;

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
        $query_header_vehicle = Query::get_select_template('vehicle_header');
        $res = VehicleModel::selectRaw($query_header_vehicle);
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
        
        return count($res) > 0 ? $res : null;
    }

    public static function softDeleteVehicleById($user_id = null,$id){
        $res = VehicleModel::whereNull("deleted_at")->where('id',$id);
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        return $res->update(['deleted_at'=>date("Y-m-d H:i")]);
    }

    public static function recoverVehicleById($user_id = null,$id){
        $res = VehicleModel::whereNotNull("deleted_at")->where('id',$id);
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        return $res->update(['deleted_at'=>null]);
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
