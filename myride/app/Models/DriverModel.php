<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Driver",
 *     type="object",
 *     required={"id", "username", "fullname", "password", "email", "telegram_is_valid", "phone", "created_at", "created_by"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the driver"),
 *     @OA\Property(property="username", type="string", maxLength=36, description="Username used by the driver"),
 *     @OA\Property(property="fullname", type="string", maxLength=50, description="Full name of the driver"),
 *     @OA\Property(property="password", type="string", maxLength=255, description="Hashed password of the driver"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, description="Email address of the driver"),
 *     @OA\Property(property="telegram_user_id", type="string", maxLength=36, nullable=true, description="Telegram user ID associated with the driver"),
 *     @OA\Property(property="telegram_is_valid", type="boolean", description="Indicates whether the Telegram account is verified"),
 *     @OA\Property(property="phone", type="string", maxLength=16, description="Phone number of the driver"),
 *     @OA\Property(property="notes", type="string", maxLength=500, nullable=true, description="Additional notes about the driver"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the driver was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the driver was last updated"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the driver"),
 *     @OA\Property(property="updated_by", type="string", format="uuid", nullable=true, description="ID of the user who last updated the driver")
 * )
 */

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
