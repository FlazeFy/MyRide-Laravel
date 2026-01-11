<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Wash",
 *     type="object",
 *     required={
 *         "id", "vehicle_id", "wash_by", "is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", 
 *         "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "is_fill_window_washing_water", "is_wash_hollow", "created_at", "created_by"
 *     },
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the wash record"),
 *     @OA\Property(property="vehicle_id", type="string", format="uuid", description="ID of the washed vehicle"),
 *     @OA\Property(property="wash_desc", type="string", maxLength=500, nullable=true, description="Additional description or notes for the wash"),
 *     @OA\Property(property="wash_by", type="string", maxLength=75, description="Person or service that performed the wash"),
 *     @OA\Property(property="is_wash_body", type="boolean", description="Indicates whether the vehicle body was washed"),
 *     @OA\Property(property="is_wash_window", type="boolean", description="Indicates whether the vehicle windows were washed"),
 *     @OA\Property(property="is_wash_dashboard", type="boolean", description="Indicates whether the dashboard was cleaned"),
 *     @OA\Property(property="is_wash_tires", type="boolean", description="Indicates whether the tires were cleaned"),
 *     @OA\Property(property="is_wash_trash", type="boolean", description="Indicates whether trash inside the vehicle was removed"),
 *     @OA\Property(property="is_wash_engine", type="boolean", description="Indicates whether the engine area was cleaned"),
 *     @OA\Property(property="is_wash_seat", type="boolean", description="Indicates whether the seats were cleaned"),
 *     @OA\Property(property="is_wash_carpet", type="boolean", description="Indicates whether the carpet was cleaned"),
 *     @OA\Property(property="is_wash_pillows", type="boolean", description="Indicates whether pillows or cushions were cleaned"),
 *     @OA\Property(property="wash_address", type="string", maxLength=255, nullable=true, description="Address where the wash was performed"),
 *     @OA\Property(property="wash_start_time", type="string", format="date-time", nullable=true, description="Start time of the washing process"),
 *     @OA\Property(property="wash_end_time", type="string", format="date-time", nullable=true, description="End time of the washing process"),
 *     @OA\Property(property="is_fill_window_washing_water", type="boolean", description="Indicates whether window washing water was refilled"),
 *     @OA\Property(property="is_wash_hollow", type="boolean", description="Indicates whether vehicle hollows or gaps were cleaned"),
 *     @OA\Property(property="wash_price", type="integer", nullable=true, description="Total cost of the washing service"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the wash record was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the wash record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the wash record was last updated")
 * )
 */

class WashModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'wash';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'wash_desc', 'wash_by', 'is_wash_body', 'is_wash_window', 'is_wash_dashboard', 'is_wash_tires', 'is_wash_trash', 'is_wash_engine', 'is_wash_seat', 'is_wash_carpet', 'is_wash_pillows', 'wash_address', 'wash_start_time', 'wash_end_time', 'is_fill_window_washing_water', 'is_wash_hollow', 'wash_price', 'created_at', 'created_by', 'updated_at'];

    public static function getAllWashHistory($user_id,$limit){
        $res = WashModel::selectRaw("
                wash.id,vehicle_type, CONCAT(vehicle.vehicle_merk, ' - ', vehicle.vehicle_name)  as vehicle_name, vehicle_plate_number, wash_desc, wash_by, wash_price, 
                is_wash_body, is_wash_window, is_wash_dashboard, is_wash_tires, is_wash_trash, is_wash_engine, is_wash_seat, is_wash_carpet, 
                is_wash_pillows, wash_address, wash_start_time, wash_end_time, is_fill_window_washing_water, is_wash_hollow, 
                wash.created_at, wash.updated_at
            ")
            ->join('vehicle','vehicle.id','=','wash.vehicle_id')
            ->orderBy('wash.created_at','desc')
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getWashByVehicleId($user_id, $vehicle_id, $limit = null, $page = 1){
        $res = WashModel::selectRaw("
                wash.id, wash_desc, wash_by,
                is_wash_body, is_wash_window, is_wash_dashboard, is_wash_tires,
                is_wash_trash, is_wash_engine, is_wash_seat, is_wash_carpet,
                is_wash_pillows, wash_address, wash_start_time, wash_end_time,
                is_fill_window_washing_water, is_wash_hollow,
                wash.created_at, wash.updated_at
            ")
            ->join('vehicle','vehicle.id','=','wash.vehicle_id')
            ->where('vehicle_id', $vehicle_id)
            ->orderBy('wash.created_at', 'desc');

        if($limit){
            $res = $res->paginate($limit, ['*'], 'page_wash', $page);
        } else {
            $res = $res->get();
        }
    
        return $res->isEmpty() ? null : $res;
    }

    public static function getLastWashByVehicleId($user_id,$vehicle_id){
        return WashModel::selectRaw("
                wash_desc, wash_by, wash.created_at,
                is_wash_body, is_wash_window, is_wash_dashboard, is_wash_tires, is_wash_trash, is_wash_engine, is_wash_seat, is_wash_carpet, 
                is_wash_pillows, wash_address, is_fill_window_washing_water, is_wash_hollow 
            ")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->orderBy('wash.created_at','DESC')
            ->first();
    }

    public static function getWashSummaryByVehicleId($user_id,$vehicle_id){
        $res = WashModel::selectRaw("
            vehicle_type, CONCAT(vehicle.vehicle_merk, ' - ', vehicle.vehicle_name) as vehicle_name, vehicle_plate_number,
            COUNT(*) AS total_wash,
            CAST(SUM(is_wash_body) as SIGNED) AS total_wash_body,
            CAST(SUM(is_wash_window) as SIGNED) AS total_wash_window,
            CAST(SUM(is_wash_dashboard) as SIGNED) AS total_wash_dashboard,
            CAST(SUM(is_wash_tires) as SIGNED) AS total_wash_tires,
            CAST(SUM(is_wash_trash) as SIGNED) AS total_wash_trash,
            CAST(SUM(is_wash_engine) as SIGNED) AS total_wash_engine,
            CAST(SUM(is_wash_seat) as SIGNED) AS total_wash_seat,
            CAST(SUM(is_wash_carpet) as SIGNED) AS total_wash_carpet,
            CAST(SUM(is_wash_pillows) as SIGNED) AS total_wash_pillows,
            CAST(SUM(is_fill_window_washing_water) as SIGNED) AS total_fill_window_washing_water,
            CAST(SUM(is_wash_hollow) as SIGNED) AS total_wash_hollow,
            CAST(SUM(wash_price) as SIGNED) AS total_price,
            CAST(AVG(wash_price) as SIGNED) AS avg_price_per_wash
        ")->where('vehicle.created_by',$user_id);

        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        return $res->join('vehicle','vehicle.id','=','wash.vehicle_id')
            ->groupby('vehicle_id')
            ->orderby('vehicle_name','asc')
            ->get();
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = WashModel::selectRaw("
                vehicle_name, wash_desc, wash_by, is_wash_body, is_wash_window, is_wash_dashboard, is_wash_tires, is_wash_trash, 
                is_wash_engine, is_wash_seat, is_wash_carpet, is_wash_pillows, wash_address, wash_start_time, wash_end_time, is_fill_window_washing_water, 
                is_wash_hollow, wash.created_at as datetime
            ")
            ->join('vehicle','vehicle.id','=','wash.vehicle_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        return $res->where('wash.created_by',$user_id)->orderBy('wash.created_at', 'desc')->get();
    }

    public static function getTotalWashSpendingPerMonth($user_id = null, $year, $is_admin){
        $res = WashModel::selectRaw("SUM(wash_price) as total, MONTH(created_at) as context");
        
        if($user_id){
            $res = $res->where('created_by', $user_id);
        }

        return $res->whereRaw("YEAR(created_at) = '$year'")->groupByRaw('MONTH(created_at)')->get();
    }

    public static function getTotalWashPerYear($user_id = null, $vehicle_id = null, $context, $year){
        switch ($context) {
            case 'total_item':
                $context = 'COUNT(id)';
                break;
            case 'total_price':
                $context = 'SUM(wash_price)';
                break;
            default:
                return [];
                break;
        }

        $res = WashModel::selectRaw("$context as total, MONTH(created_at) as context");
        
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        return $res->whereRaw("YEAR(created_at) = '$year'")->groupByRaw('MONTH(created_at)')->get();
    }

    public static function hardDeleteWashById($id, $user_id = null){
        $res = WashModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = WashModel::where('vehicle_id',$vehicle_id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function createWash($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = null;
        $data['id'] = Generator::getUUID();
            
        return WashModel::create($data);
    }

    public static function updateWashById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return WashModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }
}
