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

    /**
     * @OA\DELETE(
     *     path="/api/v1/dictionary/{id}",
     *     summary="Delete Dictionary By ID",
     *     description="This request is used to permanently delete a dictionary entry based on the provided `ID`. This request interacts with the MySQL database, and have a protected routes.",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Dictionary ID",
     *         example="e1288783-a5d4-1c4c-2cd6-0e92f7cc3bf9",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="dictionary permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="dictionary permentally deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="dictionary failed to permentally deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="dictionary not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="{validation_msg}",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="{field validation message}")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function hardDeleteDictionaryById(Request $request, $id)
    {
        try{
            // Validate param
            $request->merge(['id' => $id]);
            $validator = Validation::getValidateDictionary($request,'delete');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Delete dictionary
                $rows = DictionaryModel::destroy($id);

                if($rows > 0){
                    // Return success response
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

    /**
     * @OA\POST(
     *     path="/api/v1/dictionary",
     *     summary="Post Create Dictionary",
     *     description="This request is used to created a dictionary by using given `dictionary_type`, and `dictionary_name`. This request interacts with the MySQL database, and have a protected routes.",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dictionary_type", "dictionary_name"},
     *             @OA\Property(property="dictionary_type", type="string", example="trip_category"),
     *             @OA\Property(property="dictionary_name", type="string", example="test category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dictionary created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="dictionary created"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="dictionary name must be at least 2 characters")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="dictionary type must be one of the following values trip_category, vehicle_merk, vehicle_type, vehicle_category, vehicle_status, vehicle_default_fuel, vehicle_fuel_status, vehicle_transmission_code")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="dictionary_name is a required field")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="string", example="failed"),
     *                     @OA\Property(property="message", type="string", example="dictionary name has been used. try another")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     )
     * )
     */
    public function postCreateDictionary(Request $request)
    {
        try{
            // Validate request body
            $validator = Validation::getValidateDictionary($request,'create');
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            } else {
                // Request body
                $dictionary_type = $request->dictionary_type;
                $dictionary_name = $request->dictionary_name;

                // Check dictionary name availability
                $isUsedName = DictionaryModel::isUsedName($dictionary_name, $dictionary_type);
                if($isUsedName){
                    return response()->json([
                        'status' => 'error',
                        'message' => Generator::getMessageTemplate("conflict", "$this->module name"),
                    ], Response::HTTP_CONFLICT);
                } else {
                    $user_id = $request->user()->id;

                    // Create dictionary
                    $rows = DictionaryModel::create([
                        'id' => Generator::getUUID(),
                        'dictionary_type' => $dictionary_type,
                        'dictionary_name' => $dictionary_name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                    ]);

                    if($rows){
                        // Return success response
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
