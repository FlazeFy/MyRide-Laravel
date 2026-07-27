<?php

namespace Tests\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;
use App\Helpers\TestDataReader;

class UserTest extends TestCase
{
    protected $httpClient;
    protected string $token;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/user/',
            'http_errors' => false
        ]);

        // Pre-Condition: User already sign in
        $this->token = $this->login_trait("user");
    }

    public function test_get_my_profile(): void
    {
        // Exec
        $response = $this->httpClient->get("my_profile", [
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

        $check_not_null_str = ['id','username','email','created_at','role'];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsString($data['data'][$col]);
        }

        $this->assertContains($data['data']['role'], ['user', 'admin']);
        $this->assertContains($data['data']['telegram_is_valid'], [0, 1]);
        !is_null($data['data']['telegram_user_id']) ? $this->assertIsString($data['data']['telegram_user_id']) : $this->assertEquals(0,$data['data']['telegram_user_id']);

        Audit::auditRecordText("Test - Get My Profile", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get My Profile", "TC-XXX", 'TC-XXX test_get_my_profile', json_encode($data));
    }

    public function test_get_content_year(): void
    {
        // Exec
        $response = $this->httpClient->get("my_year", [
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
            $this->assertArrayHasKey('year', $dt);
            $this->assertNotNull($dt['year']);
            $this->assertIsInt($dt['year']);
            $this->assertGreaterThan(0, $dt['year']);
        }

        Audit::auditRecordText("Test - Get Content Year", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Content Year", "TC-XXX", 'TC-XXX test_get_content_year', json_encode($data));
    }

    public function test_put_update_profile(): void
    {
        // Exec
        $body = [
            "email" => "flazen.edu@gmail.com",
            "username" => "flazefy"
        ];
        $response = $this->httpClient->put("update_profile", [
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
        $this->assertEquals('profile updated',$data['message']);

        Audit::auditRecordText("Test - Put Update Profile", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Profile", "TC-XXX", 'TC-XXX test_put_update_profile', json_encode($data));
    }

    public function test_put_update_telegram_id(): void
    {
        $body = [
            "telegram_user_id" => "1317625977"
        ];

        // Exec
        $response = $this->httpClient->put("update_telegram_id", [
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
        $this->assertEquals('telegram id updated! and validation has been sended to you',$data['message']);

        Audit::auditRecordText("Test - Put Update Telegram ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Telegram ID", "TC-XXX", 'TC-XXX test_put_update_telegram_id', json_encode($data));
    }

    public function test_put_validate_telegram_id(): void
    {
        $body = [
            "request_context" => "R8WEO4"
        ];

        // Exec
        $response = $this->httpClient->put("validate_telegram_id", [
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
        $this->assertEquals('telegram ID has been validated',$data['message']);

        Audit::auditRecordText("Test - Put Validate Telegram ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Validate Telegram ID", "TC-XXX", 'TC-XXX test_put_validate_telegram_id', json_encode($data));
    }
}
