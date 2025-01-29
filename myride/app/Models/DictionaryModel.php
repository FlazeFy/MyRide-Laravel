<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function isUsedName($name, $type){
        $res = DictionaryModel::selectRaw('1')
            ->whereRaw('LOWER(dictionary_name) = LOWER(?)', [$name])
            ->whereRaw('LOWER(dictionary_type) = LOWER(?)', [$type])
            ->first();

        return $res ? true : false;
    }
}
