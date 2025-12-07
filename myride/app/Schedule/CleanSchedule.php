<?php

namespace App\Schedule;
use Carbon\Carbon;
use DateTime;
use Telegram\Bot\Laravel\Facades\Telegram;

// Model
use App\Models\HistoryModel;
use App\Models\AdminModel;

// Helper
use App\Helpers\Firebase;

class CleanSchedule
{
    public static function clean_history()
    {
        $firebaseRealtime = new Firebase();
        $days = 30;
        $summary = HistoryModel::whereDate('created_at', '<', Carbon::now()->subDays($days))->delete();
        $admin = AdminModel::getAllContact();

        foreach($admin as $dt){
            $message = "[ADMIN] Hello $dt->username, the system just run a clean history, with result of $summary history executed";

            if($dt->telegram_user_id){
                $response = Telegram::sendMessage([
                    'chat_id' => $dt->telegram_user_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);
            }

            // Audit to firebase realtime
            $record = [
                'context' => 'history',
                'context_id' => $dt->id,
                'clean_type' => 'destroy',
                'telegram_message' => $dt->telegram_user_id,
            ];
            $firebaseRealtime->insert_command('task_scheduling/clean/' . uniqid(), $record);
        }
    }
}
