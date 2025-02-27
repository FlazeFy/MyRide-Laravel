<?php

namespace App\Http\Controllers\Api\QuestionApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

// Model
use App\Models\FAQModel;

// Helper
use App\Helpers\Generator;

class FAQQueries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "faq";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/question/faq",
     *     summary="Get showing FAQ",
     *     description="This request is used to get showing FAQ in the welcome page. This request is using MySql database",
     *     tags={"Question"},
     *     @OA\Response(
     *         response=200,
     *         description="faq fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="faq fetched"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                          @OA\Property(property="faq_question", type="string", example="lorem ipsum?"),
     *                          @OA\Property(property="faq_answer", type="string", example="consectetuer adipiscing elit"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="faq failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="faq not found")
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
    public function getShowingFAQ()
    {
        try{
            // Model
            $res = FAQModel::getShowingFAQ();
            
            // Response
            if($res) {
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
