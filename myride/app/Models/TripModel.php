<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helpers
use App\Helpers\Query;

class TripModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'trip';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'trip_desc', 'trip_category', 'trip_person', 'trip_origin_name', 'trip_origin_coordinate', 'trip_destination_name', 'trip_destination_coordinate', 'created_at', 'created_by', 'updated_at', 'deleted_at'];

    public static function getAllTrip($user_id,$limit){
        $query_trip_coordinate = Query::get_select_template('trip_coordinate');
        $res = TripModel::selectRaw("$query_trip_coordinate, trip.created_at")
            ->join('vehicle','vehicle.id','=','trip.vehicle_id')
            ->orderBy('created_at','desc')
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getTotalTripByCategory($user_id){
        $res = TripModel::selectRaw('trip_category as context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('trip_category')
            ->limit(6)
            ->get();

        return $res;
    }

    public static function getTotalTripByDestinationOrigion($user_id, $type){
        $res = TripModel::selectRaw('trip_'.$type.'_name context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('trip_'.$type.'_name')
            ->limit(6)
            ->get();

        return $res;
    }
}
