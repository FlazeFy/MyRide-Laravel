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
}