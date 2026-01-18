<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Reminder",
 *     type="object",
 *     required={"id", "vehicle_id", "reminder_title", "reminder_context", "reminder_body", "created_at", "remind_at", "created_by"},
 *
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary key for the reminder"),
 *     @OA\Property(property="vehicle_id", type="string", format="uuid", description="ID of the related vehicle"),
 *     @OA\Property(property="reminder_title", type="string", maxLength=75, description="Title of the reminder"),
 *     @OA\Property(property="reminder_context", type="string", maxLength=36, description="Context or category of the reminder"),
 *     @OA\Property(property="reminder_body", type="string", maxLength=255, description="Detailed reminder message"),
 *     @OA\Property(property="reminder_attachment", type="object", nullable=true, description="Optional attachment data for the reminder. This is an array of objects containing two keys: attachment_type (image or location) and attachment_value (an uploaded image URL or a location coordinate)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the reminder was created"),
 *     @OA\Property(property="remind_at", type="string", format="date-time", description="Timestamp when the reminder should be triggered"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the reminder")
 * )
 */

class ReminderModel extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;
    
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

    public static function getAllReminder($user_id = null, $limit, $search){
        $res = ReminderModel::select('reminder.id','reminder_title', 'reminder_context', 'reminder_body', 'reminder_attachment', 'reminder.created_at', 'remind_at', 'vehicle.id as vehicle_id','vehicle_plate_number')
            ->leftjoin('vehicle','vehicle.id','=','reminder.vehicle_id');

        if ($user_id){
            $res = $res->where('reminder.created_by', $user_id);
        }
        if ($search) {
            $search = strtolower($search);
            $res->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(reminder_title) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(reminder_body) LIKE ?', ["%{$search}%"]);
            });
        }
            
        return $res->whereNotNull('remind_at')
            ->orderBy('remind_at', 'desc')   
            ->paginate($limit);                       
    }

    public static function getRecentlyReminder($user_id = null, $limit){
        $res = ReminderModel::select('reminder.id','reminder_title', 'reminder_context', 'reminder_body', 'remind_at', 'vehicle_plate_number')
            ->leftjoin('vehicle','vehicle.id','=','reminder.vehicle_id');

        if ($user_id){
            $res = $res->where('reminder.created_by', $user_id);
        }
            
        return $res->whereNotNull('remind_at')
            ->whereBetween('remind_at', [now()->subDays(3), now()])
            ->orderBy('remind_at', 'desc')     
            ->paginate($limit);                       
    }

    public static function getReminderByVehicle($user_id = null,$vehicle_id){
        $res = ReminderModel::select('reminder_title', 'reminder_context', 'reminder_body', 'remind_at');

        if ($user_id){
            $res = $res->where('reminder.created_by', $user_id);
        }
            
        return $res->whereNotNull('remind_at')
            ->where('remind_at', '>=', now()) 
            ->orderBy('remind_at', 'asc') 
            ->get();  
    }

    public static function hardDeleteReminderById($id, $user_id = null){
        $res = ReminderModel::where('id',$id);

        if ($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = ReminderModel::where('vehicle_id',$vehicle_id);

        if ($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function deleteReminderForLastNDays($days){
        return ReminderModel::whereDate('remind_at', '<', Carbon::now()->subDays($days))->delete();
    }

    public static function createReminder($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['id'] = Generator::getUUID();
            
        return ReminderModel::create($data);
    }
}
