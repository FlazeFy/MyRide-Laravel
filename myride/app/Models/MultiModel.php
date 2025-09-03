<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MultiModel extends Model
{
    public static function countTotalContext($context, $user_id = null){
        $query = DB::table($context);
    
        if($user_id){
            $query->where($context.'.created_by', $user_id);
        }
    
        return $query->count();
    }
}
