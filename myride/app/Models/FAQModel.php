<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'faq';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'faq_question', 'faq_answer', 'created_at', 'is_show'];

    public static function getShowingFAQ(){
        $res = FAQModel::select("faq_question","faq_answer")
            ->where('is_show',1)
            ->whereNotNull('faq_answer')
            ->orderBy('created_at','desc')
            ->limit(8)
            ->get();

        return $res->isNotEmpty() ? $res : null;
    }
}
