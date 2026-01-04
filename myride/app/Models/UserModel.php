<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

// Others Model
use App\Models\AdminModel;
use App\Models\TripModel;
use App\Models\VehicleModel;
use App\Models\WashModel;
// Helper 
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "username", "password", "email", "telegram_is_valid", "password", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="telegram_user_id", type="string", description="Telegram Account ID for Bot Apps"),
 *     @OA\Property(property="telegram_is_valid", type="bool", description="Validation status of attached telegram account"),
 *     @OA\Property(property="username", type="string", description="Unique Identifier for user"),
 *     @OA\Property(property="email", type="string", description="Email for Auth and Task Scheduling"),
 *     @OA\Property(property="password", type="string", description="Sanctum Hashed Password"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the user was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the user was updated")
 * )
 */

class UserModel extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'password', 'email', 'telegram_user_id', 'telegram_is_valid', 'created_at', 'updated_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function getAllUser($paginate){
        return UserModel::select('*')->orderby('created_at','desc')->paginate($paginate);
    } 

    public static function getSocial($id){
        $res = UserModel::select('username','telegram_user_id','telegram_is_valid','email')
            ->where('id',$id)
            ->first();

        if($res == null){
            $res = AdminModel::select('username','telegram_user_id','telegram_is_valid','email')
                ->where('id',$id)
                ->first();
        }

        return $res;
    }

    public static function getUserBroadcastAll(){
        return UserModel::select('id','username','telegram_user_id','telegram_is_valid','email')->get();
    }

    public static function getUserById($user_id){
        $select_query = 'id,username,email,telegram_user_id,telegram_is_valid,created_at,updated_at';

        $res = UserModel::selectRaw($select_query)
            ->where('id',$user_id)
            ->first();
        if($res){
            $res->role = 'user';
        }
        if(!$res){
            $res = AdminModel::selectRaw($select_query)
                ->where('id',$user_id)
                ->first();
            if($res){
                $res->role = 'admin';
            }
        }

        return $res;
    }

    public static function getUserByUsernameOrEmail($username,$email){
        return UserModel::where('username',$username)
            ->orwhere('email',$email)
            ->first();
    }

    public static function getAvailableYear($user_id, $is_admin){
        $res_vehicle = VehicleModel::selectRaw('YEAR(created_at) as year');
        if (!$is_admin) {
            $res_vehicle = $res_vehicle->where('created_by', $user_id);
        }
        $res_vehicle = $res_vehicle->groupBy('year')->get();
    
        $res_trip = TripModel::selectRaw('YEAR(created_at) as year');
        if (!$is_admin) {
            $res_trip = $res_trip->where('created_by', $user_id);
        }
        $res_trip = $res_trip->groupBy('year')->get();

        $res_wash = WashModel::selectRaw('YEAR(created_at) as year');
        if (!$is_admin) {
            $res_wash = $res_wash->where('created_by', $user_id);
        }
        $res_wash = $res_wash->groupBy('year')->get();

        $res_service = ServiceModel::selectRaw('YEAR(created_at) as year');
        if (!$is_admin) {
            $res_service = $res_service->where('created_by', $user_id);
        }
        $res_service = $res_service->groupBy('year')->get();
    
        $res = $res_vehicle->concat($res_trip)
            ->concat($res_wash)
            ->concat($res_service)
            ->unique('year') 
            ->sortBy('year')
            ->values(); 

        return $res;
    }

    public static function createUser($data){
        return UserModel::create([
            'id' => Generator::getUUID(), 
            'username' => $data->username, 
            'password' => $data->password !== "GOOGLE_SIGN_IN" ? Hash::make($data->password) : $data->password,
            'email' => $data->email, 
            'telegram_user_id' => $data->telegram_user_id, 
            'telegram_is_valid' => 0, 
            'created_at' => date('Y-m-d H:i:s'), 
            'updated_at' => null
        ]);
    }

    public static function updateUserById($data,$id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        return UserModel::where('id',$id)->update($data);
    }

    public static function getCheckUserByUsernameAndEmail($username, $email){
        $res = UserModel::where('username',$username)
            ->orwhere('email',$email)
            ->first();

        if($res){
            return true;
        } else {
            return false;
        }
    }

    // For Seeder
    public static function getRandom($null){
        if($null == 0){
            $data = UserModel::inRandomOrder()->take(1)->first();
            $res = $data->id;
        } else {
            $res = null;
        }
        
        return $res;
    }

    public static function getRandomWithVehicle($null){
        if($null == 0){
            $data = UserModel::select('users.id')
                ->join('vehicle','users.id','=','vehicle.created_by')
                ->inRandomOrder()
                ->take(1)
                ->first();
            $res = $data->id;
        } else {
            $res = null;
        }
        
        return $res;
    }

    public static function getRandomWithVehicleDriver($null){
        if($null == 0){
            $data = UserModel::select('users.id')
                ->join('vehicle','users.id','=','vehicle.created_by')
                ->join('driver','users.id','=','driver.created_by')
                ->inRandomOrder()
                ->take(1)
                ->first();
            $res = $data->id;
        } else {
            $res = null;
        }
        
        return $res;
    }

    public static function isTelegramIDUsed($telegram_id){
        return UserModel::where('telegram_user_id', $telegram_id)->exists();
    }    

    public static function isUsernameEmailUsed($email, $username, $exceptional_id){
        return UserModel::where(function ($query) use ($email, $username) {
                $query->where('email', $email)
                    ->orWhere('username', $username);
            })
            ->where('id', '!=', $exceptional_id)
            ->exists();
    }

    public static function isUsernameUsed($username){
        return UserModel::where('username',$username)->exists();
    }

    public static function getByUsername($username){
        return UserModel::where('username',$username)->first();
    }
}
