<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

// Other Models
use App\Models\ErrorModel;
use App\Models\InventoryModel;
use App\Models\VehicleModel;
use App\Models\CleanModel;
use App\Models\ServiceModel;
use App\Models\TripModel;
use App\Models\UserModel;
use App\Models\FuelModel;

/**
 * @OA\Schema(
 *     schema="Admin",
 *     type="object",
 *     required={"id", "username", "password", "email", "telegram_is_valid", "password", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="telegram_user_id", type="string", description="Telegram Account ID for Bot Apps"),
 *     @OA\Property(property="telegram_is_valid", type="bool", description="Validation status of attached telegram account"),
 *     @OA\Property(property="username", type="string", description="Unique Identifier for admin"),
 *     @OA\Property(property="email", type="string", description="Email for Auth and Task Scheduling"),
 *     @OA\Property(property="password", type="string", description="Sanctum Hashed Password"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the admin was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the admin was updated")
 * )
 */

class AdminModel extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'admin';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'password', 'email', 'telegram_user_id', 'telegram_is_valid', 'created_at', 'updated_at'];

    public static function  getAllContact(){
        $res = AdminModel::select('id','username','email','telegram_user_id','telegram_is_valid')
            ->get();

        return count($res) > 0 ? $res : null;
    }

    public static function getAppsSummaryForLastNDays($days){
        $res_inventory = InventoryModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();

        $res_user = UserModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();

        $res_vehicle = VehicleModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();

        $res_trip = TripModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();

        $res_fuel = FuelModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();
        
        $res_service = ServiceModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();
        
        $res_clean = CleanModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();

        $res_error = ErrorModel::selectRaw('count(1) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->first();

        $final_res = (object)[
            'vehicle_created' => $res_vehicle->total,
            'inventory_created' => $res_inventory->total,
            'new_user' => $res_user->total,
            'trip_created' => $res_trip->total,
            'fuel_created' => $res_fuel->total,
            'service_created' => $res_service->total,
            'clean_created' => $res_clean->total,
            'error_happen' => $res_error->total,
        ];

        return $final_res;
    }
}
