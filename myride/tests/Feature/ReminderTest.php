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

class ReminderTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/reminder/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_reminder(): void
    {
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("", [
            'headers' => [
                'Authorization' => "Bearer $token"
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
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("next", [
            'headers' => [
                'Authorization' => "Bearer $token"
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
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("recently", [
            'headers' => [
                'Authorization' => "Bearer $token"
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
        $token = $this->login_trait("user");
        $vehicle_id = "ec936a2a-62d0-6101-10b4-1b9a730213da";

        $response = $this->httpClient->get("vehicle/$vehicle_id", [
            'headers' => [
                'Authorization' => "Bearer $token"
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

    public function test_post_create_reminder(): void
    {
        // Exec
        $token = $this->login_trait("user");
        
        // Create fake image
        $img1 = UploadedFile::fake()->image('image1.jpg');

        $form = [
            ['name' => 'vehicle_id', 'contents' => 'ec936a2a-62d0-6101-10b4-1b9a730213da'],
            ['name' => 'reminder_title', 'contents' => 'Routine service KM 50000'],
            ['name' => 'reminder_context', 'contents' => 'Service'], 
            ['name' => 'reminder_body', 'contents' => 'Lorem ipsum'],
            ['name' => 'reminder_location', 'contents' => '-6.230333799218126, 106.81866017790138'],
            ['name' => 'remind_at', 'contents' => '2026-01-12 00:00:00'],
            [
                'name' => 'reminder_image',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ]
        ];

        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer $token"
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

        Audit::auditRecordText("Test - Post Create Reminder", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Reminder", "TC-XXX", 'TC-XXX test_post_create_reminder', json_encode($data));
    }

    public function test_hard_delete_reminder_by_id(): void
    {
        $token = $this->login_trait("user");
        $id = "94bd8c3e-17df-29f5-0f45-cc24a1ef7429";

        // Exec
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
        $this->assertEquals('reminder permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Reminder By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Reminder By Id", "TC-XXX", 'TC-XXX test_hard_delete_reminder_by_id', json_encode($data));
    }
}
