<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
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

    public function test_post_create_inventory(): void
    {
        $token = $this->login_trait("user");

        // Create fake image
        $img1 = UploadedFile::fake()->image('image1.jpg');

        $form = [
            ['name' => 'vehicle_id', 'contents' => 'ac278923-14ab-cb8b-0ca5-6cb6cdff094b'],
            ['name' => 'inventory_name', 'contents' => 'Secondary Tire'],
            ['name' => 'inventory_category', 'contents' => 'Maintenance'], 
            ['name' => 'inventory_storage', 'contents' => 'Trunk'],
            ['name' => 'inventory_qty', 'contents' => 1],
            [
                'name' => 'inventory_image_url',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ]
        ];

        // Exec
        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'multipart' => $form
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("inventory created", $data['message']);

        Audit::auditRecordText("Test - Post Create Inventory", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Inventory", "TC-XXX", 'TC-XXX test_post_create_inventory', json_encode($data));
    }

    public function test_put_update_inventory_by_id(): void
    {
        $token = $this->login_trait("user");
        $id = "a892e8e3-f89e-8d74-064b-66d6b2dcb8af";

        $body = [
            'vehicle_id' => 'ac278923-14ab-cb8b-0ca5-6cb6cdff094b', 
            'inventory_name' => 'Secondary Tire', 
            'inventory_category' => 'Maintenance', 
            'inventory_qty' => 2, 
            'inventory_storage' => 'Trunk'
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
        $this->assertEquals("inventory updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Inventory By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Inventory By ID", "TC-XXX", 'TC-XXX test_put_update_inventory_by_id', json_encode($data));
    }

    public function test_get_inventory_by_vehicle_id(): void
    {
        $token = $this->login_trait("user");
        $vehicle_id = "ac278923-14ab-cb8b-0ca5-6cb6cdff094b";

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

        $check_object = ["id", "inventory_name", "inventory_category", "inventory_qty", "inventory_storage", "created_at"];
        $check_not_null_str = ["id", "inventory_name", "inventory_category", "inventory_storage", "created_at"];
        foreach ($data['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $this->assertNotNull($dt["inventory_qty"]);
            $this->assertIsInt($dt["inventory_qty"]);
            $this->assertGreaterThan(0,$dt["inventory_qty"]);
        }

        Audit::auditRecordText("Test - Get Inventory By Vehicle ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Inventory By Vehicle ID", "TC-XXX", 'TC-XXX test_get_inventory_by_vehicle_id', json_encode($data));
    }
}
