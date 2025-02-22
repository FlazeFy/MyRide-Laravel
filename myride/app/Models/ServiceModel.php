<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceModel extends Model
{
    use HasFactory;
    //use HasUuids;
    use HasApiTokens;
    public $incrementing = false;

    protected $table = 'service';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'service_context', 'service_category', 'service_is_payment', 'service_payment_amount', 'service_location', 'notes', 'created_at', 'created_by', 'updated_at'];
}
