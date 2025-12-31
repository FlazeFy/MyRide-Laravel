<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;

class DriverTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/driver/',
            'http_errors' => false
        ]);
    }

    public function test_hard_delete_driver_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "70ff5a26-a2cf-11f0-86ad-3216422910e8";
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
        $this->assertEquals('driver permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Driver By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Driver By Id", "TC-XXX", 'TC-XXX test_hard_delete_driver_by_id', json_encode($data));
    }

    public function test_get_all_driver(): void
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
            $check_object = ['id', 'username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'created_at', 'updated_at'];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ['id', 'username', 'fullname', 'email', 'phone', 'created_at'];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ['telegram_user_id', 'notes','updated_at'];
            foreach ($check_nullable_str as $col) {
                if($dt[$col]){
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["telegram_is_valid"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertContains($dt[$col], [0, 1]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }
       
        Audit::auditRecordText("Test - Get All Driver", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Driver", "TC-XXX", 'TC-XXX test_get_all_driver', json_encode($data));
    }

    public function test_get_driver_vehicle(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("vehicle", [
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
            $check_object = ['username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'vehicle_list'];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ['username', 'fullname', 'email', 'phone','vehicle_list'];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ['telegram_user_id'];
            foreach ($check_nullable_str as $col) {
                if($dt[$col]){
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["telegram_is_valid"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertContains($dt[$col], [0, 1]);
            }
        }
       
        Audit::auditRecordText("Test - Get Driver Vehicle", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Driver Vehicle", "TC-XXX", 'TC-XXX test_get_driver_vehicle', json_encode($data));
    }

    public function test_get_driver_vehicle_manage_list(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("vehicle/list", [
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

        $check_object_vehicle = ['id', 'vehicle_name', 'vehicle_plate_number'];
        foreach ($data['data']['vehicle'] as $dt) {
            foreach ($check_object_vehicle as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
                $this->assertEquals(36,strlen($dt['id']));
            }
        }

        $check_object_driver = ['id', 'username', 'fullname'];
        foreach ($data['data']['driver'] as $dt) {
            foreach ($check_object_driver as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
                $this->assertEquals(36,strlen($dt['id']));
            }
        }

        $check_object_assigned = ['id', 'vehicle_plate_number', 'vehicle_id', 'driver_id', 'username', 'fullname'];
        foreach ($data['data']['assigned'] as $dt) {
            foreach ($check_object_assigned as $col) {
                $col_ids = ['id','vehicle_id','driver_id'];
                foreach ($col_ids as $col_id) {
                    $this->assertEquals(36,strlen($dt[$col_id]));
                }
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }
       
        Audit::auditRecordText("Test - Get Driver Vehicle Manage List", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Driver Vehicle Manage List", "TC-XXX", 'TC-XXX test_get_driver_vehicle_manage_listr', json_encode($data));
    }
}
