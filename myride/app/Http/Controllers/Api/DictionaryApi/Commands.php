<?php

namespace App\Http\Controllers\Api\DictionaryApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Model
use App\Models\DictionaryModel;
// Helper
use App\Helpers\Validation;
use App\Helpers\Generator;

class Commands extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "dictionary";
    }

    public function hardDeleteDictionaryById(Request $request, $id)
    {
        try{
            // Validator
            $request->merge(['id' => $id]);
            $validator = Validation::getValidateDictionary($request,'delete');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                // Service : Delete
                $rows = DictionaryModel::destroy($id);

                // Respond
                if($rows > 0){
                    return response()->json([
                        'status' => 'success',
                        'message' => Generator::getMessageTemplate("permentally delete", $this->module),
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("not_found", $this->module),
                    ], Response::HTTP_NOT_FOUND);
                }
            } 
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function postDictionary(Request $request)
    {
        try{
            // Validator
            $validator = Validation::getValidateDictionary($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $dictionary_type = $request->dictionary_type;
                $dictionary_name = $request->dictionary_name;

                // Model : Check name dictionary name avaiability
                $isUsedName = DictionaryModel::isUsedName($dictionary_name, $dictionary_type);
                if($isUsedName){
                    return response()->json([
                        'status' => 'error',
                        'message' => Generator::getMessageTemplate("conflict", "$this->module name"),
                    ], Response::HTTP_CONFLICT);
                } else {
                    $user_id = $request->user()->id;

                    // Service : Create
                    $rows = DictionaryModel::create([
                        'id' => Generator::getUUID(),
                        'dictionary_type' => $dictionary_type,
                        'dictionary_name' => $dictionary_name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                    ]);

                    // Respond
                    if($rows){
                        return response()->json([
                            'status' => 'success',
                            'message' => Generator::getMessageTemplate("create", $this->module),
                        ], Response::HTTP_CREATED);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => Generator::getMessageTemplate("unknown_error", null),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
