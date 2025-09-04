<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderModel extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $table = 'reminder';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'reminder_title', 'reminder_context', 'reminder_body', 'reminder_attachment', 'created_at', 'created_by', 'remind_at'];

    public static function getNextReminder($user_id){
        return ReminderModel::select("reminder_title","reminder_context","reminder_body","remind_at")
            ->where('created_by', $user_id)
            ->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc')     
            ->first();                       
    }
}
