<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            $check_object = ["reminder_title", "reminder_context", "reminder_body", "remind_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["reminder_title", "reminder_context", "reminder_body", "remind_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }

        Audit::auditRecordText("Test - Get All Reminder", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Reminder", "TC-XXX", 'TC-XXX test_get_all_reminder', json_encode($data));
    }

    public function test_hard_delete_reminder_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "94bd8c3e-17df-29f5-0f45-cc24a1ef7429";
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
