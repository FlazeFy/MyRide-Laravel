<?php

namespace App\Http\Controllers\Api\ChatApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

// Models
use App\Models\VehicleModel;
// Helpers
use App\Helpers\Validation;
use App\Helpers\Generator;

class Commands extends Controller
{
    private function detectIntent(array $tokens)
    {
        $vehicleSynonyms = ['vehicle'];
        $mostSynonyms = ['most','highest','maximum','largest'];
        $leastSynonyms = ['least','lowest','minimum','smallest'];
        $tripSynonyms = ['trip'];

        $match = function($token, $dictionary) {
            foreach ($dictionary as $word) {
                // typo tolerance
                if (levenshtein($token, $word) <= 2) return true;
                // semantic similarity
                similar_text($token, $word, $percent);
                if ($percent >= 70) return true;
            }
            return false;
        };

        $hasVehicle = false;
        $hasMost = false;
        $hasLeast = false;
        $hasTrip = false;

        foreach ($tokens as $t) {
            if ($match($t, $vehicleSynonyms)) $hasVehicle = true;
            if ($match($t, $mostSynonyms)) $hasMost = true;
            if ($match($t, $leastSynonyms)) $hasLeast = true;
            if ($match($t, $tripSynonyms)) $hasTrip = true;
        }

        if ($hasVehicle && $hasMost && $hasTrip) {
            return 'vehicle_with_most_trip';
        }
        if ($hasVehicle && $hasLeast && $hasTrip) {
            return 'vehicle_with_least_trip';
        }

        return null;
    }

    /**
     * @OA\POST(
     *     path="/api/v1/chat",
     *     summary="Post Chat",
     *     description="This NLP request is used to do analyze and find data using command (prompt). This request interacts with the MySQL database.",
     *     tags={"Chat"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question"},
     *             @OA\Property(property="question", type="string", example="can you find my vehicle with the most trip?"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="chat answered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="lorem ipsum")
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
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
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
    public function postChat(Request $request)
    {
        try {
            // Validate request body
            $validator = Validation::getValidateChat($request);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $user_id = $request->user()->id;

            // Preprocessing: Normalize and tokenize
            $message = strtolower(trim($request->question));
            $tokens = preg_split('/\s+/', $message);
            // remove punctuation
            $tokens = array_map(fn($t) => preg_replace('/[^\w]/', '', $t), $tokens); 

            // NLU: Detect intent using synonyms and fuzzy matching
            $intent = $this->detectIntent($tokens);

            // Dialog Manager: Route logic based on intent
            switch ($intent) {
                case 'vehicle_with_most_trip':
                    $res= [];

                    if ($res) {
                        // NLG: Format response
                        return response()->json([
                            'status' => 'success',
                            'data' => $res
                        ]);
                    }
                    break;

                case 'vehicle_with_least_trip':
                    $res= [];

                    if ($res) {
                        // NLG: Format response
                        return response()->json([
                            'status' => 'success',
                            'data' => $res
                        ]);
                    }
                    break;

                default:
                    // Learning loop: log unknown messages 
                    return response()->json([
                        'status' => 'failed',
                        'message' => Generator::getMessageTemplate("custom", 'Sorry, I cannot understand your request'),
                    ], Response::HTTP_BAD_REQUEST);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
