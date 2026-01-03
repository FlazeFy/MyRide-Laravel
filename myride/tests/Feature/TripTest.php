<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
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

    public function test_post_create_trip(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $body = [
            'vehicle_id' => 'ac278923-14ab-cb8b-0ca5-6cb6cdff094b', 
            'trip_desc' => 'jalan2', 
            'trip_category' => 'Family Vacation',
            'trip_person' => 'John Doe', 
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
        $this->assertEquals("trip created", $data['message']);

        Audit::auditRecordText("Test - Post Create Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Trip", "TC-XXX", 'TC-XXX test_post_create_trip', json_encode($data));
    }

    public function test_get_all_trip(): void
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
            $check_object = ["id", "vehicle_name", "vehicle_plate_number", "trip_desc", "trip_category", "trip_origin_name", "trip_person", "trip_origin_coordinate", "trip_destination_name", "trip_destination_coordinate", "created_at", "driver_fullname"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_plate_number", "trip_desc", "trip_category", "trip_origin_name", "trip_destination_name", "created_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["trip_person", "trip_origin_coordinate", "trip_destination_coordinate", "driver_fullname"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Trip", "TC-XXX", 'TC-XXX test_get_all_trip', json_encode($data));
    }

    public function test_get_trip_history_coordinate_by_location_name(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $location_name = "my";
        $response = $this->httpClient->get("coordinate/$location_name", [
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
            $check_object = ["trip_location_name","trip_location_coordinate"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }

        Audit::auditRecordText("Test - Get Trip History Coordinate By Location Name", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Trip History Coordinate By Location Name", "TC-XXX", 'TC-XXX test_get_trip_history_coordinate_by_location_name', json_encode($data));
    }

    public function test_get_last_trip(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("last", [
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

        $check_object = ["trip_destination_name", "trip_destination_coordinate", "driver_username", "vehicle_plate_number", "created_at"];
        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $dt);
        }

        $check_not_null_str = ["trip_destination_name", "trip_destination_coordinate", "vehicle_plate_number", "created_at"];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($dt[$col]);
            $this->assertIsString($dt[$col]);
        }

        if (!is_null($dt["driver_username"])) {
            $this->assertIsString($dt["driver_username"]);
        }

        Audit::auditRecordText("Test - Get Last Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Last Trip", "TC-XXX", 'TC-XXX test_get_last_trip', json_encode($data));
    }

    public function test_get_trip_calendar(): void
    {
        $token = $this->login_trait("user");

        // Exec
        $response = $this->httpClient->get("calendar", [
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

        $check_object = ['vehicle_plate_number','trip_location_name','created_at'];

        foreach ($data['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }

        Audit::auditRecordText("Test - Get Trip Calendar", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Trip Calendar", "TC-XXX", 'TC-XXX test_get_trip_calendar', json_encode($data));
    }

    public function test_get_all_trip_by_driver_id(): void
    {
        $token = $this->login_trait("user");
        $driver_id = "c61fe1f9-7618-f041-17c6-61682541eca0";

        // Exec
        $response = $this->httpClient->get("driver/$driver_id", [
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
            $check_object = ["id", "vehicle_name", "vehicle_plate_number", "trip_desc", "trip_category", "trip_origin_name", "trip_person", "trip_origin_coordinate", "trip_destination_name", "trip_destination_coordinate", "created_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_plate_number", "trip_desc", "trip_category", "trip_origin_name", "trip_destination_name", "created_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["trip_person", "trip_origin_coordinate", "trip_destination_coordinate"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }
        }

        Audit::auditRecordText("Test - Get All Trip By Driver ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Trip By Driver ID", "TC-XXX", 'TC-XXX test_get_all_trip_by_driver_id', json_encode($data));
    }

    public function test_put_update_trip_by_id(): void
    {
        $token = $this->login_trait("user");
        $id = "9ebacf20-e193-5ed7-241d-ac549f9c3ea9";

        $body = [
            'vehicle_id' => 'ac278923-14ab-cb8b-0ca5-6cb6cdff094b', 
            'trip_desc' => 'jalan2', 
            'trip_category' => 'Business Trip',
            'trip_person' => 'John Doe', 
            'trip_origin_name' => 'Place B', 
            'trip_origin_coordinate' => '-6.226828716225759, 106.82152290589822',  
            'trip_destination_name' => 'Place D',
            'trip_destination_coordinate' => '-6.230792280916382, 106.81781530380249', 
        ];

        // Exec
        $response = $this->httpClient->put("$id", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("trip updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Trip By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Trip By ID", "TC-XXX", 'TC-XXX test_put_update_trip_by_id', json_encode($data));
    }
}
