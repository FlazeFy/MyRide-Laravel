<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorModel extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'errors';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'message', 'stack_trace', 'file', 'line', 'faced_by','created_at'];

    public static function getAllErrorAudit(){
        $res = ErrorModel::selectRaw('message,created_at,faced_by,COUNT(1) as total')
            ->orderby('total','desc')
            ->orderby('message','asc')
            ->orderby('created_at','asc')
            ->groupby('message')
            ->get();

        return count($res) > 0 ? $res : null;
    } 
}
