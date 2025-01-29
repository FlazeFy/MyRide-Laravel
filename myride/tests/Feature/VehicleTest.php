<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
use App\Helpers\Audit;

class VehicleTest extends TestCase
{
    protected $httpClient;
    protected $room;
    protected $storage;
    protected $list_month;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/vehicle/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_vehicle_header(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("header", [
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
            $check_object = ["id", "vehicle_name", "vehicle_desc", "vehicle_merk", "vehicle_type", "vehicle_distance", "vehicle_category", "vehicle_status", 
                "vehicle_plate_number", "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color", "vehicle_capacity", "vehicle_img_url", "updated_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_category", "vehicle_status", 
                "vehicle_plate_number", "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["vehicle_desc", "vehicle_img_url", "updated_at"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["vehicle_distance","vehicle_capacity"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertGreaterThan(0, $dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Inventory", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Inventory", "TC-XXX", 'TC-XXX test_get_all_inventory', json_encode($data));
    }
}
