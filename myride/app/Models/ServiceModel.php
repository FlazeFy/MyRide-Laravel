<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Helper
use App\Helpers\Generator;

class ServiceModel extends Model
{
    use HasFactory;
    //use HasUuids;
    public $incrementing = false;

    protected $table = 'service';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'created_at', 'created_by', 'updated_at', 'remind_at'];

    public static function getNextService($user_id){
        return ServiceModel::select('service_category', 'service_price_total', 'service_location', 'service_note',"remind_at","vehicle_plate_number")
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id')
            ->where('service.created_by', $user_id)
            ->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc')     
            ->first();                       
    }

    public static function getAllService($user_id = null, $limit){
        $res = ServiceModel::select('service.id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'service.created_at', 'service.updated_at', 'vehicle_plate_number','vehicle_type','remind_at')
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
            
        return $res->orderBy('service.remind_at', 'desc') 
            ->orderBy('service.created_at', 'desc')     
            ->paginate($limit);                       
    }

    public static function getAllServiceSpending($user_id = null){
        $res = ServiceModel::selectRaw('vehicle_plate_number,MAX(vehicle_type) as vehicle_type,CAST(SUM(service_price_total) as INT) as total')
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
            
        return $res->groupBy('vehicle_plate_number')
            ->orderBy('total', 'desc') 
            ->orderBy('vehicle_plate_number', 'desc')     
            ->get(); 
    }

    public static function getServiceByVehicle($user_id = null,$vehicle_id){
        $res = ServiceModel::select('service.id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'service.created_at', 'remind_at');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
            
        return $res->orderBy('remind_at', 'asc') 
            ->get();  
    }

    public static function getTotalServicePerYear($user_id = null, $context, $year){
        switch ($context) {
            case 'total_item':
                $context = 'COUNT(id)';
                break;
            case 'SUM(service_price_total)':
                break;
            default:
                return [];
                break;
        }

        $res = ServiceModel::selectRaw("$context as total, MONTH(created_at) as context");
        
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        $res = $res->whereRaw("YEAR(created_at) = '$year'")
            ->groupByRaw('MONTH(created_at)')
            ->get();

        return $res;
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = ServiceModel::select("vehicle_name","vehicle_plate_number", "vehicle_type", 'service_category', 'service_price_total', 'service_location', 'service_note', 'remind_at', 'service.created_at', 'service.updated_at')
            ->join('vehicle','vehicle.id','=','service.vehicle_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('service.created_by',$user_id)
            ->orderBy('service.created_at', 'desc');

        return $res->get();
    }

    public static function createService($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = null;
        $data['id'] = Generator::getUUID();
            
        return ServiceModel::create($data);
    }

    public static function updateServiceById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return ServiceModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }

    public static function hardDeleteServiceById($id, $user_id = null){
        $res = ServiceModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = ServiceModel::where('vehicle_id',$vehicle_id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }
}
