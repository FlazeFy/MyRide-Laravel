<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
use App\Helpers\Audit;

class AuthTest extends TestCase
{
    protected $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/',
            'http_errors' => false
        ]);
    }

    public function test_post_login()
    {
        // Exec
        $param = [
            'username' => 'flazefy',
            'password' => 'nopass123'
        ];
        $response = $this->httpClient->post("/api/v1/login", [
            'json' => $param
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('role', $data);
        $this->assertArrayHasKey('result', $data);

        $check_object = ['id','username','email','telegram_user_id','telegram_is_valid','created_at','updated_at'];
        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $data['result']);
        }

        $check_not_null_str = ['id','username','email','created_at'];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($col, $data['result'][$col]);
            $this->assertIsString($col, $data['result'][$col]);
        }

        $check_nullable_str = ['telegram_user_id','updated_at'];
        foreach ($check_nullable_str as $col) {
            if(!is_null($data['result'][$col])){
                $this->assertIsString($col, $data['result'][$col]);
            }
        }

        Audit::auditRecordText("Integration Test - Success Post Login With Valid Data", "TC-INT-AU-001-01", "Token : ".$data['token']);
        Audit::auditRecordSheet("Integration Test - Success Post Login With Valid Data", "TC-INT-AU-001-01", json_encode($param), $data['token']);
        return $data['token'];
    }

    public function test_post_sign_out(): void
    {
        // Exec
        $token = $this->test_post_login();
        $response = $this->httpClient->post("/api/v1/logout", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);

        Audit::auditRecordText("Integration Test - Success Post Sign Out With Valid Token", "TC-INT-AU-002-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Post Sign Out With Valid Token", "TC-INT-AU-002-01", 'test_post_sign_out', json_encode($data));
    }

    public function test_post_get_register_validation_token(): void
    {
        // Exec
        $payload = [
            "username" => "tester_01",
            "email" => "flazen.work@gmail.com",
        ];
        $response = $this->httpClient->post("/api/v1/register/token", [
            'json' => $payload
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals($data['message'],"the validation token has been sended to ".$payload['email']." email account");

        Audit::auditRecordText("Integration Test - Success Post Get Register Validation Token", "TC-INT-AU-003-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Post Get Register Validation Token", "TC-INT-AU-003-01", 'test_post_get_register_validation_token', json_encode($data));
    }

    public function test_post_validate_register(): void
    {
        // Exec
        $payload = [
            "username" => "tester_01",
            "email" => "flazen.work@gmail.com",
            "telegram_user_id" => "1317625977",
            "password" => "nopass123"
        ];
        $response = $this->httpClient->post("/api/v1/register/account", [
            'json' => $payload
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $check_keys_data = ['message','is_signed_in','token'];
        foreach ($check_keys_data as $dt) {
            $this->assertArrayHasKey($dt, $data);
        }
        $this->assertEquals($data['message'],"account is registered");

        Audit::auditRecordText("Integration Test - Success Post Validate Register With Valid Data", "TC-INT-AU-004-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Post Validate Register With Valid Data", "TC-INT-AU-004-01", 'test_post_validate_register', json_encode($data));
    }

    public function test_post_regenerate_register_token(): void
    {
        // Exec
        $payload = [
            "username" => "tester_01",
            "email" => "flazen.work@gmail.com",
        ];
        $response = $this->httpClient->post("/api/v1/register/regen_token", [
            'json' => $payload
        ]);

        $data = json_decode($response->getBody(), true);
        print_r($data);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals($data['message'],"the validation token has been sended to ".$payload['email']." email account");

        Audit::auditRecordText("Integration Test - Success Post Regenerate Register Token With Valid Data", "TC-INT-AU-005-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Post Regenerate Register Token With Valid Data", "TC-INT-AU-005-01", 'test_post_regenerate_register_token', json_encode($data));
    }
}
