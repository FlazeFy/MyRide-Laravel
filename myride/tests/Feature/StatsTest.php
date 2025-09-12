<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
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

    public function test_get_summary_apps(): void
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

        $int_col = ["total_vehicle","total_service","total_clean","total_driver","total_trip"];

        foreach ($int_col as $col) {
            $this->assertArrayHasKey($col, $data['data']);
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsInt($data['data'][$col]);
            $this->assertGreaterThanOrEqual(0, $data['data'][$col]);
        }

        Audit::auditRecordText("Test - Get Summary Apps", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Summary Apps", "TC-XXX", "TC-XXX test_get_summary_apps", json_encode($data));
    }

    public function test_get_total_fuel_per_year(): void
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
            Audit::auditRecordText("Test - Get Total Fuel Per Year By $ctx_title", "TC-XXX", "Result : ".json_encode($data));
            Audit::auditRecordSheet("Test - Get Total Fuel Per Year By $ctx_title", "TC-XXX", "TC-XXX test_get_total_fuel_per_year_by_$ctx", json_encode($data));
        }
    }

    public function test_get_total_trip_per_year(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $year = 2024;

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

        Audit::auditRecordText("Test - Get Total Trip Per Year", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Total Trip Per Year", "TC-XXX", "TC-XXX test_get_total_trip_per_year", json_encode($data));
    }
}
