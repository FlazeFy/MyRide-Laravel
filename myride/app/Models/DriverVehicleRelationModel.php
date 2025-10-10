<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
// Helper
use App\Helpers\Generator;

class DriverVehicleRelationModel extends Authenticatable
{
    use HasFactory;
    //use HasUuids;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'driver_vehicle_relation';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'driver_id', 'relation_note', 'created_at'];

    public static function hardDeleteDriverVehicleRelationById($id, $user_id = null){
        $res = DriverVehicleRelationModel::where('driver_vehicle_relation.id',$id);

        if($user_id){
            $res = $res->join('driver','driver.id','=','driver_vehicle_relation.driver_id')
                ->where('driver.created_by',$user_id);
        }
            
        return $res->delete();
    }
}
