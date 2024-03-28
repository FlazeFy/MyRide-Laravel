<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'trip';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'trip_desc', 'trip_category', 'trip_person', 'trip_origin_name', 'trip_origin_coordinate', 'trip_destination_name', 'trip_destination_coordinate', 'created_at', 'created_by', 'updated_at', 'deleted_at'];
}
