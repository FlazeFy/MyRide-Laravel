<?php

namespace Tests\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;
use App\Helpers\TestDataReader;

class ClosingTest extends TestCase
{
    protected $httpClient;
    protected string $token;
    protected string $vehicleId;
    protected string $fuelId;
    protected string $tripId;
    protected string $washId;
    protected string $reminderId;
    protected string $serviceId;
    protected string $inventoryId;
    protected string $driverId;
    protected string $driverRelationId;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/',
            'http_errors' => false
        ]);

        // Pre-Condition: User already sign in
        $this->token = $this->login_trait("user");
        // Pre-Condition: At least a vehicle exists
        $this->vehicleId = TestDataReader::getValue('vehicle_id') ?? "";
        // Pre-Condition: At least a fuel exists
        $this->fuelId = TestDataReader::getValue('fuel_id') ?? "";
        // Pre-Condition: At least a trip exists
        $this->tripId = TestDataReader::getValue('trip_id') ?? "";
        // Pre-Condition: At least a reminder exists
        $this->reminderId = TestDataReader::getValue('reminder_id') ?? "";
        // Pre-Condition: At least a inventory exists
        $this->inventoryId = TestDataReader::getValue('inventory_id') ?? "";
        // Pre-Condition: At least a service exists
        $this->serviceId = TestDataReader::getValue('service_id') ?? "";
        // Pre-Condition: At least a wash exists
        $this->washId = TestDataReader::getValue('wash_id') ?? "";
        // Pre-Condition: At least a driver exists
        $this->driverId = TestDataReader::getValue('driver_id') ?? "";
        // Pre-Condition: At least a driver relation exists
        $this->driverRelationId = TestDataReader::getValue('driver_relation_id') ?? "";
    }

    public function test_get_vehicle_trip_summary_by_id(): void
    {
        // Exec
        $response = $this->httpClient->get("vehicle/trip/summary/".$this->vehicleId, [
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

    public function test_get_vehicle_full_detail_by_id(): void
    {
        // Exec
        $response = $this->httpClient->get("vehicle/detail/full/".$this->vehicleId, [
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

        $detail_data = $data['data']['detail'];
        $trip_data = $data['data']['trip']['data'];
        $wash_data = $data['data']['wash']['data'];
        $driver_data = $data['data']['driver'];
        
        // Test Detail Data
        $check_object_detail = ["id","vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_price", "vehicle_desc", 
            "vehicle_distance", "vehicle_category", "vehicle_status", "vehicle_year_made", "vehicle_plate_number", 
            "vehicle_fuel_status", "vehicle_fuel_capacity", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission", 
            "vehicle_img_url", "vehicle_other_img_url", "vehicle_capacity", "vehicle_document", "created_at", "updated_at", "deleted_at"];

        foreach ($check_object_detail as $col) {
            $this->assertArrayHasKey($col, $detail_data);
        }

        $check_not_null_str_detail = ["id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_category", "vehicle_status", "vehicle_plate_number", 
            "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission", "created_at"];
        foreach ($check_not_null_str_detail as $col) {
            $this->assertNotNull($detail_data[$col]);
            $this->assertIsString($detail_data[$col]);
        }

        $check_nullable_str_detail = ["vehicle_desc", "vehicle_img_url", "updated_at", "deleted_at"];
        foreach ($check_nullable_str_detail as $col) {
            if (!is_null($detail_data[$col])) {
                $this->assertIsString($detail_data[$col]);
            }
        }

        $check_not_null_int_detail = ["vehicle_price", "vehicle_distance", "vehicle_capacity"];
        foreach ($check_not_null_int_detail as $col) {
            $this->assertNotNull($detail_data[$col]);
            $this->assertIsInt($detail_data[$col]);
            $this->assertGreaterThan(0, $detail_data[$col]);
        }

        $check_nullable_int_detail = ["vehicle_fuel_capacity"];
        foreach ($check_nullable_int_detail as $col) {
            if (!is_null($detail_data[$col])) {
                $this->assertIsInt($detail_data[$col]);
                $this->assertGreaterThan(0, $detail_data[$col]);
            }
        }

        if (!is_null($detail_data['vehicle_document'])) {
            foreach ($detail_data['vehicle_document'] as $dt) {
                $check_object_detail_doc = ["id", "attach_type", "attach_name", "attach_url"];

                foreach ($check_object_detail_doc as $col) {
                    $this->assertArrayHasKey($col, $dt);
                }

                $check_not_null_str_detail_doc = ["id", "attach_type", "attach_name", "attach_url"];
                foreach ($check_not_null_str_detail_doc as $col) {
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }
        }

        $this->assertEquals(36,strlen($detail_data['id']));

        // Test Detail Data Trip
        foreach ($trip_data as $dt) {
            $check_object_trip = ["id", "trip_desc", "trip_category", "trip_origin_name", "trip_person", "trip_origin_coordinate", "trip_destination_name", "trip_destination_coordinate", "created_at"];

            foreach ($check_object_trip as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str_trip = ["id", "trip_desc", "trip_category", "trip_origin_name", "trip_destination_name", "created_at"];
            foreach ($check_not_null_str_trip as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str_trip = ["trip_person", "trip_origin_coordinate", "trip_destination_coordinate"];
            foreach ($check_nullable_str_trip as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        // Test Detail Data Wash
        foreach ($wash_data as $dt) {
            $check_object = ["id", "wash_desc", "wash_by", "is_wash_body", "is_wash_window", 
                "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "wash_address", 
                "wash_start_time", "wash_end_time", "is_fill_window_washing_water",  "is_wash_hollow", "created_at", "updated_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "wash_start_time", "created_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["wash_desc", "wash_by", "wash_address", "wash_end_time", "updated_at"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", 
                "is_wash_carpet", "is_wash_pillows", "is_fill_window_washing_water", "is_wash_hollow"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertTrue($dt[$col] === 0 || $dt[$col] === 1);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        // Test Detail Data Driver
        foreach ($driver_data as $dt) {
            $check_object = ['username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'assigned_at'];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ['username', 'fullname', 'email', 'phone', 'assigned_at'];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ['telegram_user_id', 'notes'];
            foreach ($check_nullable_str as $col) {
                if ($dt[$col]) {
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }

            $this->assertNotNull($dt["telegram_is_valid"]);
            $this->assertIsInt($dt["telegram_is_valid"]);
            $this->assertContains($dt["telegram_is_valid"], [0, 1]);
        }

        Audit::auditRecordText("Test - Get Vehicle Full Detail By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Vehicle Full Detail By ID", "TC-XXX", 'TC-XXX test_get_vehicle_full_detail_by_id', json_encode($data));
    }

    public function test_hard_delete_reminder_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("reminder/destroy/".$this->reminderId, [
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
        $this->assertEquals('reminder permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Reminder By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Reminder By Id", "TC-XXX", 'TC-XXX test_hard_delete_reminder_by_id', json_encode($data));
    }

    public function test_hard_delete_wash_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("wash/destroy/".$this->washId, [
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
        $this->assertEquals('wash permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Wash By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Wash By Id", "TC-XXX", 'TC-XXX test_hard_delete_wash_by_id', json_encode($data));
    }

    public function test_hard_delete_service_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("service/destroy/".$this->serviceId, [
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
        $this->assertEquals('service permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Service By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Service By Id", "TC-XXX", 'TC-XXX test_hard_delete_service_by_id', json_encode($data));
    }

    public function test_hard_delete_trip_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("trip/destroy/".$this->tripId, [
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
        $this->assertEquals('trip permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Trip By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Trip By Id", "TC-XXX", 'TC-XXX test_hard_delete_trip_by_id', json_encode($data));
    }

    public function test_hard_delete_fuel_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("fuel/destroy/".$this->fuelId, [
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
        $this->assertEquals('fuel permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Fuel By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Fuel By Id", "TC-XXX", 'TC-XXX test_hard_delete_fuel_by_id', json_encode($data));
    }

    public function test_hard_delete_inventory_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("inventory/destroy/".$this->inventoryId, [
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
        $this->assertEquals('inventory permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Inventory By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Inventory By Id", "TC-XXX", 'TC-XXX test_hard_delete_inventory_by_id', json_encode($data));
    }

    public function test_hard_delete_driver_relation_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("driver/destroy/relation/".$this->driverRelationId, [
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
        $this->assertEquals('driver relation permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Driver Relation By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Driver Relation By Id", "TC-XXX", 'TC-XXX test_hard_delete_driver_relation_by_id', json_encode($data));
    }

    public function test_hard_delete_driver_by_id(): void
    {
        // Exec
        $response = $this->httpClient->delete("driver/destroy/".$this->driverId, [
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
        $this->assertEquals('driver permanently deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Driver By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Driver By Id", "TC-XXX", 'TC-XXX test_hard_delete_driver_by_id', json_encode($data));
    }

    public function test_hard_delete_vehicle_image_collection_by_id(): void
    {
        // Exec
        $image_id = "3be503d1-5566-1bd0-2864-9c25404294ca";

        $response = $this->httpClient->delete("vehicle/image_collection/destroy/".$this->vehicleId."/$image_id", [
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
        $this->assertEquals("vehicle image deleted", $data['message']);

        Audit::auditRecordText("Test - Hard Delete Vehicle Image Collection By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Vehicle Image Collection By ID", "TC-XXX", 'TC-XXX test_hard_delete_vehicle_image_collection_by_id', json_encode($data));
    }

    public function test_hard_delete_vehicle_document_by_id(): void
    {
        // Exec
        $doc_id = "304d5c4a-b0a7-8c1a-1cac-50efb3413403";

        $response = $this->httpClient->delete("vehicle/document/destroy/".$this->vehicleId."/$doc_id", [
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
        $this->assertEquals("vehicle document deleted", $data['message']);

        Audit::auditRecordText("Test - Hard Delete Vehicle Document By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Vehicle Document By ID", "TC-XXX", 'TC-XXX test_hard_delete_vehicle_document_by_id', json_encode($data));
    }

    public function test_hard_delete_vehicle_by_id(): void
    {
        $response = $this->httpClient->delete("vehicle/destroy/".$this->vehicleId, [
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
        $this->assertEquals("vehicle permanently deleted", $data['message']);

        Audit::auditRecordText("Test - Hard Delete Vehicle By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Vehicle By ID", "TC-XXX", 'TC-XXX test_hard_delete_vehicle_by_id', json_encode($data));
    }

    public function test_post_sign_out(): void
    {
        // Exec
        $response = $this->httpClient->post('/api/v1/logout', [
            'headers' => [
                'Authorization' => "Bearer ".$this->token,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);

        Audit::auditRecordText('Integration Test - Success Post Sign Out With Valid Token', 'TC-INT-AU-002-01', 'Result : '.json_encode($data));
        Audit::auditRecordSheet('Integration Test - Success Post Sign Out With Valid Token', 'TC-INT-AU-002-01', 'test_post_sign_out', json_encode($data));
    }
}
