<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helpers
use App\Helpers\Query;

class VehicleModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'vehicle';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_name', 'vehicle_merk', 'vehicle_type', 'vehicle_price', 'vehicle_desc', 'vehicle_distance', 'vehicle_category', 'vehicle_status', 'vehicle_year_made', 'vehicle_plate_number', 'vehicle_fuel_status', 'vehicle_fuel_capacity', 'vehicle_default_fuel', 'vehicle_color', 'vehicle_transmission', 'vehicle_img_url', 'vehicle_other_img_url', 'vehicle_capacity', 'vehicle_document', 'created_by', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'vehicle_document' => 'array',
        'vehicle_other_img_url' => 'array'
    ];

    public static function getTotalVehicleByCategory($user_id){
        $res = VehicleModel::selectRaw('vehicle_category as context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('vehicle_category')
            ->limit(6)
            ->get();

        return $res;
    }

    public static function getContextTotalStats($context,$user_id){
        $res = VehicleModel::selectRaw("$context as context, COUNT(1) as total");
        if($user_id){
            $res = $res->where('created_by', $user_id);
        }
        $res = $res->groupby($context)
            ->orderby('total','desc')
            ->limit(7)
            ->get();
        
        return count($res) > 0 ? $res : null;
    }

    public static function getAllVehicleHeader($user_id,$limit){
        $query_header_vehicle = Query::get_select_template('vehicle_header');
        $res = VehicleModel::selectRaw($query_header_vehicle)
            ->orderBy('updated_at','desc')
            ->orderBy('created_at','desc')
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getVehicleDetailById($user_id,$id){
        $res = VehicleModel::where('id',$id)
            ->where('created_by',$user_id)
            ->first();
    
        unset($res->created_by);

        return $res;
    }

    public static function getVehicleIdentity($user_id,$id){
        $res = VehicleModel::select('vehicle_name','vehicle_plate_number')
            ->where('id',$id)
            ->where('created_by',$user_id)
            ->first();

        return $res;
    }
}
