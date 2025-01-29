<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
use App\Helpers\Audit;

class CleanTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/clean/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_clean_history(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        foreach ($data['data']['data'] as $dt) {
            $check_object = ["id", "vehicle_name", "vehicle_plate_number", "clean_desc", "clean_by", "clean_tools", "is_clean_body", "is_clean_window", 
                "is_clean_dashboard", "is_clean_tires", "is_clean_trash", "is_clean_engine", "is_clean_seat", "is_clean_carpet", "is_clean_pillows", "clean_address", 
                "clean_start_time", "clean_end_time", "is_fill_window_cleaning_water", "is_fill_fuel", "is_clean_hollow", "created_at", "updated_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_plate_number", "clean_start_time", "created_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["clean_desc", "clean_by", "clean_tools", "clean_address", "clean_end_time", "updated_at"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["is_clean_body", "is_clean_window", "is_clean_dashboard", "is_clean_tires", "is_clean_trash", "is_clean_engine", "is_clean_seat", 
                "is_clean_carpet", "is_clean_pillows", "is_fill_window_cleaning_water", "is_fill_fuel", "is_clean_hollow"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertTrue($dt[$col] === 0 || $dt[$col] === 1);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Clean History", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Clean History", "TC-XXX", 'TC-XXX test_get_all_clean_history', json_encode($data));
    }
}
