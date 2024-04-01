<?php
namespace App\Helpers;

use App\Models\HistoryModel;
use App\Helpers\Generator;

class Audit
{
    public static function createHistory($type, $ctx){
        $user_id = Generator::getUserId(session()->get('role_key'));
        
        HistoryModel::create([
            'id' => Generator::getUUID(), 
            'history_type' => $type, 
            'history_context' => $ctx, 
            'created_at' => date("Y-m-d H:i:s"), 
            'created_by' => $user_id,
        ]);
    }
}