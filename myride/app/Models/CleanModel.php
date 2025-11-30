<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Helper
use App\Helpers\Generator;

class CleanModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'clean';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'clean_desc', 'clean_by', 'clean_tools', 'is_clean_body', 'is_clean_window', 'is_clean_dashboard', 'is_clean_tires', 'is_clean_trash', 'is_clean_engine', 'is_clean_seat', 'is_clean_carpet', 'is_clean_pillows', 'clean_address', 'clean_start_time', 'clean_end_time', 'is_fill_window_cleaning_water', 'is_clean_hollow', 'clean_price', 'created_at', 'created_by', 'updated_at'];

    public static function getAllCleanHistory($user_id,$limit){
        $res = CleanModel::selectRaw("
                clean.id,vehicle_type, CONCAT(vehicle.vehicle_merk, ' - ', vehicle.vehicle_name)  as vehicle_name, vehicle_plate_number, clean_desc, clean_by, clean_tools, clean_price, 
                is_clean_body, is_clean_window, is_clean_dashboard, is_clean_tires, is_clean_trash, is_clean_engine, is_clean_seat, is_clean_carpet, 
                is_clean_pillows, clean_address, clean_start_time, clean_end_time, is_fill_window_cleaning_water, is_clean_hollow, 
                clean.created_at, clean.updated_at
            ")
            ->join('vehicle','vehicle.id','=','clean.vehicle_id')
            ->orderBy('clean.created_at')
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getCleanByVehicleId($user_id,$vehicle_id,$limit = null){
        $res = CleanModel::selectRaw("
                clean.id, clean_desc, clean_by, clean_tools, 
                is_clean_body, is_clean_window, is_clean_dashboard, is_clean_tires, is_clean_trash, is_clean_engine, is_clean_seat, is_clean_carpet, 
                is_clean_pillows, clean_address, clean_start_time, clean_end_time, is_fill_window_cleaning_water, is_clean_hollow, 
                clean.created_at, clean.updated_at
            ")
            ->join('vehicle','vehicle.id','=','clean.vehicle_id')
            ->where('vehicle_id',$vehicle_id)
            ->orderBy('clean.created_at');
            
        if($limit){
            $res = $res->paginate($limit);
        } else {
            $res = $res->get();
        }

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getLastCleanByVehicleId($user_id,$vehicle_id){
        return CleanModel::selectRaw("
                clean_desc, clean_by, clean.created_at,
                is_clean_body, is_clean_window, is_clean_dashboard, is_clean_tires, is_clean_trash, is_clean_engine, is_clean_seat, is_clean_carpet, 
                is_clean_pillows, clean_address, is_fill_window_cleaning_water, is_clean_hollow 
            ")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->orderBy('clean.created_at','DESC')
            ->first();
    }

    public static function getCleanSummaryByVehicleId($user_id,$vehicle_id){
        $res = CleanModel::selectRaw("
            vehicle_type, CONCAT(vehicle.vehicle_merk, ' - ', vehicle.vehicle_name) as vehicle_name, vehicle_plate_number,
            COUNT(*) AS total_clean,
            CAST(SUM(is_clean_body) as INT) AS total_clean_body,
            CAST(SUM(is_clean_window) as INT) AS total_clean_window,
            CAST(SUM(is_clean_dashboard) as INT) AS total_clean_dashboard,
            CAST(SUM(is_clean_tires) as INT) AS total_clean_tires,
            CAST(SUM(is_clean_trash) as INT) AS total_clean_trash,
            CAST(SUM(is_clean_engine) as INT) AS total_clean_engine,
            CAST(SUM(is_clean_seat) as INT) AS total_clean_seat,
            CAST(SUM(is_clean_carpet) as INT) AS total_clean_carpet,
            CAST(SUM(is_clean_pillows) as INT) AS total_clean_pillows,
            CAST(SUM(is_fill_window_cleaning_water) as INT) AS total_fill_window_cleaning_water,
            CAST(SUM(is_clean_hollow) as INT) AS total_clean_hollow,
            CAST(SUM(clean_price) as INT) AS total_price,
            CAST(AVG(clean_price) as INT) AS avg_price_per_clean
        ")->where('vehicle.created_by',$user_id);

        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }
        $res = $res->join('vehicle','vehicle.id','=','clean.vehicle_id')
            ->groupby('vehicle_id');

        return $res->get();
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = CleanModel::selectRaw("
                vehicle_name, clean_desc, clean_by, clean_tools, is_clean_body, is_clean_window, is_clean_dashboard, is_clean_tires, is_clean_trash, 
                is_clean_engine, is_clean_seat, is_clean_carpet, is_clean_pillows, clean_address, clean_start_time, clean_end_time, is_fill_window_cleaning_water, 
                is_clean_hollow, clean.created_at as datetime
            ")
            ->join('vehicle','vehicle.id','=','clean.vehicle_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('clean.created_by',$user_id)
            ->orderBy('clean.created_at');

        return $res->get();
    }

    public static function getTotalCleanSpendingPerMonth($user_id = null, $year, $is_admin){
        $res = CleanModel::selectRaw("SUM(clean_price) as total, MONTH(created_at) as context");
        
        if($user_id){
            $res = $res->where('created_by', $user_id);
        }

        $res = $res->whereRaw("YEAR(created_at) = '$year'")
            ->groupByRaw('MONTH(created_at)')
            ->get();

        return $res;
    }

    public static function getTotalCleanPerYear($user_id = null, $vehicle_id = null, $context, $year){
        switch ($context) {
            case 'total_item':
                $context = 'COUNT(id)';
                break;
            case 'SUM(clean_price)':
                break;
            default:
                return [];
                break;
        }

        $res = CleanModel::selectRaw("$context as total, MONTH(created_at) as context");
        
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->whereRaw("YEAR(created_at) = '$year'")
            ->groupByRaw('MONTH(created_at)')
            ->get();

        return $res;
    }

    public static function hardDeleteCleanById($id, $user_id = null){
        $res = CleanModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = CleanModel::where('vehicle_id',$vehicle_id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function createClean($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['updated_at'] = null;
        $data['id'] = Generator::getUUID();
            
        return CleanModel::create($data);
    }

    public static function updateCleanById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return CleanModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }
}
