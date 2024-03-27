<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleanModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'clean';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'clean_desc', 'clean_by', 'clean_tools', 'is_clean_body', 'is_clean_window', 'is_clean_dashboard', 'is_clean_tires', 'is_clean_trash', 'is_clean_engine', 'is_clean_seat', 'is_clean_carpet', 'is_clean_pillows', 'clean_address', 'clean_start_time', 'clean_end_time', 'is_fill_window_cleaning_water', 'is_fill_fuel', 'is_clean_hollow', 'created_at', 'created_by', 'updated_at'];

}
