<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Helper 
use App\Helpers\Generator;

class DictionaryModel extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $timestamps = false;

    protected $table = 'dictionary';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'dictionary_type', 'dictionary_name', 'created_at', 'created_by'];

    public static function getDictionaryByType($type){
        $res = DictionaryModel::select("dictionary_name")
            ->where('dictionary_type', $type)
            ->orderBy('dictionary_name', 'ASC')
            ->get();

        return $res;
    }

    public static function getDictionaryByTypeAndUserID($type,$user_id){
        $res = DictionaryModel::select('dictionary_name','dictionary_type')
            ->where(function($query) use ($user_id){
                $query->where('created_by',$user_id)
                    ->orwhereNull('created_by');
            });
        if(strpos($type, ',')){
            $dcts = explode(",", $type);
            $res = $res->where(function($query) use ($dcts) {
                foreach ($dcts as $dt) {
                    $query->orWhere('dictionary_type', $dt);
                }
            });
        } else {
            $res = $res->where('dictionary_type',$type); 
        }

        return $res->orderby('dictionary_type', 'ASC')
            ->orderby('dictionary_name', 'ASC')
            ->get();
    }

    public static function isUsedName($name, $type){
        $res = DictionaryModel::selectRaw('1')
            ->whereRaw('LOWER(dictionary_name) = LOWER(?)', [$name])
            ->whereRaw('LOWER(dictionary_type) = LOWER(?)', [$type])
            ->first();

        return $res ? true : false;
    }

    public static function createDictionary($data,$user_id){
        $data['id'] = Generator::getUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;

        return DictionaryModel::create($data);
    }

    // For Seeder
    public static function getRandom($null,$type){
        if($null == 0){
            $data = DictionaryModel::inRandomOrder()->take(1)->where('dictionary_type',$type)->first();
            $res = $data->dictionary_name;
        } else {
            $res = null;
        }
        
        return $res;
    }
}
