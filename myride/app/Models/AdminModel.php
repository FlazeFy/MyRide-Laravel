<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class AdminModel extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'password', 'email', 'telegram_user_id', 'telegram_is_valid', 'created_at', 'updated_at'];

    public static function  getAllContact(){
        $res = AdminModel::select('id','username','email','telegram_user_id','telegram_is_valid')
            ->get();

        return count($res) > 0 ? $res : null;
    }
}
