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
    protected $fillable = ['id', 'reminder_title', 'reminder_context', 'reminder_body', 'reminder_attachment', 'created_at', 'created_by', 'remind_at', 'vehicle_id'];
    protected $casts = [
        'reminder_attachment' => 'array'
    ];

    public static function getNextReminder($user_id){
        return ReminderModel::select("reminder_title","reminder_context","reminder_body","remind_at","vehicle_plate_number")
            ->leftjoin('vehicle','vehicle.id','=','reminder.vehicle_id')
            ->where('reminder.created_by', $user_id)
            ->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc')     
            ->first();                       
    }

    public static function getAllReminder($user_id = null, $limit){
        $res = ReminderModel::select('reminder.id','reminder_title', 'reminder_context', 'reminder_body', 'reminder_attachment', 'reminder.created_at', 'remind_at', 'vehicle.id as vehicle_id','vehicle_plate_number')
            ->leftjoin('vehicle','vehicle.id','=','reminder.vehicle_id');

        if($user_id){
            $res = $res->where('reminder.created_by', $user_id);
        }
            
        return $res->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc')     
            ->paginate($limit);                       
    }

    public static function getReminderByVehicle($user_id = null,$vehicle_id){
        $res = ReminderModel::select('reminder.id','reminder_title', 'reminder_context', 'reminder_body', 'reminder_attachment', 'reminder.created_at', 'remind_at');

        if($user_id){
            $res = $res->where('reminder.created_by', $user_id);
        }
            
        return $res->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc') 
            ->get();  
    }

    public static function hardDeleteReminderById($id, $user_id = null){
        $res = ReminderModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }
}
