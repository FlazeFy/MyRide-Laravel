<?php

namespace App\Http\Controllers\Api\ExportApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

// Exports
use App\Exports\WashExport;
use App\Exports\FuelExport;
use App\Exports\InventoryExport;
use App\Exports\ServiceExport;
use App\Exports\DriverExport;
use App\Exports\TripExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
// Telegram
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
// Models
use App\Models\WashModel;
use App\Models\FuelModel;
use App\Models\UserModel;
use App\Models\InventoryModel;
use App\Models\ServiceModel;
use App\Models\DriverModel;
use App\Models\TripModel;
// Helpers
use App\Helpers\Generator;
use App\Helpers\TelegramMessage;

class Queries extends Controller {
    public function exportWashHistory(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Wash-$user->username-$datetime.xlsx";

            $res_wash_history = WashModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'vehicle_name' => $dt->vehicle_name,
                    'wash_desc' => $dt->wash_desc,
                    'wash_by' => $dt->wash_by,
                    'is_wash_body' => $dt->is_wash_body == 1 ? "Yes" : "No",
                    'is_wash_window' => $dt->is_wash_window == 1 ? "Yes" : "No",
                    'is_wash_dashboard' => $dt->is_wash_dashboard == 1 ? "Yes" : "No",
                    'is_wash_tires' => $dt->is_wash_tires == 1 ? "Yes" : "No",
                    'is_wash_trash' => $dt->is_wash_trash == 1 ? "Yes" : "No",
                    'is_wash_engine' => $dt->is_wash_engine == 1 ? "Yes" : "No",
                    'is_wash_seat' => $dt->is_wash_seat == 1 ? "Yes" : "No",
                    'is_wash_carpet' => $dt->is_wash_carpet == 1 ? "Yes" : "No",
                    'is_wash_pillows' => $dt->is_wash_pillows == 1 ? "Yes" : "No",
                    'wash_address' => $dt->wash_address,
                    'wash_start_time' => $dt->wash_start_time,
                    'wash_end_time' => $dt->wash_end_time,
                    'is_fill_window_washing_water' => $dt->is_fill_window_washing_water == 1 ? "Yes" : "No",
                    'is_wash_hollow' => $dt->is_wash_hollow == 1 ? "Yes" : "No",
                    'datetime' => $dt->datetime,
                ];
            });

            Excel::store(new class($res_wash_history) implements WithMultipleSheets {
                private $res_wash_history;

                public function __construct($res_wash_history)
                {
                    $this->res_wash_history = $res_wash_history;
                }

                public function sheets(): array
                {
                    return [
                        new WashExport($this->res_wash_history),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id) {
                if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                    $inputFile = InputFile::create($storagePath, $file_name);

                    Telegram::sendDocument([
                        'chat_id' => $user->telegram_user_id,
                        'document' => $inputFile,
                        'caption' => "Your wash export is ready",
                        'parse_mode' => 'HTML',
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                }
            }

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportFuelHistory(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Fuel-$user->username-$datetime.xlsx";

            $res_fuel_history = FuelModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'vehicle_name' => $dt->vehicle_name,
                    'vehicle_type' => $dt->vehicle_type,
                    'vehicle_plate_number' => $dt->vehicle_plate_number, 
                    'fuel_volume' => $dt->fuel_volume, 
                    'fuel_price_total' => $dt->fuel_price_total, 
                    'fuel_brand' => $dt->fuel_brand, 
                    'fuel_type' => $dt->fuel_type, 
                    'fuel_ron' => $dt->fuel_ron, 
                    'datetime' => $dt->datetime,
                ];
            });

            Excel::store(new class($res_fuel_history) implements WithMultipleSheets {
                private $res_fuel_history;

                public function __construct($res_fuel_history)
                {
                    $this->res_fuel_history = $res_fuel_history;
                }

                public function sheets(): array
                {
                    return [
                        new FuelExport($this->res_fuel_history),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id) {
                if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                    $inputFile = InputFile::create($storagePath, $file_name);

                    Telegram::sendDocument([
                        'chat_id' => $user->telegram_user_id,
                        'document' => $inputFile,
                        'caption' => "Your fuel export is ready",
                        'parse_mode' => 'HTML',
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                }
            }

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportInventory(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Inventory-$user->username-$datetime.xlsx";

            $res_inventory = InventoryModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'vehicle_name' => $dt->vehicle_name,
                    'vehicle_type' => $dt->vehicle_type,
                    'vehicle_plate_number' => $dt->vehicle_plate_number, 
                    'inventory_name' => $dt->inventory_name, 
                    'inventory_category' => $dt->inventory_category, 
                    'inventory_qty' => $dt->inventory_qty, 
                    'inventory_storage' => $dt->inventory_storage, 
                    'created_at' => $dt->created_at, 
                    'updated_at' => $dt->updated_at,
                ];
            });

            Excel::store(new class($res_inventory) implements WithMultipleSheets {
                private $res_inventory;

                public function __construct($res_inventory)
                {
                    $this->res_inventory = $res_inventory;
                }

                public function sheets(): array
                {
                    return [
                        new InventoryExport($this->res_inventory),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id){
                if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                    $inputFile = InputFile::create($storagePath, $file_name);

                    Telegram::sendDocument([
                        'chat_id' => $user->telegram_user_id,
                        'document' => $inputFile,
                        'caption' => "Your inventory export is ready",
                        'parse_mode' => 'HTML',
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                }
            }

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportService(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Service-$user->username-$datetime.xlsx";

            $res_service = ServiceModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'vehicle_name' => $dt->vehicle_name,
                    'vehicle_type' => $dt->vehicle_type,
                    'vehicle_plate_number' => $dt->vehicle_plate_number, 
                    'service_category' => $dt->service_category, 
                    'service_price_total' => $dt->service_price_total, 
                    'service_location' => $dt->service_location, 
                    'service_note' => $dt->service_note, 
                    'created_at' => $dt->created_at, 
                    'updated_at' => $dt->updated_at,
                    'remind_at' => $dt->remind_at,
                ];
            });

            Excel::store(new class($res_service) implements WithMultipleSheets {
                private $res_service;

                public function __construct($res_service)
                {
                    $this->res_service = $res_service;
                }

                public function sheets(): array
                {
                    return [
                        new ServiceExport($this->res_service),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id){
                if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                    $inputFile = InputFile::create($storagePath, $file_name);

                    Telegram::sendDocument([
                        'chat_id' => $user->telegram_user_id,
                        'document' => $inputFile,
                        'caption' => "Your service export is ready",
                        'parse_mode' => 'HTML',
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                }
            } 

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportDriver(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Driver-$user->username-$datetime.xlsx";

            $res_driver = DriverModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'username' => $dt->username, 
                    'fullname' => $dt->fullname,  
                    'email' => $dt->email,  
                    'telegram_user_id' => $dt->telegram_user_id,  
                    'telegram_is_valid' => $dt->telegram_is_valid,   
                    'phone' => $dt->phone, 
                    'notes' => $dt->notes, 
                    'created_at' => $dt->created_at, 
                    'updated_at' => $dt->updated_at,
                ];
            });

            Excel::store(new class($res_driver) implements WithMultipleSheets {
                private $res_driver;

                public function __construct($res_driver)
                {
                    $this->res_driver = $res_driver;
                }

                public function sheets(): array
                {
                    return [
                        new DriverExport($this->res_driver),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id){
                if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                    $inputFile = InputFile::create($storagePath, $file_name);

                    Telegram::sendDocument([
                        'chat_id' => $user->telegram_user_id,
                        'document' => $inputFile,
                        'caption' => "Your driver export is ready",
                        'parse_mode' => 'HTML',
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                }
            } 

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function exportTripHistory(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Trip-$user->username-$datetime.xlsx";

            $res_trip = TripModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'vehicle_name' => $dt->vehicle_name,
                    'vehicle_type' => $dt->vehicle_type,
                    'vehicle_plate_number' => $dt->vehicle_plate_number, 
                    'driver_name' => $dt->driver_name, 
                    'trip_desc' => $dt->trip_desc, 
                    'trip_category' => $dt->trip_category, 
                    'trip_person' => $dt->trip_person, 
                    'trip_origin_name' => $dt->trip_origin_name, 
                    'trip_origin_coordinate' => $dt->trip_origin_coordinate, 
                    'trip_destination_name' => $dt->trip_destination_name, 
                    'trip_destination_coordinate' => $dt->trip_destination_coordinate,
                    'created_at' => $dt->created_at, 
                    'updated_at' => $dt->updated_at,
                ];
            });

            Excel::store(new class($res_trip) implements WithMultipleSheets {
                private $res_trip;

                public function __construct($res_trip)
                {
                    $this->res_trip = $res_trip;
                }

                public function sheets(): array
                {
                    return [
                        new TripExport($this->res_trip),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id){
                if(TelegramMessage::checkTelegramID($user->telegram_user_id)){
                    $inputFile = InputFile::create($storagePath, $file_name);

                    Telegram::sendDocument([
                        'chat_id' => $user->telegram_user_id,
                        'document' => $inputFile,
                        'caption' => "Your trip export is ready",
                        'parse_mode' => 'HTML',
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                }
            } 

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}