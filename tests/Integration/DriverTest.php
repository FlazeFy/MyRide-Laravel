<?php

namespace Tests\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;
use App\Helpers\TestDataReader;

class DriverTest extends TestCase
{
    protected $httpClient;
    protected string $token;
    protected string $vehicleId;
    protected string $driverId;
    protected string $driverRelationId;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/driver/',
            'http_errors' => false
        ]);

        // Pre-Condition: User already sign in
        $this->token = $this->login_trait("user");
        // Pre-Condition: At least a vehicle exists
        $this->vehicleId = TestDataReader::getValue('vehicle_id') ?? "";
        // Pre-Condition: At least a driver exists
        $this->driverId = TestDataReader::getValue('driver_id') ?? "";
    }

    public function test_post_create_driver(): void
    {
        $body = [
            'username' => 'tester_01',
            'fullname' => 'Tester User',
            'email' => 'flazen.work@gmail.com',
            'phone' => '08123456789',
            'notes' => 'Lorem ipsum',
            'telegram_user_id' => '1317625977',
            'password' => 'nopass123',
            'password_confirmation' => 'nopass123'
        ];

        // Exec
        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("driver created", $data['message']);

        // Store all created data
        foreach ($body as $key => $val) {
            TestDataReader::setValue("driver_$key", $val);
        }
        TestDataReader::setValue('driver_id', $data['data']['id']);

        Audit::auditRecordText("Test - Post Create Driver", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Driver", "TC-XXX", 'TC-XXX test_post_create_driver', json_encode($data));
    }

    public function test_post_create_driver_vehicle(): void
    {
        $body = [
            'vehicle_id' => $this->vehicleId,
            'driver_id' => $this->driverId,
            'relation_note' => 'Driver weekday'
        ];

        // Exec
        $response = $this->httpClient->post("vehicle", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("driver relation created", $data['message']);

        // Store all created data
        foreach ($body as $key => $val) {
            TestDataReader::setValue($key, $val);
        }
        TestDataReader::setValue('driver_relation_id', $data['data']['id']);

        Audit::auditRecordText("Test - Post Create Driver Vehicle", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Driver Vehicle", "TC-XXX", 'TC-XXX test_post_create_driver_vehicle', json_encode($data));
    }

    public function test_get_all_driver(): void
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
                if ($dt[$col]) {
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

    public function test_get_all_driver_name(): void
    {
        // Exec
        $response = $this->httpClient->get("name", [
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
            $check_object = ['id', 'username', 'fullname'];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }
       
        Audit::auditRecordText("Test - Get All Driver Name", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Driver Name", "TC-XXX", 'TC-XXX test_get_all_driver_name', json_encode($data));
    }

    public function test_put_update_driver_by_id(): void
    {
        $body = [
            'username' => 'tester_01',
            'fullname' => 'Tester User',
            'email' => 'flazen.study@gmail.com',
            'phone' => '08123456789',
            'notes' => 'Lorem ipsum test',
        ];

        // Exec
        $response = $this->httpClient->put($this->driverId, [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("driver updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Driver By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Driver By ID", "TC-XXX", 'TC-XXX test_put_update_driver_by_id', json_encode($data));
    }

    public function test_get_driver_vehicle(): void
    {
        // Exec
        $response = $this->httpClient->get("vehicle", [
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
                if ($dt[$col]) {
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
        $response = $this->httpClient->get("vehicle/list", [
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
