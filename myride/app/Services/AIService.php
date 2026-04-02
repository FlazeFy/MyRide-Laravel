<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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

    public function generateSQL(string $userPrompt, string $userId) {
        $schema = $this->getSchemaContext();

        $prompt = "
        You are an expert SQL generator.

        $schema

        User ID: $userId

        Instruction:
        Convert the following request into a valid MySQL SQL query.

        Request:
        \"$userPrompt\"

        Output:
        Only SQL query without explanation.
        ";

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

        $prompt = "
        You are a helpful assistant that explains SQL results.

        STRICT RULES:
        - Answer in Bahasa Indonesia
        - Be concise and natural
        - DO NOT mention 'data', 'JSON', or 'based on data'
        - DO NOT repeat the question
        - Convert numbers into a natural sentence
        - If result is COUNT, explain as total occurrences
        - Use friendly and human-like language

        Question:
        $question

        Data:
        $json

        Answer:
        ";

        return $this->callLlama($prompt);
    }
}