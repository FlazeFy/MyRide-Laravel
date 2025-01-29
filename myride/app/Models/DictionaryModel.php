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
    protected $fillable = ['id', 'dictionary_type', 'dictionary_name'];

    public static function getDictionaryByType($type){
        $res = DictionaryModel::select("dictionary_name")
            ->where('dictionary_type', $type)
            ->orderBy('dictionary_name', 'ASC')
            ->get();

        return $res;
    }
}
