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
            ->orderBy('clean.created_at','DESC')
            ->first();
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
