<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Validator;

// Rules
use App\Rules\DictionaryTypeRule;
use App\Rules\VehicleTransmissionRule;
use App\Rules\VehicleFuelStatusRule;
use App\Rules\VehicleTypeRule;
use App\Rules\VehicleStatusRule;
use App\Rules\FuelBrandRule;
use App\Rules\FuelRonRule;
use App\Rules\ServiceCategoryRule;
use App\Rules\ReminderContextRule;
use App\Rules\InventoryCategoryRule;
use App\Rules\InventoryStorageRule;
use App\Rules\TripCategoryRule;
use App\Rules\VehicleCategoryRule;
use App\Rules\VehicleDefaultFuelRule;
use App\Rules\WashByRule;

class Validation
{
    public static function getValidateLogin($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:36|string',
            'password' => 'required|min:6|max:36|string',
        ]);
    }

    public static function getValidateChat($request){
        return Validator::make($request->all(), [
            'question' => 'required|min:2|max:255|string'
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

    public static function getValidateDriver($request,$type){
        $rules = [
            'username' => 'required|string|min:6|max:36',
            'email' => 'required|string|min:10|max:255',
            'fullname' => 'required|string|min:2|max:50',
            'phone' => 'required|string|min:8|max:16',
            'notes' => 'nullable|string|min:2|max:500',
        ];
    
        if ($type === 'create') {
            $rules['password'] = 'required|string|min:6|max:36';
            $rules['password_confirmation'] = 'required|string|min:6|max:36';
            $rules['telegram_user_id'] = 'nullable|string|min:2|max:36';
        }
    
        if ($type === 'create_relation') {
            $rules = [
                'driver_id' => 'required|string|size:36',
                'vehicle_id' => 'required|string|size:36',
                'relation_note' => 'nullable|string|min:2|max:255',
            ];
        }
    
        return Validator::make($request->all(), $rules);
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
                'dictionary_type' => ['required', new DictionaryTypeRule],
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
                'vehicle_type' => ['required', new VehicleTypeRule],
                'vehicle_price' => 'required|integer|min:0',
                'vehicle_desc' => 'nullable|string|max:500',
                'vehicle_distance' => 'required|integer|min:0',
                'vehicle_category' => ['required', new VehicleCategoryRule],
                'vehicle_status' => ['required', new VehicleStatusRule],
                'vehicle_year_made' => 'required|integer|min:1885|max:'.date('Y'),
                'vehicle_plate_number' => 'required|string|max:14',
                'vehicle_fuel_status' => ['required', new VehicleFuelStatusRule],
                'vehicle_fuel_capacity' => 'nullable|integer|min:0|max:999',
                'vehicle_default_fuel' => ['required', new VehicleDefaultFuelRule],
                'vehicle_color' => 'required|string|max:36',
                'vehicle_transmission' => ['required', new VehicleTransmissionRule],
                'vehicle_capacity' => 'required|integer|min:1|max:99',
            ]);
        }
    }

    public static function getValidateTrip($request,$type){
        $rules = [
            'driver_id' => 'nullable|string|max:36|min:36', 
            'trip_desc' => 'nullable|string|max:500', 
            'trip_category' => ['required', new TripCategoryRule],
            'trip_person' => 'nullable|string|max:255', 
            'trip_origin_name' => 'required|string|max:75|min:2', 
            'trip_origin_coordinate' => 'nullable|string|max:144',  
            'trip_destination_name' => 'required|string|max:75|min:2',
            'trip_destination_coordinate' => 'nullable|string|max:144', 
        ];

        if ($type === 'create') {
            $rules['vehicle_id'] = 'required|string|max:36|min:36';
        }

        return Validator::make($request->all(), $rules);
    }

    public static function getValidateFuel($request){
        return Validator::make($request->all(), [
            'vehicle_id' => 'required|string|max:36|min:36', 
            'fuel_volume' => 'required|integer|min:1|max:99', 
            'fuel_price_total' => 'required|integer|min:1|max:999999999',  
            'fuel_brand' => ['required', new FuelBrandRule], 
            'fuel_type' => 'nullable|string|max:36|min:1',
            'fuel_ron' => ['required', new FuelRonRule],
        ]);
    }

    public static function getValidateUser($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:30|string',
            'email' => 'nullable|string|max:144|min:10', 
            'telegram_user_id' => 'nullable|string|max:36|min:2'
        ]);
    }

    public static function getValidateWash($request){
        return Validator::make($request->all(), [
            'vehicle_id' => 'required|string|max:36|min:36', 
            'wash_desc' => 'nullable|string|min:1|max:500',
            'wash_by' => ['required', new WashByRule], 
            'is_wash_body' => 'required|boolean',
            'is_wash_window' => 'required|boolean',
            'is_wash_dashboard' => 'required|boolean',
            'is_wash_tires' => 'required|boolean',
            'is_wash_trash' => 'required|boolean',
            'is_wash_engine' => 'required|boolean',
            'is_wash_seat' => 'required|boolean',
            'is_wash_carpet' => 'required|boolean',
            'is_wash_pillows' => 'required|boolean',
            'wash_address' => 'nullable|string|max:75|min:1', 
            'wash_price' => 'nullable|integer|min:1|max:999999999  ',
            'wash_start_time' => 'required|date_format:Y-m-d H:i:s',
            'wash_end_time' => 'nullable|date_format:Y-m-d H:i:s', 
            'is_fill_window_washing_water' => 'required|boolean',
            'is_wash_hollow' => 'required|boolean'
        ]);
    }

    public static function getValidateReminder($request){
        return Validator::make($request->all(), [
            'vehicle_id' => 'required|string|max:36|min:36', 
            'reminder_title' => 'required|string|max:75|min:1', 
            'reminder_context' => ['required', new ReminderContextRule], 
            'reminder_body' => 'required|string|max:255|min:1',   
            'remind_at' => 'required|date_format:Y-m-d H:i:s',
        ]);
    }

    public static function getValidateService($request){
        return Validator::make($request->all(), [
            'vehicle_id' => 'required|string|max:36|min:36', 
            'service_note' => 'required|string|min:1', 
            'service_category' => ['required', new ServiceCategoryRule], 
            'service_location' => 'required|string|max:75|min:1', 
            'service_price_total' => 'nullable|integer|max:999999999|min:1',     
            'remind_at' => 'nullable|date_format:Y-m-d H:i:s',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);
    }
    
    public static function getValidateInventory($request,$type){
        $rules = [
            'vehicle_id' => 'required|string|size:36',
            'inventory_name' => 'required|string|min:1|max:75',
            'inventory_category' => ['required', new InventoryCategoryRule],
            'inventory_qty' => 'required|integer|min:1|max:99',
            'inventory_storage' => ['required', new InventoryStorageRule],
        ];

        if ($type === 'create') {
            $rules['gudangku_inventory_id'] = 'nullable|string|size:36';
        }

        return Validator::make($request->all(), $rules);
    }
}
