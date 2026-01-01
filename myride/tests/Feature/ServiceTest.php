<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;

class ServiceTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/service/',
            'http_errors' => false
        ]);
    }

    public function test_get_next_service(): void
    {
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("next", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ]
        ]);

        // Exec
        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["service_note", "service_category", "service_location", "service_price_total", "remind_at", "vehicle_plate_number"];
        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $data["data"]);
        }

        $check_not_null_str = ["service_category", "service_location", "remind_at", "vehicle_plate_number"];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsString($data['data'][$col]);
        }

        if(!is_null($data['data']["service_price_total"])){
            $this->assertIsInt($data['data']["service_price_total"]);
            $this->assertGreaterThan(0, $data['data']["service_price_total"]);
        }
        
        Audit::auditRecordText("Test - Get Next Service", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Next Service", "TC-XXX", 'TC-XXX test_get_next_service', json_encode($data));
    }

    public function test_get_all_service_spending(): void
    {
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("spending", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ]
        ]);

        // Exec
        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["vehicle_plate_number", "vehicle_type", "total"];
        $check_not_null_str = ["vehicle_plate_number", "vehicle_type"];

        foreach ($data['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }
    
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
    
            if(!is_null($dt["total"])){
                $this->assertIsInt($dt["total"]);
                $this->assertGreaterThan(0, $dt["total"]);
            }
        }
        
        Audit::auditRecordText("Test - Get All Service Spending", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Service Spending", "TC-XXX", 'TC-XXX test_get_all_service_spending', json_encode($data));
    }

    public function test_get_service_by_vehicle_id(): void
    {
        $token = $this->login_trait("user");
        $vehicle_id = "ec936a2a-62d0-6101-10b4-1b9a730213da";

        // Exec
        $response = $this->httpClient->get("vehicle/$vehicle_id", [
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

        $check_object = ["service_category", "service_price_total", "service_location", "service_note", "created_at"];
        $check_not_null_str = ["service_category", "service_location", "created_at"];

        foreach ($data['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            if (!is_null($dt["service_price_total"])){
                $this->assertIsInt($dt["service_price_total"]);
            }

            if (!is_null($dt["service_note"])){
                $this->assertIsString($dt["service_note"]);
            }
        }
        
        Audit::auditRecordText("Test - Get Service By Vehicle ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Service By Vehicle ID", "TC-XXX", 'TC-XXX test_get_service_by_vehicle_id', json_encode($data));
    }

    public function test_post_create_service(): void
    {
        $token = $this->login_trait("user");

        $body = [
            'vehicle_id' => 'ec936a2a-62d0-6101-10b4-1b9a730213da',
            'service_note' => 'Routine service KM 50.000',
            'service_category' => 'Routine',
            'service_location' => 'Honda Autobest',
            'service_price_total' => 1500000,
            'remind_at' => "2025-01-20 09:15:00"
        ];

        // Exec
        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("service created", $data['message']);

        Audit::auditRecordText("Test - Post Create Service", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Service", "TC-XXX", 'TC-XXX test_post_create_service', json_encode($data));
    }

    public function test_put_update_service_by_id(): void
    {
        $token = $this->login_trait("user");
        $id = "c624818e-df03-5ab4-06d5-ca7ea415dfa0";

        $body = [
            'vehicle_id' => 'ec936a2a-62d0-6101-10b4-1b9a730213da',
            'service_note' => 'Routine service KM 60.000',
            'service_category' => 'Routine',
            'service_location' => 'Honda Autobest',
            'service_price_total' => 1600000,
            'remind_at' => "2025-01-20 09:15:00"
        ];

        // Exec
        $response = $this->httpClient->put("$id", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("service updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Service By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Service By ID", "TC-XXX", 'TC-XXX test_put_update_service_by_id', json_encode($data));
    }

    public function test_get_all_service(): void
    {
        $token = $this->login_trait("user");

        // Exec
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

        $check_object = ["id", "service_category", "service_price_total", "service_location", "service_note", "created_at", "updated_at", "vehicle_plate_number", "vehicle_type", "remind_at"];
        $check_not_null_str = ["id", "service_category", "service_location", "created_at", "vehicle_plate_number", "vehicle_type"];
        $check_nullable_str = ["service_note", "updated_at", "remind_at"];

        foreach ($data['data']['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])){
                    $this->assertIsString($dt[$col]);
                }
            }

            if (!is_null($dt["service_price_total"])){
                $this->assertIsInt($dt["service_price_total"]);
            }
        }
        
        Audit::auditRecordText("Test - Get All Service", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Service", "TC-XXX", 'TC-XXX test_get_all_service', json_encode($data));
    }

    public function test_hard_delete_service_by_id(): void
    {
        $token = $this->login_trait("user");
        $id = "2599322c-a232-11ee-8c90-0242ac120002";

        // Exec
        $response = $this->httpClient->delete("destroy/$id", [
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
        $this->assertEquals('service permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Service By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Service By Id", "TC-XXX", 'TC-XXX test_hard_delete_service_by_id', json_encode($data));
    }
}
