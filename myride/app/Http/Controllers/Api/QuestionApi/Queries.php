<?php

namespace App\Http\Controllers\Api\QuestionApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

// Model
use App\Models\FAQModel;
// Helper
use App\Helpers\Generator;

class Queries extends Controller
{
    private $module;
    public function __construct()
    {
        $this->module = "faq";
    }

    /**
     * @OA\GET(
     *     path="/api/v1/question/faq",
     *     summary="Get Showing FAQ",
     *     description="This request is used to get showing FAQ in the welcome page (Maximum to fetch 8 item). This request interacts with the MySQL database.",
     *     tags={"Question"},
     *     @OA\Response(
     *         response=200,
     *         description="FAQ fetched successfully. Ordered in descending order by `created_at`",
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
            // Get Showing FAQ
            $res = FAQModel::getShowingFAQ();
            if($res) {
                // Return success response
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
