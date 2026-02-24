<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;

class StatsTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/stats/',
            'http_errors' => false
        ]);
    }

    public function test_get_total_vehicle_by_context(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["vehicle_merk","vehicle_fuel_status","vehicle_category","vehicle_status","vehicle_transmission"];

        foreach($context as $ctx){
            $response = $this->httpClient->get("total/vehicle/$ctx", [
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

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Test - Get Total Vehicle By $ctx_title", "TC-XXX", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Test - Get Total Vehicle By $ctx_title", "TC-XXX", "TC-XXX test_get_total_vehicle_by_$ctx", json_encode($data));    
        }
    }

    public function test_get_total_trip_by_context(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["trip_category","trip_origin_name","trip_destination_name"];

        foreach($context as $ctx){
            $response = $this->httpClient->get("total/trip/$ctx", [
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

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Test - Get Total Trip By $ctx_title", "TC-XXX", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Test - Get Total Trip By $ctx_title", "TC-XXX", "TC-XXX test_get_total_trip_by_$ctx", json_encode($data));
        }
    }

    public function test_get_total_inventory_by_context(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["inventory_category","inventory_storage"];

        foreach($context as $ctx){
            $response = $this->httpClient->get("total/inventory/$ctx", [
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

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Test - Get Total Inventory By $ctx_title", "TC-XXX", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Test - Get Total Inventory By $ctx_title", "TC-XXX", "TC-XXX test_get_total_inventory_by_$ctx", json_encode($data));
        }
    }

    public function test_get_total_service_price_by_context(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["service_category","service_location"];

        foreach($context as $ctx){
            $response = $this->httpClient->get("total/service/$ctx", [
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

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Test - Get Total Service Price By $ctx_title", "TC-XXX", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Test - Get Total Service Price By $ctx_title", "TC-XXX", "TC-XXX test_get_total_service_price_by_$ctx", json_encode($data));
        }
    }

    public function test_get_summary_apps_public(): void
    {
        // Exec
        $response = $this->httpClient->get("summary");

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $int_col = ["total_vehicle","total_service","total_wash","total_driver","total_trip","total_user"];

        foreach ($int_col as $col) {
            $this->assertArrayHasKey($col, $data['data']);
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsInt($data['data'][$col]);
            $this->assertGreaterThanOrEqual(0, $data['data'][$col]);
        }

        Audit::auditRecordText("Integration Test - Success Get Summary Apps (Public)", "TC-INT-ST-001-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Get Summary Apps (Public)", "TC-INT-ST-001-01", "test_get_summary_apps_public", json_encode($data));
    }

    public function test_get_summary_apps_protected(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("summary",[
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

        $int_col = ["total_vehicle","total_service","total_wash","total_driver","total_trip"];

        foreach ($int_col as $col) {
            $this->assertArrayHasKey($col, $data['data']);
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsInt($data['data'][$col]);
            $this->assertGreaterThanOrEqual(0, $data['data'][$col]);
        }

        Audit::auditRecordText("Integration Test - Success Get Summary Apps (Protected)", "TC-INT-ST-001-02", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Get Summary Apps (Protected)", "TC-INT-ST-001-02", "test_get_summary_apps_protected", json_encode($data));
    }

    public function test_get_total_fuel_per_year_protected(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["fuel_volume","fuel_price_total"];
        $year = 2025;

        foreach($context as $ctx){
            // Exec
            $response = $this->httpClient->get("total/fuel/monthly/$ctx/$year",[
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
            $this->assertEquals(12,count($data['data']));

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Integration Test - Success Get Total Fuel Per Year By $ctx_title (Protected)", "TC-INT-ST-003-02", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Integration Test - Success Get Total Fuel Per Year By $ctx_title (Protected)", "TC-INT-ST-003-02", "test_get_total_fuel_per_year_by_$ctx"."_protected", json_encode($data));
        }
    }

    public function test_get_total_fuel_per_year_public(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["fuel_volume","fuel_price_total"];
        $year = 2025;

        foreach($context as $ctx){
            // Exec
            $response = $this->httpClient->get("total/fuel/monthly/$ctx/$year");

            $data = json_decode($response->getBody(), true);

            // Test Parameter
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertArrayHasKey('status', $data);
            $this->assertEquals('success', $data['status']);
            $this->assertArrayHasKey('message', $data);
            $this->assertArrayHasKey('data', $data);
            $this->assertEquals(12,count($data['data']));

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Integration Test - Success Get Total Fuel Per Year By $ctx_title (Public)", "TC-INT-ST-003-01", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Integration Test - Success Get Total Fuel Per Year By $ctx_title (Public)", "TC-INT-ST-003-01", "test_get_total_fuel_per_year_by_$ctx"."_public", json_encode($data));
        }
    }

    public function test_get_total_service_per_year_protected(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["total_item","total_price"];
        $year = 2024;

        foreach($context as $ctx){
            // Exec
            $response = $this->httpClient->get("total/service/monthly/$ctx/$year",[
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
            $this->assertEquals(12,count($data['data']));

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Integration Test - Success Get Total Service Per Year By $ctx_title (Protected)", "TC-INT-ST-004-02", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Integration Test - Success Get Total Service Per Year By $ctx_title (Protected)", "TC-INT-ST-004-02", "test_get_total_service_per_year_by_$ctx"."_protected", json_encode($data));
        }
    }

    public function test_get_total_service_per_year_public(): void
    {
        // Exec
        $context = ["total_item","total_price"];
        $year = 2024;

        foreach($context as $ctx){
            // Exec
            $response = $this->httpClient->get("total/service/monthly/$ctx/$year");

            $data = json_decode($response->getBody(), true);

            // Test Parameter
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertArrayHasKey('status', $data);
            $this->assertEquals('success', $data['status']);
            $this->assertArrayHasKey('message', $data);
            $this->assertArrayHasKey('data', $data);
            $this->assertEquals(12,count($data['data']));

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Integration Test - Success Get Total Service Per Year By $ctx_title (Public)", "TC-INT-ST-004-01", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Integration Test - Success Get Total Service Per Year By $ctx_title (Public)", "TC-INT-ST-004-01", "test_get_total_service_per_year_by_$ctx"."_public", json_encode($data));
        }
    }

    public function test_get_total_wash_per_year_protected(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $context = ["total_item","total_price"];
        $year = 2025;

        foreach($context as $ctx){
            // Exec
            $response = $this->httpClient->get("total/wash/monthly/$ctx/$year",[
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
            $this->assertEquals(12,count($data['data']));

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Integration Test - Success Get Total Wash Per Year By $ctx_title (Protected)", "TC-INT-ST-004-02", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Integration Test - Success Get Total Wash Per Year By $ctx_title (Protected)", "TC-INT-ST-004-02", "test_get_total_wash_per_year_by_$ctx"."_protected", json_encode($data));
        }
    }

    public function test_get_total_wash_per_year_public(): void
    {
        // Exec
        $context = ["total_item","total_price"];
        $year = 2025;

        foreach($context as $ctx){
            // Exec
            $response = $this->httpClient->get("total/wash/monthly/$ctx/$year");

            $data = json_decode($response->getBody(), true);

            // Test Parameter
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertArrayHasKey('status', $data);
            $this->assertEquals('success', $data['status']);
            $this->assertArrayHasKey('message', $data);
            $this->assertArrayHasKey('data', $data);
            $this->assertEquals(12,count($data['data']));

            foreach ($data['data'] as $dt) {
                $this->assertArrayHasKey('context', $dt);
                $this->assertArrayHasKey('total', $dt);

                $this->assertNotNull($dt['context']);
                $this->assertIsString($dt['context']);
        
                $this->assertNotNull($dt['total']);
                $this->assertIsInt($dt['total']);
                $this->assertGreaterThanOrEqual(0, $dt['total']);
            }

            $ctx_title = ucwords(str_replace("_"," ",$ctx));
            Audit::auditRecordText("Integration Test - Success Get Total Wash Per Year By $ctx_title (Public)", "TC-INT-ST-004-01", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Integration Test - Success Get Total Wash Per Year By $ctx_title (Public)", "TC-INT-ST-004-01", "test_get_total_wash_per_year_by_$ctx"."_public", json_encode($data));
        }
    }

    public function test_get_total_trip_per_year_public(): void
    {
        // Exec
        $year = 2025;

        // Exec
        $response = $this->httpClient->get("total/trip/monthly/$year");

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(12,count($data['data']));

        foreach ($data['data'] as $dt) {
            $this->assertArrayHasKey('context', $dt);
            $this->assertArrayHasKey('total', $dt);

            $this->assertNotNull($dt['context']);
            $this->assertIsString($dt['context']);
    
            $this->assertNotNull($dt['total']);
            $this->assertIsInt($dt['total']);
            $this->assertGreaterThanOrEqual(0, $dt['total']);
        }

        Audit::auditRecordText("Integration Test - Success Get Total Trip Per Year (Public)", "TC-INT-ST-002-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Get Total Trip Per Year (Public)", "TC-INT-ST-002-01", "test_get_total_trip_per_year_public", json_encode($data));
    }

    public function test_get_total_trip_per_year_protected(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $year = 2025;

        // Exec
        $response = $this->httpClient->get("total/trip/monthly/$year",[
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
        $this->assertEquals(12,count($data['data']));

        foreach ($data['data'] as $dt) {
            $this->assertArrayHasKey('context', $dt);
            $this->assertArrayHasKey('total', $dt);

            $this->assertNotNull($dt['context']);
            $this->assertIsString($dt['context']);
    
            $this->assertNotNull($dt['total']);
            $this->assertIsInt($dt['total']);
            $this->assertGreaterThanOrEqual(0, $dt['total']);
        }

        Audit::auditRecordText("Integration Test - Success Get Total Trip Per Year (Protected)", "TC-INT-ST-002-02", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Get Total Trip Per Year (Protected)", "TC-INT-ST-002-02", "test_get_total_trip_per_year_protected", json_encode($data));
    }

    public function test_get_journey_by_vehicle_id_protected(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $vehicle_id = "7d53371a-e363-2ad3-25fe-180dae88c062";

        // Exec
        $response = $this->httpClient->get("journey/$vehicle_id",[
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

        $str_not_null_col = ["journey_category", "journey_context", "created_at"];

        foreach ($data['data'] as $dt) {
            foreach ($str_not_null_col as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }

        Audit::auditRecordText("Integration Test - Success Get Journey By Vehicle ID (Protected)", "TC-INT-ST-003-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Get Journey By Vehicle ID (Protected)", "TC-INT-ST-003-01", "test_get_journey_by_vehicle_id_protected", json_encode($data));
    }
}
