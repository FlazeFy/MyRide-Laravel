<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MultiModel extends Model
{
    public static function countTotalContext($context, $user_id = null){
        $query = DB::table($context);
    
        if($user_id && $context != "user"){
            $query->where($context.'.created_by', $user_id);
        }
    
        return $query->count();
    }

    public static function getContextTotalStats($context,$user_id,$table){
        $res = DB::table($table)->select(DB::raw("$context as context, COUNT(1) as total"));
        if($user_id){
            $res = $res->where('created_by', $user_id);
        }
        $res = $res->groupby($context)
            ->orderby('total','desc')
            ->limit(7)
            ->get();
        
        return count($res) > 0 ? $res : null;
    }
}
