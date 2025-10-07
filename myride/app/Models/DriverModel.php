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
    //use HasUuids;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'driver';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'fullname', 'password', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'created_at', 'updated_at', 'created_by'];

    public static function getAllDriver($user_id = null, $limit){
        $res = DriverModel::select('*');

        if($user_id){
            $res = $res->where('created_by', $user_id);
        }
            
        return $res->orderBy('created_at', 'desc')     
            ->paginate($limit);                       
    }

    public static function hardDeleteDriverById($id, $user_id = null){
        $res = DriverModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function getDriverByUsernameOrEmail($username,$email){
        return DriverModel::where('username',$username)
            ->orWhere('email',$email)
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
            ->orderBy('created_at')
            ->get();
    }
}
