<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Service",
 *     type="object",
 *     required={"id", "vehicle_id", "service_category", "service_location", "created_at", "created_by"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the service record"),
 *     @OA\Property(property="vehicle_id", type="string", format="uuid", description="ID of the related vehicle"),
 *     @OA\Property(property="service_note", type="string", nullable=true, description="Additional notes or details about the service"),
 *     @OA\Property(property="service_category", type="string", maxLength=36, description="Category of the service"),
 *     @OA\Property(property="service_price_total", type="integer", nullable=true, description="Total cost of the service"),
 *     @OA\Property(property="service_location", type="string", maxLength=255, description="Location where the service was performed"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the service record was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the service record was last updated"),
 *     @OA\Property(property="remind_at", type="string", format="date-time", nullable=true, description="Optional reminder timestamp for the service"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the service record")
 * )
 */

class ServiceModel extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $table = 'service';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'created_at', 'created_by', 'updated_at', 'remind_at'];
    protected $casts = [
        'service_price_total' => 'integer',
    ];

    public static function getNextService($user_id){
        return ServiceModel::select('service_category', 'service_price_total', 'service_location', 'service_note',"remind_at","vehicle_plate_number")
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id')
            ->where('service.created_by', $user_id)
            ->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc')     
            ->first();                       
    }

    public static function getAllService($user_id = null, $vehicle_id = null, $limit, $search = null){
        $res = ServiceModel::select('service.id', 'service_category', 'service_price_total', 'service_location', 'service_note', 'service.created_at', 'service.updated_at', 'vehicle_plate_number','vehicle_type','remind_at')
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
        if($vehicle_id){
            $res = $res->where('vehicle.id', $vehicle_id);
        }
        if ($search) {
            $search = strtolower($search);
            $res->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(service_note) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(service_location) LIKE ?', ["%{$search}%"]);
            });
        }
            
        return $res->orderByRaw('COALESCE(service.remind_at, service.created_at) DESC')->paginate($limit);                       
    }

    public static function getAllServiceSpending($user_id = null){
        $res = ServiceModel::selectRaw('vehicle_plate_number,MAX(vehicle_type) as vehicle_type,CAST(SUM(service_price_total) as SIGNED) as total')
            ->leftjoin('vehicle','vehicle.id','=','service.vehicle_id');

        if($user_id){
            $res = $res->where('service.created_by', $user_id);
        }
            
        $res = $res->groupBy('vehicle_plate_number')
            ->orderBy('total', 'desc')  
            ->get(); 

        if ($res->isEmpty()) {
            return null;
        }
    
        return $res->map(function ($row) {
            $row->total = (int) $row->total;
            return $row;
        });
    }

    public static function getServiceByVehicle($user_id = null,$vehicle_id){
        $res = ServiceModel::select('service_category', 'service_price_total', 'service_location', 'service_note', 'created_at', 'remind_at');

        if($user_id){
            $res = $res->where('created_by', $user_id);
        }
            
        return $res->where('vehicle_id',$vehicle_id)
            ->orderBy('remind_at', 'desc') 
            ->orderBy('created_at', 'desc')
            ->get();  
    }

    public static function getTotalServicePerYear($user_id = null, $context, $year){
        switch ($context) {
            case 'total_item':
                $context = 'COUNT(id)';
                break;
            case 'total_price':
                $context = 'SUM(service_price_total)';
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

        if ($res->isEmpty()) {
            return null;
        }
    
        return $res->map(function ($row) {
            $row->total = (int) $row->total;
            return $row;
        });
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = ServiceModel::select("vehicle_name","vehicle_plate_number", "vehicle_type", 'service_category', 'service_price_total', 'service_location', 'service_note', 'remind_at', 'service.created_at', 'service.updated_at')
            ->join('vehicle','vehicle.id','=','service.vehicle_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        return $res->where('service.created_by',$user_id)
            ->orderBy('service.created_at', 'desc')
            ->get();
    }

    public static function createService($data, $user_id){
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
