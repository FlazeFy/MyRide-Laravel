<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
