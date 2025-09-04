<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helpers
use App\Helpers\Query;
use App\Helpers\Converter;

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
            ->where('trip.created_by',$user_id)
            ->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getContextTotalStats($context,$user_id){
        $res = TripModel::selectRaw("$context as context, COUNT(1) as total");
        if($user_id){
            $res = $res->where('created_by', $user_id);
        }
        $res = $res->groupby($context)
            ->orderby('total','desc')
            ->limit(7)
            ->get();
        
        return count($res) > 0 ? $res : null;
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

    public static function getTripByVehicleId($user_id,$vehicle_id,$limit = null){
        $res = TripModel::select("id","trip_desc","trip_category","trip_person","trip_origin_name","trip_origin_coordinate","trip_destination_name","trip_destination_coordinate","created_at")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->whereNull('deleted_at')
            ->orderBy('created_at','desc');
        
        if($limit){
            $res = $res->paginate($limit);
        } else {
            $res = $res->get();
        }

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getMostPersonTripWith($user_id, $vehicle_id, $limit = 7){
        $res = TripModel::selectRaw("LOWER(trip_person) as context")
            ->where('vehicle_id', $vehicle_id)
            ->where('created_by', $user_id)
            ->whereNull('deleted_at')
            ->get();

        $name_counts = [];			
        foreach ($res as $row) {
            if (!empty($row->context)) {
                // Separate using ", " and ", and "
                $names = preg_split('/, and |, /', $row->context);
                
                foreach ($names as $name) {
                    $name = trim(strtolower($name)); 
                    if (!empty($name)) {
                        if (isset($name_counts[$name])) {
                            $name_counts[$name]++;
                        } else {
                            $name_counts[$name] = 1;
                        }
                    }
                }
            }
        }
        arsort($name_counts);

        $result = [];
        $i = 0;
        foreach ($name_counts as $context => $total) {
            $result[] = (object)['context' => $context, 'total' => $total];
            
            if (++$i >= $limit) {
                break;
            }
        }
    
        return $result;
    }

    public static function getMostContext($user_id, $vehicle_id){
        $res = TripModel::selectRaw("MAX(LOWER(trip_destination_name)) as most_destination, MAX(LOWER(trip_origin_name)) as most_origin, MAX(trip_category) as most_category")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->whereNull('deleted_at')
            ->first();

        return $res;
    }

    public static function getTotalTripDistance($user_id,$vehicle_id){
        $res = TripModel::select("trip_origin_coordinate","trip_destination_coordinate")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->whereNull('deleted_at')
            ->get();

        $total_distance = 0;
        foreach ($res as $dt) {
            $origin_coor = explode(", ", $dt->trip_origin_coordinate);
            $destination_coor = explode(", ", $dt->trip_destination_coordinate);
            $lat1 = $origin_coor[0];
            $lon1 = $origin_coor[1];
            $lat2 = $destination_coor[0];
            $lon2 = $destination_coor[1];

            $distance = Converter::calculate_distance($lat1, $lon1, $lat2, $lon2, $unit = 'km');
            $total_distance = $total_distance + $distance;
        }

        return $total_distance;
    }

    public static function getTotalTripByVehiclePerYear($user_id, $vehicle_id = null, $year = null){
        if($year == null){
            $year = date('Y');
        }
        $res = TripModel::selectRaw("COUNT(DISTINCT trip.id) as total, MONTH(trip.created_at) as context");

        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('created_by',$user_id)
            ->whereRaw("YEAR(trip.created_at) = '$year'")
            ->groupByRaw('MONTH(trip.created_at)')
            ->get();

        return $res;
    }
}
