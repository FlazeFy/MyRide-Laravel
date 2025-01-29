<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
use App\Helpers\Audit;

class TripTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/trip/',
            'http_errors' => false
        ]);
    }

    public function test_post_trip(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $body = [
            'vehicle_id' => '2d98f524-de02-11ed-b5ea-0242ac120002', 
            'trip_desc' => 'jalan2', 
            'trip_category' => 'Others',
            'trip_person' => 'budi', 
            'trip_origin_name' => 'Place A', 
            'trip_origin_coordinate' => '-6.226828716225759, 106.82152290589822',  
            'trip_destination_name' => 'Place C',
            'trip_destination_coordinate' => '-6.230792280916382, 106.81781530380249', 
        ];
        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertTrue(str_contains($data['message'], 'trip created'));

        Audit::auditRecordText("Test - Post Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Trip", "TC-XXX", 'TC-XXX test_post_trip', json_encode($data));
    }
}
