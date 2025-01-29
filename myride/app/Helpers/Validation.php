<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Validator;

// Rules
use App\Rules\DictionaryType;
use App\Rules\VehicleTransmissionType;
use App\Rules\VehicleFuelStatusType;
use App\Rules\VehicleType;
use App\Rules\VehicleStatusType;

class Validation
{
    public static function getValidateLogin($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:30|string',
            'password' => 'required|min:6|string'
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
}
