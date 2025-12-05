<?php

namespace App\Schedule;

use Carbon\Carbon;
use DateTime;
use Telegram\Bot\Laravel\Facades\Telegram;

use App\Models\HistoryModel;
use App\Models\AdminModel;
use App\Models\VehicleModel;
use App\Models\WashModel;
use App\Models\TripModel;
use App\Models\ServiceModel;
use App\Models\InventoryModel;
use App\Models\FuelModel;
use App\Models\ReminderModel;

use App\Helpers\TelegramMessage;

class WashSchedule
{
    public static function wash_history()
    {
        $days = 30;
        $total = HistoryModel::deleteHistoryForLastNDays($days);
        $admin = AdminModel::getAllContact();

        if($admin){
            foreach($admin as $dt){
                $message = "[ADMIN] Hello $dt->username, the system just run a wash history, with result of $total history executed";

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

    public static function wash_reminder()
    {
        $days = 7;
        $total = ReminderModel::deleteReminderForLastNDays($days);
        $admin = AdminModel::getAllContact();

        if($admin){
            foreach($admin as $dt){
                $message = "[ADMIN] Hello $dt->username, the system just run a wash reminder, with result of $total reminder executed";

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

    public static function wash_deleted_vehicle()
    {
        $days = 30;
        $summary = VehicleModel::getVehiclePlanDestroy($days);
        
        if($summary){
            $admin = AdminModel::getAllContact();
            $summary_exec = "";
            $username_before = "";
            $items = "";
            $message = "";
            $total = count($summary); 

            foreach($summary as $index => $vh) {
                $items .= $vh->vehicle_name;
                if($index < $total - 1) {
                    if($summary[$index + 1]->username == $vh->username) {
                        $items .= ", ";
                    } 
                }
            
                if($index == $total - 1 || $summary[$index + 1]->username != $vh->username) {
                    $message = "Hello $vh->username, your vehicle $items is permanently deleted";
            
                    // Report to user & execute destroy
                    if($vh->telegram_user_id){
                        if(TelegramMessage::checkTelegramID($vh->telegram_user_id)){
                            $response = Telegram::sendMessage([
                                'chat_id' => $vh->telegram_user_id,
                                'text' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        } else {
                            UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$user_id);
                        }
                    }

                    // Destroy vehicle items
                    VehicleModel::destroy($vh->id);
                    WashModel::hardDeleteByVehicleId($vh->id);
                    FuelModel::hardDeleteByVehicleId($vh->id);
                    InventoryModel::hardDeleteByVehicleId($vh->id);
                    ReminderModel::hardDeleteByVehicleId($vh->id);
                    ServiceModel::hardDeleteByVehicleId($vh->id);
                    TripModel::hardDeleteByVehicleId($vh->id);

                    $summary_exec .= "- $items owned by #$vh->username\n";
                    $items = "";
                }
            }

            // Report to admin
            foreach($admin as $dt){
                $message_admin = "[ADMIN] Hello $dt->username, the system just run a wash vehicle, here's the detail:\n\n$summary_exec";

                if($dt->telegram_user_id){
                    if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){
                        $response = Telegram::sendMessage([
                            'chat_id' => $dt->telegram_user_id,
                            'text' => $message_admin,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        AdminModel::updateAdminById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                    }
                }
            }
        }
    }
}
