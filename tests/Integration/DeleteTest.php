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

class DeleteTest extends TestCase
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
}
