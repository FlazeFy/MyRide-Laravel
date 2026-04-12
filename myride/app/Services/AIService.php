<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AIService
{
    protected string $model = 'llama3';

    public function callLlama(string $prompt) {
        $response = Http::timeout(120)->post('http://localhost:11434/api/generate', [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false
        ]);

        return trim($response['response'] ?? '');
    }

    public function getSchemaContext() {
        return "
        Tables:
    
        vehicle(id, vehicle_name, vehicle_type, vehicle_status, vehicle_plate_number, vehicle_distance, vehicle_price, vehicle_color, vehicle_fuel_status, vehicle_fuel_capacity, vehicle_capacity, vehicle_transmission)
    
        trip(id, vehicle_id, driver_id, trip_desc, trip_origin_name, trip_destination_name, trip_person, created_by)
    
        driver(id, fullname)
    
        fuel(id, vehicle_id, fuel_volume, fuel_price_total, created_by)
    
        wash(id, vehicle_id, wash_price, created_by)
    
        service(id, vehicle_id, service_price_total, created_by)
    
        inventory(id, vehicle_id, inventory_name, inventory_qty, created_by)
    
        Relationships:
        - vehicle.id = trip.vehicle_id
        - vehicle.id = fuel.vehicle_id
        - vehicle.id = wash.vehicle_id
        - vehicle.id = service.vehicle_id
        - vehicle.id = inventory.vehicle_id
        - driver.id = trip.driver_id
    
        Semantic Mapping:
        - 'penumpang' refers to trip.trip_person
        - 'asal' refers to trip.trip_origin_name
        - 'tujuan' refers to trip.trip_destination_name
        - 'driver', 'sopir', or 'pengemudi' refers to driver.fullname
        - For name, person, location → ALWAYS use LIKE with %%
        - NEVER use = for text search
    
        Notes:
        - trip_person stores passenger names
        - origin and destination are text fields (locations)
    
        Rules:
        - ONLY generate SELECT query
        - ALWAYS filter by created_by
        - ALWAYS use full SQL syntax (SELECT ... FROM ...)
        - Use JOIN when needed
        - Use SUM() for total or aggregation
        - Use table prefix for all columns
        ";
    }

    public function findRelevantExamples(string $question, int $limit = 4): array {
        $examples = Cache::remember('sql_examples', now()->addDay(), fn() => 
            json_decode(file_get_contents(storage_path('AI/sql_examples.json')), true)
        );
    
        $keywords = explode(' ', strtolower($question));
        
        // Score each example by keyword overlap
        $scored = array_map(function($ex) use ($keywords) {
            $q = strtolower($ex['question']);
            $score = count(array_filter($keywords, fn($k) => str_contains($q, $k)));
            return [...$ex, 'score' => $score];
        }, $examples);
    
        // Sort by score, take top N
        usort($scored, fn($a, $b) => $b['score'] - $a['score']);
        
        return array_slice(array_filter($scored, fn($e) => $e['score'] > 0), 0, $limit);
    }

    public function generateSQL(string $userPrompt, string $userId) {
        $schema = $this->getSchemaContext();
        $examples = $this->findRelevantExamples($userPrompt);
    
        $exampleBlock = '';
        foreach ($examples as $ex) {
            $exampleBlock .= "\nQ: {$ex['question']}\nA: {$ex['sql']}\n";
        }
    
        $prompt = "
        You are a MySQL query generator.
        Output ONLY the raw SQL query. No explanation. No markdown. No comments.
        
        IMPORTANT: If the question matches or is similar to an example below, 
        use that exact SQL as your base and only adjust for the user ID.
    
        === SCHEMA ===
        $schema
    
        === EXAMPLES (follow these exactly) ===
        $exampleBlock
    
        === TASK ===
        User ID: $userId
        Q: $userPrompt
        A:";
    
        return $this->callLlama($prompt);
    }

    public function validateSQL(string $sql, string $userId) {
        // Remove code block
        $sql = preg_replace('/```sql|```/', '', $sql);
    
        // Remove semicolon
        $sql = rtrim($sql, " \t\n\r\0\x0B;");
        $lower = strtolower($sql);
    
        // Only allow SELECT
        if (!str_starts_with($lower, 'select')) throw new \Exception("Only SELECT queries are allowed");
    
        // Block dangerous keywords
        $blocked = ['insert', 'update', 'delete', 'drop', 'truncate', 'alter'];
        foreach ($blocked as $word) {
            if (str_contains($lower, $word)) throw new \Exception("Dangerous SQL detected");
        }
    
        // Detect main table and alias
        preg_match('/from\s+([a-zA-Z_]+)(?:\s+([a-zA-Z_]+))?/i', $sql, $matches);
    
        $mainTable = $matches[1] ?? null;
        $alias = $matches[2] ?? null;
    
        if (!$mainTable) throw new \Exception("Unable to detect main table");
    
        // Use alias if exists, otherwise use table name
        $tableReference = $alias ?: $mainTable;
    
        // Fix ambiguous created_by
        $sql = preg_replace(
            '/(?<!\.)\bcreated_by\b/i',
            "{$tableReference}.created_by",
            $sql
        );
    
        // Ensure WHERE exists only once
        if (str_contains($lower, 'where')) {
            if (!preg_match('/\bcreated_by\s*=/i', $sql)) $sql .= " AND {$tableReference}.created_by = '$userId'";
        } else {
            $sql .= " WHERE {$tableReference}.created_by = '$userId'";
        }
    
        // Clean double WHERE 
        $sql = preg_replace('/where\s+where/i', 'WHERE', $sql);
    
        // Fix accidental WHERE.created_by
        $sql = str_replace('WHERE.', 'WHERE ', $sql);
    
        // Add limit 
        if (!str_contains($lower, 'limit') && !str_contains($lower, 'count(')) $sql .= " LIMIT 50";
    
        return $sql;
    }

    public function generateNarration(string $question, array $data) {
        $json = json_encode($data);

        // Currency rule
        $isMoney = preg_match('/harga|pengeluaran|biaya|cost|price|total/i', $question);
        $moneyRule = $isMoney ? "- This question is about money/cost, ALWAYS format numbers with Rp prefix (e.g. Rp 3.459.000)" : "- Write numbers as digits only (e.g. 3.459)";

        // Datetime rule
        $hasDatetime = preg_match('/"\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])\s(0[0-9]|1[0-9]|2[0-3]):[0-5]\d:[0-5]\d"/', $json);
        $datetimeRule = $hasDatetime ? "- Any datetime value found in the data MUST be formatted as: day (numeric) + full month name in English + year + 'at' + time in 12-hour format with AM/PM (e.g. the format pattern is: DD MMMM YYYY 'at' hh:mm AM/PM). NEVER show raw datetime string." : "";
        
        $prompt = "
        You are a helpful assistant that explains SQL results.

        STRICT RULES:
        - Answer ONLY in Bahasa Indonesia
        - DO NOT translate or add translation in parentheses
        - DO NOT mention 'data', 'JSON', or 'based on data'
        - DO NOT repeat the question
        - Write numbers as digits (e.g. 3.459.000), NOT in word form (e.g. 'tiga juta')
        - $moneyRule
        - $datetimeRule
        - If result is COUNT, explain as total occurrences
        - Use friendly and human-like language
        - Dont put ID in the result

        Question:
        $question

        Data:
        $json

        Answer:
        ";

        return $this->callLlama($prompt);
    }
}