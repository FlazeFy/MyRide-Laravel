<?php

namespace Tests\Feature;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;
use App\Helpers\TestDataReader;
// Models
use App\Models\UserModel;
use App\Models\ValidateRequestModel;

class AuthTest extends TestCase
{
    protected Client $httpClient;
    protected array $testUser;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        TestDataReader::clear();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/',
            'http_errors' => false,
        ]);

        if (TestDataReader::getValue('register_token') === null) {
            // Create new test account
            $this->testUser = UserModel::factory()->apiPayload()->raw();

            TestDataReader::setValue('username', $this->testUser['username']);
            TestDataReader::setValue('email', $this->testUser['email']);
            TestDataReader::setValue('password', 'nopass123');
        } else {
            // Read existing test account
            $this->testUser = [
                'username' => TestDataReader::getValue('username'),
                'email' => TestDataReader::getValue('email'),
                'password' => TestDataReader::getValue('password'),
            ];
        }
    }

    public function test_post_get_register_validation_token(): void
    {
        // Exec
        $payload = [
            'username' => $this->testUser['username'],
            'email' => $this->testUser['email'],
        ];

        $response = $this->httpClient->post('/api/v1/register/token', [
            'json' => $payload,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('the validation token has been sended to '.$payload['email'].' email account', $data['message']);

        Audit::auditRecordText('Integration Test - Success Post Get Register Validation Token', 'TC-INT-AU-003-01', 'Result : '.json_encode($data));
        Audit::auditRecordSheet('Integration Test - Success Post Get Register Validation Token', 'TC-INT-AU-003-01', 'test_post_get_register_validation_token', json_encode($data));
    }

    public function test_post_regenerate_register_token(): void
    {
        // Exec
        $payload = [
            'username' => $this->testUser['username'],
            'email' => $this->testUser['email'],
        ];

        $response = $this->httpClient->post('/api/v1/register/regen_token', [
            'json' => $payload,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('the validation token has been sended to ' . $payload['email'] . ' email account', $data['message']);

        // Get token from email alternative
        $response = $this->httpClient->get('/api/v1/user/validate_request/register/'.$payload['username'], [
            'headers' => [
                'X-API-KEY' => env('TESTING_API_KEY'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Store token
        TestDataReader::setValue('register_token', $data['data']);

        Audit::auditRecordText('Integration Test - Success Post Regenerate Register Token With Valid Data', 'TC-INT-AU-005-01', 'Result : '.json_encode($data));
        Audit::auditRecordSheet('Integration Test - Success Post Regenerate Register Token With Valid Data', 'TC-INT-AU-005-01', 'test_post_regenerate_register_token', json_encode($data));
    }

    public function test_post_validate_register(): void
    {
        // Exec
        $token = TestDataReader::getValue('register_token');

        $payload = array_merge($this->testUser, [
            'token' => $token,
        ]);

        $response = $this->httpClient->post('/api/v1/register/account', [
            'json' => $payload,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());

        $checkKeysData = ['message', 'is_signed_in', 'token'];
        foreach ($checkKeysData as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        $this->assertEquals('account is registered', $data['message']);

        Audit::auditRecordText("Integration Test - Success Post Validate Register With Valid Data", "TC-INT-AU-004-01", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Integration Test - Success Post Validate Register With Valid Data", "TC-INT-AU-004-01", "test_post_validate_register", json_encode($data));
    }

    public function test_post_login()
    {
        // Exec
        $payload = [
            'username' => $this->testUser['username'],
            'password' => $this->testUser['password'],
        ];

        $response = $this->httpClient->post('/api/v1/login', [
            'json' => $payload,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('role', $data);
        $this->assertArrayHasKey('message', $data);

        $checkObject = ['id', 'username', 'email', 'telegram_user_id', 'telegram_is_valid', 'created_at', 'updated_at'];
        foreach ($checkObject as $col) {
            $this->assertArrayHasKey($col, $data['message']);
        }

        $checkNotNullStr = ['id', 'username', 'email', 'created_at'];
        foreach ($checkNotNullStr as $col) {
            $this->assertNotNull($data['message'][$col]);
            $this->assertIsString($data['message'][$col]);
        }

        $checkNullableStr = ['telegram_user_id', 'updated_at'];
        foreach ($checkNullableStr as $col) {
            if (!is_null($data['message'][$col])) {
                $this->assertIsString($data['message'][$col]);
            }
        }

        $this->assertContains($data['message']['telegram_is_valid'], [0, 1]);

        Audit::auditRecordText('Integration Test - Success Post Login With Valid Data', 'TC-INT-AU-001-01', 'Token : '.$data['token']);
        Audit::auditRecordSheet('Integration Test - Success Post Login With Valid Data', 'TC-INT-AU-001-01', json_encode($payload), $data['token']);

        return $data['token'];
    }

    public function test_post_sign_out(): void
    {
        // Exec
        $token = $this->test_post_login();

        $response = $this->httpClient->post('/api/v1/logout', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);

        Audit::auditRecordText('Integration Test - Success Post Sign Out With Valid Token', 'TC-INT-AU-002-01', 'Result : '.json_encode($data));
        Audit::auditRecordSheet('Integration Test - Success Post Sign Out With Valid Token', 'TC-INT-AU-002-01', 'test_post_sign_out', json_encode($data));
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        // Remove this later
        ValidateRequestModel::truncate();
    }
}