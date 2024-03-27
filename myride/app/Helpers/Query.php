<?php
namespace App\Helpers;

class Query
{
    public static function get_select_template($type){ 
        if($type == "vehicle_header"){
            $res = "
                id, 
                vehicle_name, vehicle_desc, vehicle_merk, vehicle_type, vehicle_distance, vehicle_category, vehicle_status, vehicle_plate_number, 
                vehicle_fuel_status, vehicle_default_fuel, vehicle_color, vehicle_capacity, vehicle_img_url, 
                updated_at";
        } 

        return $res;
    }
}