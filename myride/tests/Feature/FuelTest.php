<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
use App\Helpers\Audit;

class FuelTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/fuel/',
            'http_errors' => false
        ]);
    }

    public function test_hard_delete_fuel_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "1d2eb3ed-ab10-610b-2475-35789f801dc3";
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
        $this->assertEquals('fuel permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Fuel By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Fuel By Id", "TC-XXX", 'TC-XXX test_hard_delete_fuel_by_id', json_encode($data));
    }
}
