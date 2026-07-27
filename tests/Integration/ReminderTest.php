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

class ReminderTest extends TestCase
{
    protected $httpClient;
    protected string $token;
    protected string $vehicleId;
    protected string $reminderId;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/reminder/',
            'http_errors' => false
        ]);

        // Pre-Condition: User already sign in
        $this->token = $this->login_trait("user");
        // Pre-Condition: At least a vehicle exists
        $this->vehicleId = TestDataReader::getValue('vehicle_id') ?? "";
        // Pre-Condition: At least a reminder exists
        $this->reminderId = TestDataReader::getValue('reminder_id') ?? "";
    }

    public function test_post_create_reminder(): void
    {
        // Create fake image
        $img1 = UploadedFile::fake()->image('image1.jpg');

        $form = [
            ['name' => 'vehicle_id', 'contents' => $this->vehicleId],
            ['name' => 'reminder_title', 'contents' => 'Routine service KM 50000'],
            ['name' => 'reminder_context', 'contents' => 'Service'], 
            ['name' => 'reminder_body', 'contents' => 'Lorem ipsum'],
            ['name' => 'reminder_location', 'contents' => '-6.230333799218126, 106.81866017790138'],
            ['name' => 'remind_at', 'contents' => date('Y-m-d H:i:s', strtotime('+1 week'))],
            [
                'name' => 'reminder_image',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ]
        ];

        // Exec
        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ],
            'multipart' => $form,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("reminder created", $data['message']);

        // Store all created data
        foreach ($form as $dt) {
            if (array_key_exists('filename', $dt)) continue; 
            TestDataReader::setValue($dt['name'], $dt['contents']);
        }
        TestDataReader::setValue('reminder_id', $data['data']['id']);

        Audit::auditRecordText("Test - Post Create Reminder", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Reminder", "TC-XXX", 'TC-XXX test_post_create_reminder', json_encode($data));
    }

    public function test_get_all_reminder(): void
    {
        $response = $this->httpClient->get("", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ]
        ]);

        // Exec
        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["reminder_title", "reminder_context", "reminder_body", "reminder_attachment", "remind_at"];
        foreach ($data['data']['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["reminder_title", "reminder_context", "reminder_body", "remind_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            if (!is_null($dt["reminder_attachment"])) {
                foreach ($dt["reminder_attachment"] as $dt_reminder_att) {
                    $check_object_attachment = ["attachment_type","attachment_value"];

                    foreach ($check_object_attachment as $col_att) {
                        $this->assertNotNull($dt_reminder_att[$col_att]);
                        $this->assertIsString($dt_reminder_att[$col_att]);
                        $this->assertContains($dt_reminder_att["attachment_type"], ["location","image"]);
                    }
                }
            }
        }

        Audit::auditRecordText("Test - Get All Reminder", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Reminder", "TC-XXX", 'TC-XXX test_get_all_reminder', json_encode($data));
    }

    public function test_get_next_reminder(): void
    {
        $response = $this->httpClient->get("next", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ]
        ]);

        // Exec
        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["reminder_title", "reminder_context", "reminder_body", "remind_at", "vehicle_plate_number"];
        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $data["data"]);
            $this->assertNotNull($data["data"][$col]);
            $this->assertIsString($data["data"][$col]);
        }
        
        Audit::auditRecordText("Test - Get Next Reminder", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Next Reminder", "TC-XXX", 'TC-XXX test_get_next_reminder', json_encode($data));
    }

    public function test_get_recently_reminder(): void
    {
        // Pre-Condition: At least one reminder is due within 3 days
        ReminderModel::factory()->withRemindAt(now()->subDays(1))->create();

        $response = $this->httpClient->get("recently", [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ]
        ]);

        // Exec
        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["id", "reminder_title", "reminder_context", "reminder_body", "remind_at", "vehicle_plate_number"];
        foreach ($data['data']['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }
        
        Audit::auditRecordText("Test - Get Recently Reminder", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Recently Reminder", "TC-XXX", 'TC-XXX test_get_recently_reminder', json_encode($data));
    }

    public function test_get_reminder_by_vehicle_id(): void
    {
        $response = $this->httpClient->get("vehicle/".$this->vehicleId, [
            'headers' => [
                'Authorization' => "Bearer ".$this->token
            ]
        ]);

        // Exec
        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $check_object = ["reminder_title", "reminder_context", "reminder_body", "remind_at"];
        foreach ($data['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }
        
        Audit::auditRecordText("Test - Get Reminder By Vehicle ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Reminder By Vehicle ID", "TC-XXX", 'TC-XXX test_get_reminder_by_vehicle_id', json_encode($data));
    }
}
