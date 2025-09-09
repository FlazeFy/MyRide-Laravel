<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Validator;

// Rules
use App\Rules\DictionaryType;
use App\Rules\VehicleTransmissionType;
use App\Rules\VehicleFuelStatusType;
use App\Rules\VehicleType;
use App\Rules\VehicleStatusType;
use App\Rules\FuelBrandRules;
use App\Rules\FuelTypeRules;
use App\Rules\FuelRonRules;

class Validation
{
    public static function getValidateLogin($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:36|string',
            'password' => 'required|min:6|max:36|string',
        ]);
    }

    public static function getValidateRegister($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:36|string',
            'password' => 'required|min:6|max:36|string',
            'confirm_password' => 'required|min:6|max:36|string',
            'email' => 'required|min:10|max:255|string',
            'telegram_user_id' => 'nullable|string|max:36|min:2'
        ]);
    }

    public static function getValidateRegisterValidation($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:36|string',
            'token' => 'required|min:6|max:6|string',
        ]);
    }

    public static function getValidateDictionary($request,$type){
        if($type == 'create'){
            return Validator::make($request->all(), [
                'dictionary_name' => 'required|string|max:75|min:2',
                'dictionary_type' => ['required', new DictionaryType],
            ]);  
        } else if($type == 'delete'){
            return Validator::make($request->all(), [
                'id' => 'required|string|max:36|min:36',
            ]); 
        }
    }

    public static function getValidateVehicle($request, $type){
        if($type == 'detail'){
            return Validator::make($request->all(), [
                'vehicle_name' => 'required|string|min:2|max:75',
                'vehicle_merk' => 'required|string|max:36',
                'vehicle_type' => ['required', new VehicleType],
                'vehicle_price' => 'required|integer|min:0',
                'vehicle_desc' => 'nullable|string|max:500',
                'vehicle_distance' => 'required|integer|min:0',
                'vehicle_category' => 'required|string|max:36',
                'vehicle_status' => ['required', new VehicleStatusType],
                'vehicle_year_made' => 'required|integer|min:1885|max:' . date('Y'),
                'vehicle_plate_number' => 'required|string|max:14',
                'vehicle_fuel_status' => ['required', new VehicleFuelStatusType],
                'vehicle_fuel_capacity' => 'nullable|integer|min:0|max:999',
                'vehicle_default_fuel' => 'required|string|max:36',
                'vehicle_color' => 'required|string|max:36',
                'vehicle_transmission' => ['required', new VehicleTransmissionType],
                'vehicle_capacity' => 'required|integer|min:1|max:99',
            ]);
        }
    }

    public static function getValidateTrip($request){
        return Validator::make($request->all(), [
            'vehicle_id' => 'required|string|max:36|min:36', 
            'trip_desc' => 'nullable|string|max:500', 
            'trip_category' => 'required|string|max:36|min:2',
            'trip_person' => 'nullable|string|max:255', 
            'trip_origin_name' => 'required|string|max:75|min:2', 
            'trip_origin_coordinate' => 'nullable|string|max:144',  
            'trip_destination_name' => 'required|string|max:75|min:2',
            'trip_destination_coordinate' => 'nullable|string|max:144', 
        ]);
    }

    public static function getValidateFuel($request){
        return Validator::make($request->all(), [
            'vehicle_id' => 'required|string|max:36|min:36', 
            'fuel_volume' => 'required|integer|min:1|max:99', 
            'fuel_price_total' => 'required|integer|min:1|max:999999999',  
            'fuel_brand' => ['required', new FuelBrandRules], 
            'fuel_type' => 'nullable|string|max:36|min:1',
            'fuel_ron' => ['required', new FuelRonRules],
        ]);
    }

    public static function getValidateUser($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:30|string',
            'email' => 'nullable|string|max:144|min:10', 
            'telegram_user_id' => 'nullable|string|max:36|min:2'
        ]);
    }
}
