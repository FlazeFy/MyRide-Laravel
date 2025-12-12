<?php
namespace App\Helpers;

class Query
{
    public static function get_select_template($type){ 
        if($type == "vehicle_header"){
            $res = "
                vehicle.id, 
                vehicle_name, vehicle_desc, vehicle_merk, vehicle_type, vehicle_distance, vehicle_category, vehicle_status, vehicle_plate_number, 
                vehicle_fuel_status, vehicle_default_fuel, vehicle_color, vehicle_capacity, vehicle_img_url, vehicle_transmission,
                updated_at";
        } else if($type == 'trip_coordinate'){
            $res = "
                trip.id, vehicle_name, vehicle_plate_number,
                trip_desc,trip_category,trip_origin_name,trip_person,trip_origin_coordinate,trip_destination_name,trip_destination_coordinate
                ";
        }

        return $res;
    }
}