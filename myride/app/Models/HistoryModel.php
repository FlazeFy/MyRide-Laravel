<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="History",
 *     type="object",
 *     required={"id", "history_type", "history_context", "created_at", "created_by"},
 * 
 *     @OA\Property(property="id", type="integer", description="Primary Key"),
 *     @OA\Property(property="history_type", type="string", description="Type of the history"),
 *     @OA\Property(property="history_context", type="string", description="Context of the history"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the user created the history"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the history")
 * )
 */

class HistoryModel extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'history';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'history_type', 'history_context', 'created_at', 'created_by'];

    public static function deleteHistoryForLastNDays($days){
        $res = HistoryModel::whereDate('created_at', '<', Carbon::now()->subDays($days))
            ->delete();

        return $res;
    }
}
