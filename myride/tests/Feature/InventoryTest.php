<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;

class InventoryTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/inventory/',
            'http_errors' => false
        ]);
    }

    public function test_hard_delete_inventory_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "2599322c-a232-11ee-8c90-0242ac120002";
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
        $this->assertEquals('inventory permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Inventory By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Inventory By Id", "TC-XXX", 'TC-XXX test_hard_delete_inventory_by_id', json_encode($data));
    }

    public function test_get_all_inventory(): void
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
            $check_object = ['id','inventory_name','inventory_category','inventory_qty','inventory_storage','inventory_image_url','created_at','updated_at','vehicle_plate_number','vehicle_type'];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ['id','inventory_name','inventory_category','inventory_storage','created_at','vehicle_plate_number','vehicle_type'];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_not_null_int = ["inventory_qty"];
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
