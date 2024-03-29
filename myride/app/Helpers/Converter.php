<?php
namespace App\Helpers;

class Converter
{
    public static function convert_price_k($val) {
        if ($val >= 1000) {
            return round($val / 1000, 1) . 'K';
        } else {
            return $val;
        }
    }

    public static function calculate_distance($lat1, $lon1, $lat2, $lon2, $unit = 'km') {
        $theta = $lon1 - $lon2;
        $distance = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515; 

        if ($unit == 'km') {
            $distance = $distance * 1.609344;
        }

        $distance = number_format($distance, 2);
        
        return $distance;
    }
}