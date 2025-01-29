<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Helpers\Query;

class VehicleModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'vehicle';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_name', 'vehicle_merk', 'vehicle_type', 'vehicle_price', 'vehicle_desc', 'vehicle_distance', 'vehicle_category', 'vehicle_status', 'vehicle_year_made', 'vehicle_plate_number', 'vehicle_fuel_status', 'vehicle_fuel_capacity', 'vehicle_default_fuel', 'vehicle_color', 'vehicle_transmission', 'vehicle_img_url', 'vehicle_other_img_url', 'vehicle_capacity', 'vehicle_document', 'created_by', 'created_at', 'updated_at', 'deleted_at'];

    public static function getTotalVehicleByCategory($user_id){
        $res = VehicleModel::selectRaw('vehicle_category as context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('vehicle_category')
            ->limit(6)
            ->get();

        return $res;
    }

    public static function getAllVehicleHeader($user_id){
        $query_header_vehicle = Query::get_select_template('vehicle_header');
        $res = VehicleModel::selectRaw($query_header_vehicle)
            ->orderBy('updated_at','desc')
            ->orderBy('created_at','desc')
            ->get();

        return count($res) > 0 ? $res : null;
    }
}
