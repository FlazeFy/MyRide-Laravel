<?php

namespace Tests\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;
use App\Helpers\TestDataReader;

class TripTest extends TestCase
{
    protected $httpClient;
    protected string $token;
    protected string $vehicleId;
    protected string $tripId;
    protected string $driverId;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/trip/',
            'http_errors' => false
        ]);

        // Pre-Condition: User already sign in
        $this->token = $this->login_trait("user");
        // Pre-Condition: At least a vehicle exists
        $this->vehicleId = TestDataReader::getValue('vehicle_id') ?? "";
        // Pre-Condition: At least a trip exists
        $this->tripId = TestDataReader::getValue('trip_id') ?? "";
        // Pre-Condition: At least a driver exists
        $this->driverId = TestDataReader::getValue('driver_id') ?? "";
    }

    public function test_post_create_trip(): void
    {
        // Exec
        $body = [
            'vehicle_id' => $this->vehicleId, 
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
                'Authorization' => "Bearer ".$this->token
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

        // Store all created data
        foreach ($body as $key => $val) {
            TestDataReader::setValue($key, $val);
        }
        TestDataReader::setValue('trip_id', $data['data']['id']);

        Audit::auditRecordText("Test - Post Create Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Trip", "TC-XXX", 'TC-XXX test_post_create_trip', json_encode($data));
    }

    public function test_get_all_trip(): void
    {
        // Exec
        $response = $this->httpClient->get("", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
                if (!is_null($dt[$col])) $this->assertIsString($dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Trip", "TC-XXX", 'TC-XXX test_get_all_trip', json_encode($data));
    }

    public function test_get_all_trip_coordinate(): void
    {
        // Exec
        $response = $this->httpClient->get("coordinate", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
            $check_object = ["id", "vehicle_name", "vehicle_plate_number", "vehicle_type", "trip_desc", "trip_category", "trip_origin_name", "trip_person", "trip_origin_coordinate", "trip_destination_name", "trip_destination_coordinate", "created_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_plate_number", "vehicle_type",  "trip_category", "trip_origin_name", "trip_origin_coordinate", "trip_destination_name", "trip_destination_coordinate", "created_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["trip_person","trip_desc"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) $this->assertIsString($dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Trip Coordinate", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Trip Coordinate", "TC-XXX", 'TC-XXX test_get_all_trip_coordinate', json_encode($data));
    }

    public function test_get_trip_history_coordinate_by_location_name(): void
    {
        // Exec
        $location_name = TestDataReader::getValue('trip_origin_name');
        $response = $this->httpClient->get("coordinate/$location_name", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
        $response = $this->httpClient->get("last", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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

        if (!is_null($dt["driver_username"])) $this->assertIsString($dt["driver_username"]);

        Audit::auditRecordText("Test - Get Last Trip", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Last Trip", "TC-XXX", 'TC-XXX test_get_last_trip', json_encode($data));
    }

    public function test_get_trip_calendar(): void
    {
        // Exec
        $response = $this->httpClient->get("calendar", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
        // Exec
        $response = $this->httpClient->get("driver/".$this->driverId, [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
                if (!is_null($dt[$col])) $this->assertIsString($dt[$col]);
            }
        }

        Audit::auditRecordText("Test - Get All Trip By Driver ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Trip By Driver ID", "TC-XXX", 'TC-XXX test_get_all_trip_by_driver_id', json_encode($data));
    }

    public function test_get_trip_discovered(): void
    {
        // Exec
        $response = $this->httpClient->get("discovered", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["total_trip","distance_km","last_update"];
        $check_not_null_int = ["total_trip","distance_km"];

        foreach ($check_object as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertArrayHasKey($col, $data['data']);
        }

        foreach ($check_not_null_int as $col) {
            $this->assertGreaterThan(0, $data['data'][$col]);
        }

        $this->assertIsInt($data['data']["total_trip"]);
        $this->assertIsFloat($data['data']["distance_km"]);
        $this->assertIsString($data['data']["last_update"]);

        Audit::auditRecordText("Test - Get Trip Discovered", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Trip Discovered", "TC-XXX", 'TC-XXX test_get_trip_discovered', json_encode($data));
    }

    public function test_get_nearest_coordinate(): void
    {
        // Exec
        $coordinate = "-6.193307477576132,106.8290024771821";
        $response = $this->httpClient->get("coordinate/nearest/$coordinate", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
            $check_object = ["place_name","place_coordinate","place_distance","last_visit"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["place_name","place_coordinate","last_visit"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $this->assertNotNull($dt["place_distance"]);
            $this->assertIsInt($dt["place_distance"]);
        }

        Audit::auditRecordText("Test - Get Nearest Coordinate", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Nearest Coordinate", "TC-XXX", 'TC-XXX test_get_nearest_coordinate', json_encode($data));
    }

    public function test_get_vehicle_trip_summary_by_id(): void
    {
        // Exec
        $response = $this->httpClient->get("trip/summary/".$this->vehicleId, [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object_trip = ["most_person_with","vehicle_total_trip_distance","most_origin","most_destination","most_category"];
        foreach ($check_object_trip as $col) {
            $this->assertArrayHasKey($col, $data["data"]);
        }

        $check_not_null_str_trip = ["most_origin","most_destination","most_category"];
        foreach ($check_not_null_str_trip as $col) {
            $this->assertNotNull($data["data"][$col]);
            $this->assertIsString($data["data"][$col]);
        }

        if (!is_null($data["data"]["most_person_with"])) {
            $this->assertIsString($data["data"]["most_person_with"]);
        }

        $this->assertIsFloat($data["data"]["vehicle_total_trip_distance"]);
        $this->assertGreaterThan(0, $data["data"]["vehicle_total_trip_distance"]);

        Audit::auditRecordText("Test - Get Vehicle Trip Summary By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Vehicle Trip Summary By ID", "TC-XXX", 'TC-XXX test_get_vehicle_trip_summary_by_id', json_encode($data));
    }

    public function test_put_update_trip_by_id(): void
    {
        $body = [
            'vehicle_id' => $this->vehicleId, 
            'trip_desc' => 'jalan2', 
            'trip_category' => 'Business Trip',
            'trip_person' => 'John Doe', 
            'trip_origin_name' => 'Place B', 
            'trip_origin_coordinate' => '-6.226828716225759, 106.82152290589822',  
            'trip_destination_name' => 'Place D',
            'trip_destination_coordinate' => '-6.230792280916382, 106.81781530380249', 
        ];

        // Exec
        $response = $this->httpClient->put($this->tripId, [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
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
