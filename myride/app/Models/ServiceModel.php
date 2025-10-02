<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceModel extends Model
{
    use HasFactory;
    //use HasUuids;
    public $incrementing = false;

    protected $table = 'service';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'created_at', 'created_by', 'updated_at', 'remind_at'];

    public static function getAllService($user_id = null, $limit){
        $res = ServiceModel::select('service.id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'service.created_at', 'service.updated_at', 'vehicle_plate_number','vehicle_type')
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
            
        return $res->orderBy('service.remind_at', 'desc') 
            ->orderBy('service.created_at', 'desc')     
            ->paginate($limit);                       
    }

    public static function getServiceByVehicle($user_id = null,$vehicle_id){
        $res = ServiceModel::select('service.id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'service.created_at', 'remind_at');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
            
        return $res->orderBy('remind_at', 'asc') 
            ->get();  
    }
}
