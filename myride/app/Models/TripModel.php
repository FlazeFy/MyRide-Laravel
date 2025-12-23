<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helpers
use App\Helpers\Converter;
use App\Helpers\Generator;

class TripModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'trip';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'driver_id', 'trip_desc', 'trip_category', 'trip_person', 'trip_origin_name', 'trip_origin_coordinate', 'trip_destination_name', 'trip_destination_coordinate', 'created_at', 'created_by', 'updated_at', 'deleted_at'];

    public static function getAllTrip($user_id, $limit, $driver_id = null){
        $res = TripModel::select("trip.id", "vehicle_name", "driver.fullname as driver_fullname", "vehicle_plate_number", "trip_desc", "trip_category", "trip_origin_name", "trip_person", "trip_origin_coordinate", "trip_destination_name","trip_destination_coordinate","vehicle_type", "trip.created_at")
            ->join('vehicle','vehicle.id','=','trip.vehicle_id')
            ->leftjoin('driver','driver.id','=','trip.driver_id')
            ->orderBy('trip.created_at','desc')
            ->where('trip.created_by',$user_id);

        if($driver_id){
            $res = $res->where('trip.driver_id',$driver_id);
        }

        $res = $res->paginate($limit);

        return $res->isNotEmpty() ? $res : null;
    }

    public static function getTripCalendar($user_id){
        return TripModel::selectRaw("CONCAT(trip_origin_name, ' - ', trip_destination_name) AS trip_location_name, vehicle.vehicle_plate_number,trip.created_at")
            ->leftjoin('vehicle','vehicle.id','=','trip.vehicle_id')
            ->where('trip.created_by',$user_id)
            ->orderby('trip.created_at','DESC')
            ->get();
    }

    public static function getTotalTripByCategory($user_id){
        return TripModel::selectRaw('trip_category as context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('trip_category')
            ->limit(6)
            ->get();
    }

    public static function getCoordinateByTripLocationName($user_id,$trip_location_name){
        $origin = TripModel::selectRaw('trip_origin_name as trip_location_name, trip_origin_coordinate as trip_location_coordinate')
            ->where('trip_origin_name', 'like', "%{$trip_location_name}%")
            ->where('created_by', $user_id)
            ->groupBy('trip_location_name');

        $destination = TripModel::selectRaw('trip_destination_name as trip_location_name, trip_destination_coordinate as trip_location_coordinate')
            ->where('trip_origin_name', 'like', "%{$trip_location_name}%")
            ->where('created_by', $user_id)
            ->groupBy('trip_location_name');

        $combined = $origin->union($destination)
            ->orderBy('trip_location_name','ASC')
            ->limit(14)
            ->get();

        return $combined;
    }

    public static function getLastTrip($user_id){
        return TripModel::select('trip_destination_name','trip_destination_coordinate','driver.username as driver_username','vehicle_plate_number','trip.created_at','vehicle_type')
            ->join('vehicle','vehicle.id','=','trip.vehicle_id')
            ->leftjoin('driver','driver.id','=','trip.driver_id')
            ->where('trip.created_by', $user_id)
            ->orderBy('trip.created_at','DESC')
            ->first();
    }

    public static function getTotalTripByDestinationOrigion($user_id, $type){
        return TripModel::selectRaw('trip_'.$type.'_name context, COUNT(1) as total')
            ->where('created_by', $user_id)
            ->orderBy('total','DESC')
            ->groupBy('trip_'.$type.'_name')
            ->limit(6)
            ->get();
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
        return TripModel::selectRaw("MAX(LOWER(trip_destination_name)) as most_destination, MAX(LOWER(trip_origin_name)) as most_origin, MAX(trip_category) as most_category")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->whereNull('deleted_at')
            ->first();
    }

    public static function getTotalTripDistance($user_id,$vehicle_id){
        $res = TripModel::select("trip_origin_coordinate","trip_destination_coordinate")
            ->where('vehicle_id',$vehicle_id)
            ->where('created_by',$user_id)
            ->whereNull('deleted_at')
            ->whereNotNull("trip_origin_coordinate")
            ->whereNotNull("trip_destination_coordinate")
            ->get();

        $total_distance = 0;

        if(count($res) > 0){
            foreach ($res as $dt) {
                $origin_coor = explode(",", $dt->trip_origin_coordinate);
                $destination_coor = explode(",", $dt->trip_destination_coordinate);
                $lat1 = $origin_coor[0];
                $lon1 = $origin_coor[1];
                $lat2 = $destination_coor[0];
                $lon2 = $destination_coor[1];

                $distance = Converter::calculate_distance($lat1, $lon1, $lat2, $lon2, $unit = 'km');
                $total_distance = $total_distance + $distance;
            }
        }

        return $total_distance;
    }

    public static function getTotalTripByVehiclePerYear($user_id = null, $vehicle_id = null, $year = null){
        if($year == null){
            $year = date('Y');
        }
        $res = TripModel::selectRaw("COUNT(DISTINCT trip.id) as total, MONTH(trip.created_at) as context");

        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        $res = $res->whereRaw("YEAR(trip.created_at) = '$year'")
            ->groupByRaw('MONTH(trip.created_at)')
            ->get();

        return $res;
    }

    public static function getTripDiscovered($user_id = null, $vehicle_id = null)
    {
        $res = TripModel::selectRaw("trip_origin_coordinate, trip_destination_coordinate, COUNT(1) as total,MAX(trip.created_at) as last_update")
            ->join('vehicle', 'vehicle.id', '=', 'trip.vehicle_id');

        if ($vehicle_id) {
            $res = $res->where('vehicle_id', $vehicle_id);
        }
        if ($user_id) {
            $res = $res->where('trip.created_by', $user_id);
        }

        $res = $res->groupBy('trip_origin_coordinate', 'trip_destination_coordinate')->get();

        $totalTrip    = 0;
        $totalDistance = 0;
        $lastUpdate   = null;

        foreach ($res as $item) {
            if($item->trip_origin_coordinate && $item->trip_destination_coordinate){
                [$originLat, $originLng] = explode(',', $item->trip_origin_coordinate);
                [$destLat, $destLng]     = explode(',', $item->trip_destination_coordinate);

                $distance = Converter::calculate_distance((float) $originLat,(float) $originLng,(float) $destLat,(float) $destLng,'km');

                $totalDistance += (float) $distance;

                if (is_null($lastUpdate) || $item->last_update > $lastUpdate) {
                    $lastUpdate = $item->last_update;
                }
            }
            $totalTrip += $item->total;
        }

        return [
            'total_trip'  => $totalTrip,
            'distance_km' => number_format($totalDistance, 2),
            'last_update' => $lastUpdate,
        ];
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = TripModel::selectRaw("
                vehicle_name, vehicle_type, vehicle_plate_number, driver.fullname as driver_name, trip_desc, trip_category, trip_person, trip_origin_name, trip_origin_coordinate, 
                trip_destination_name, trip_destination_coordinate, trip.created_at, trip.updated_at
            ")
            ->join('vehicle','vehicle.id','=','trip.vehicle_id')
            ->leftjoin('driver','driver.id','=','trip.driver_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('trip.created_by',$user_id)
            ->orderBy('trip.created_at', 'desc');

        return $res->get();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = TripModel::where('vehicle_id',$vehicle_id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteTripById($id, $user_id = null){
        $res = TripModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function createTrip($data, $user_id){
        $data['id'] = Generator::getUUID();
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['deleted_at'] = null;
        $data['updated_at'] = null;
        
        return TripModel::create($data);
    }

    public static function updateTripById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return TripModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }
}
