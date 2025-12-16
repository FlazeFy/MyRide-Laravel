<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helper 
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="ValidateRequest",
 *     type="object",
 *     required={"id", "request_type", "request_context", "created_at", "created_by"},
 * 
 *     @OA\Property(property="id", type="integer", description="Primary Key"),
 *     @OA\Property(property="request_type", type="string", description="Type of the request"),
 *     @OA\Property(property="request_context", type="string", description="Context of the request"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the user make the request"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who make the request")
 * )
 */

class ValidateRequestModel extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'validate_request';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'request_type', 'request_context', 'created_at', 'created_by']; 

    public static function getActiveRequest($user_id){
        $res = ValidateRequestModel::select('id','request_type', 'request_context', 'created_at')
            ->where('created_by', $user_id)
            ->first();

        return $res;
    }   

    public static function getCheckRegisterToken($username){
        $res = ValidateRequestModel::select('id')
            ->where('request_type','register')
            ->where('created_by',$username)
            ->first();

        return $res ? $res->id : null;
    }

    public static function getActiveRequestByCreatedByTokenAndType($created_by,$token,$type){
        $res = ValidateRequestModel::select('id')
            ->where('request_type',$type)
            ->where('request_context',$token)
            ->where('created_by',$created_by)
            ->first();

        return $res ? $res->id : null;
    }

    public static function createValidateRequest($data, $user_id){
        $res = ValidateRequestModel::create([
            'id' => Generator::getUUID(), 
            'request_type' => $data->request_type, 
            'request_context' => $data->request_context, 
            'created_at' => date('Y-m-d H:i:s'), 
            'created_by' => $user_id
        ]);

        return $res;
    }

    public static function isUserRequestDuplicate($ctx,$user){
        return ValidateRequestModel::where('request_type',$ctx)
            ->where('created_by',$user)
            ->exists();
    }

    public static function isUserRequestValid($ctx,$token,$user){
        $res = ValidateRequestModel::select('id')
            ->where('request_type',$ctx);
        
        if($token){
            $res = $res->where('request_context',$token);
        }
            
        return $res->where('created_by',$user)->first();
    }
}
