<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="DriverVehicleRelation",
 *     type="object",
 *     required={"id", "vehicle_id", "driver_id", "created_at"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the driver-vehicle relation"),
 *     @OA\Property(property="vehicle_id", type="string", format="uuid", description="ID of the related vehicle"),
 *     @OA\Property(property="driver_id", type="string", format="uuid", description="ID of the related driver"),
 *     @OA\Property(property="relation_note", type="string", maxLength=255, nullable=true, description="Additional note describing the driver-vehicle relation"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the relation was created")
 * )
 */

class DriverVehicleRelationModel extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'driver_vehicle_relation';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'driver_id', 'relation_note', 'created_at'];

    public static function hardDeleteDriverVehicleRelationById($id, $user_id = null){
        $res = DriverVehicleRelationModel::where('driver_vehicle_relation.id',$id);

        if($user_id){
            $res = $res->join('driver','driver.id','=','driver_vehicle_relation.driver_id')->where('driver.created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function getRelationByVehicleAndDriver($vehicle_id,$driver_id){
        return DriverVehicleRelationModel::select('driver_vehicle_relation.id')
            ->where('driver_vehicle_relation.driver_id',$driver_id)
            ->where('driver_vehicle_relation.vehicle_id',$vehicle_id)
            ->first();
    }

    public static function createDriverVehicleRelation($data){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['id'] = Generator::getUUID();
            
        return DriverVehicleRelationModel::create($data);
    }

    public static function hardDeleteDriverVehicleRelationByDriverId($driver_id, $user_id = null){
        $res = DriverVehicleRelationModel::where('driver_vehicle_relation.driver_id',$driver_id);

        if($user_id){
            $res = $res->join('driver','driver.id','=','driver_vehicle_relation.driver_id')
                ->where('driver.created_by',$user_id);
        }
            
        return $res->delete();
    }
}
