<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

// Others Model
use App\Models\AdminModel;

class UserModel extends Authenticatable
{
    use HasFactory;
    //use HasUuids;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'password', 'email', 'telegram_user_id', 'telegram_is_valid', 'created_at', 'updated_at'];

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
}
