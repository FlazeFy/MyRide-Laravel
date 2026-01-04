<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

// Helper
use App\Helpers\Generator;

class DriverModel extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'driver';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'fullname', 'password', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'created_at', 'updated_at', 'created_by'];

    public static function getAllDriver($user_id = null, $limit, $col = 'driver.*,COUNT(trip.id) as total_trip'){
        $res = DriverModel::selectRaw($col);

        if($col == 'driver.*,COUNT(trip.id) as total_trip'){
            $res = $res->leftjoin('trip','trip.driver_id','=','driver.id');
        }

        if($user_id){
            $res = $res->where('driver.created_by', $user_id);
        }

        if($col == 'driver.*,COUNT(trip.id) as total_trip'){
            $res = $res->groupby('driver.id');
        }
            
        if($limit !== 0){
            return $res->orderBy('driver.created_at', 'desc')->paginate($limit); 
        } else {
            return $res->orderBy('driver.created_at', 'desc')->get(); 
        }                     
    }

    public static function getAllDriverName($user_id){
        return DriverModel::select('id', 'username', 'fullname')
            ->where('created_by',$user_id)
            ->orderby('fullname','asc')
            ->get();
    }

    public static function getDriverVehicle($user_id = null, $limit){
        $res = DriverModel::selectRaw('username, fullname, email, telegram_user_id, telegram_is_valid, phone, GROUP_CONCAT(CONCAT(vehicle.vehicle_plate_number, "-", vehicle.vehicle_name) SEPARATOR ", ") as vehicle_list')
            ->leftjoin('driver_vehicle_relation','driver_vehicle_relation.driver_id','=','driver.id')
            ->leftjoin('vehicle','vehicle.id','=','driver_vehicle_relation.vehicle_id');

        if($user_id){
            $res = $res->where('driver.created_by', $user_id);
        }
            
        return $res->orderBy('driver_vehicle_relation.created_at', 'desc')  
            ->groupBy('driver.id', 'driver.username', 'driver.fullname', 'driver.email', 'driver.telegram_user_id', 'driver.telegram_is_valid', 'driver.phone')
            ->paginate($limit);                       
    }

    public static function hardDeleteDriverById($id, $user_id = null){
        $res = DriverModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function getDriverByUsernameOrEmail($username,$email, $id){
        return DriverModel::where(function ($query) use ($username, $email) {
                $query->where('username', $username)
                    ->orWhere('email', $email);
            })
            ->when($id, function ($query, $id) {
                $query->whereNot('id', $id);
            })
            ->first();
    }

    public static function createDriver($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = null;
        $data['id'] = Generator::getUUID();
            
        return DriverModel::create($data);
    }

    public static function getExportData($user_id){
        return DriverModel::select('username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'created_at', 'updated_at')
            ->where('created_by',$user_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getDriverVehicleManageList($user_id){
        return DriverModel::select('driver_vehicle_relation.id', 'vehicle_plate_number', 'vehicle.id as vehicle_id', 'driver.id as driver_id', 'username', 'fullname')
            ->leftjoin('driver_vehicle_relation','driver_vehicle_relation.driver_id','=','driver.id')
            ->join('vehicle','vehicle.id','=','driver_vehicle_relation.vehicle_id')
            ->where('driver.created_by',$user_id)
            ->get();
    }

    public static function getDriverContact($driver_id){
        return DriverModel::select('username','email', 'telegram_user_id', 'telegram_is_valid', 'phone')
            ->where('driver.id',$driver_id)
            ->first();
    }

    public static function getDriverByVehicleId($user_id,$vehicle_id){
        return DriverModel::select('username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'driver_vehicle_relation.created_at as assigned_at')
            ->leftjoin('driver_vehicle_relation','driver_vehicle_relation.driver_id','=','driver.id')
            ->where('vehicle_id',$vehicle_id)
            ->where('driver.created_by',$user_id)
            ->orderBy('driver.created_at','DESC')
            ->get();
    }

    public static function updateDriverById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return DriverModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }

    // For Seeder
    public static function getRandom($null, $user_id){
        if($null == 0){
            $data = DriverModel::where('created_by',$user_id)->inRandomOrder()->take(1)->first();
            $res = $data->id;
        } else {
            $res = null;
        }
        
        return $res;
    }
}
