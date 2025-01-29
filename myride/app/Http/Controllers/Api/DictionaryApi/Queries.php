<?php

namespace App\Http\Controllers\Api\DictionaryApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Model
use App\Models\DictionaryModel;

// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "dictionary";
    }

    public function getDictionaryByType(Request $request,$type)
    {
        try{
            $user_id = $request->user()->id;

            // Model
            $res = DictionaryModel::select('dictionary_name','dictionary_type')
                ->where('created_by',$user_id)
                ->orwherenull('created_by');
            if(strpos($type, ',')){
                $dcts = explode(",", $type);
                foreach ($dcts as $dt) {
                    $res = $res->orwhere('dictionary_type',$dt); 
                }
            } else {
                $res = $res->where('dictionary_type',$type); 
            }

            $res = $res->orderby('dictionary_type', 'ASC')
                ->orderby('dictionary_name', 'ASC')
                ->get();
            
            // Response
            if (count($res) > 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => Generator::getMessageTemplate("fetch", $this->module),
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("not_found", $this->module),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
