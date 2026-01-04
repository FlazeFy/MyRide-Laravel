<?php

namespace App\Schedule;
use Carbon\Carbon;
use DateTime;
use Telegram\Bot\Laravel\Facades\Telegram;

// Model
use App\Models\HistoryModel;
use App\Models\AdminModel;
use App\Models\VehicleModel;
use App\Models\WashModel;
use App\Models\TripModel;
use App\Models\ServiceModel;
use App\Models\InventoryModel;
use App\Models\FuelModel;
use App\Models\ReminderModel;
use App\Models\UserModel;
// Helper
use App\Helpers\TelegramMessage;

class CleanSchedule
{
    public static function clean_history()
    {
        $days = 30;
        $total = HistoryModel::deleteHistoryForLastNDays($days);
        $admin = AdminModel::getAllContact();

        if($admin){
            foreach($admin as $dt){
                $message = "[ADMIN] Hello $dt->username, the system just run a clean history, with result of $total history executed";

                if($dt->telegram_user_id && $dt->telegram_is_valid == 1){
                    if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){
                        $response = Telegram::sendMessage([
                            'chat_id' => $dt->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        AdminModel::updateAdminById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                    }
                }
            }
        }
    }

    public static function clean_reminder()
    {
        $days = 7;
        $total = ReminderModel::deleteReminderForLastNDays($days);
        $admin = AdminModel::getAllContact();

        if($admin){
            foreach($admin as $dt){
                $message = "[ADMIN] Hello $dt->username, the system just run a clean reminder, with result of $total reminder executed";

                if($dt->telegram_user_id && $dt->telegram_is_valid == 1){
                    if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){
                        $response = Telegram::sendMessage([
                            'chat_id' => $dt->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        AdminModel::updateAdminById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                    }
                }
            }
        }
    }

    public static function clean_deleted_vehicle()
    {
        $days = 30;
        $summary = VehicleModel::getVehiclePlanDestroy($days);
        
        if($summary){
            $firebaseRealtime = new Firebase();
            $admin = AdminModel::getAllContact();
            $summary_exec = "";
            $username_before = "";
            $items = "";
            $message = "";
            $total = count($summary); 

            foreach($summary as $vhdex => $vh) {        
                $vehicle_data = "$vh->vehicle_merk $vh->vehicle_name ($vh->vehicle_plate_number)";

                // Report to user & execute destroy
                if($vh->telegram_user_id){
                    if(TelegramMessage::checkTelegramID($vh->telegram_user_id)){
                        $message = "Hello $vh->username, your vehicle $vehicle_data is permanently deleted";
                        // Report to user
                        $response = Telegram::sendMessage([
                            'chat_id' => $vh->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$vh->created_by);
                    }
                }
        
                // Destroy vehicle items
                $rows = VehicleModel::hardDeleteVehicleById($vh->created_by,$vh->id);
                if($rows > 0){
                    // Delete Firebase Uploaded Image
                    if($vh->vehicle_img_url){
                        if(!Firebase::deleteFile($vh->vehicle_img_url)){
                            return response()->json([
                                'status' => 'failed',
                                'message' => Generator::getMessageTemplate("not_found", 'failed to delete vehicle image'),
                            ], Response::HTTP_NOT_FOUND);
                        }
                    }
    
                    WashModel::hardDeleteByVehicleId($vh->id);
                    FuelModel::hardDeleteByVehicleId($vh->id);
                    InventoryModel::hardDeleteByVehicleId($vh->id);
                    ReminderModel::hardDeleteByVehicleId($vh->id);
                    ServiceModel::hardDeleteByVehicleId($vh->id);
                    TripModel::hardDeleteByVehicleId($vh->id);
                }
        
                $summary_exec .= "- $vehicle_data by @$vh->username\n";
            }

            // Report to admin
            foreach($admin as $dt){
                $message_admin = "[ADMIN] Hello $dt->username, the system just run a clean vehicle, here's the detail:\n\n$summary_exec";

                if($dt->telegram_user_id){
                    if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){
                        // Report to admin
                        $response = Telegram::sendMessage([
                            'chat_id' => $dt->telegram_user_id,
                            'text' => $message_admin,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                    }
                }

                // Audit to firebase realtime
                $record = [
                    'context' => 'vehicle_report_admin',
                    'context_id' => $dt->id,
                    'clean_type' => 'destroy',
                    'telegram_message' => $dt->telegram_user_id
                ];
                $firebaseRealtime->insert_command('task_scheduling/clean/' . uniqid(), $record);
            }
        }
    }
}
