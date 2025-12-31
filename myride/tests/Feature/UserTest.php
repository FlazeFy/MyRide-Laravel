<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;

class UserTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/user/',
            'http_errors' => false
        ]);
    }

    public function test_get_my_profile(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("my_profile", [
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

        $check_not_null_str = ['id','username','email','created_at','role'];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsString($data['data'][$col]);
        }

        $this->assertContains($data['data']['role'], ['user', 'admin']);
        $this->assertContains($data['data']['telegram_is_valid'], [0, 1]);
        if(!is_null($data['data']['telegram_user_id'])){
            $this->assertIsString($data['data']['telegram_user_id']);
        } else {
            $this->assertEquals(0,$data['data']['telegram_user_id']);
        }

        Audit::auditRecordText("Test - Get My Profile", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get My Profile", "TC-XXX", 'TC-XXX test_get_my_profile', json_encode($data));
    }

    public function test_get_all_user(): void
    {
        // Exec
        $token = $this->login_trait("admin");
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
            $check_object = ['id','username','email','telegram_user_id','telegram_is_valid','firebase_fcm_token','line_user_id','phone','timezone','created_at','updated_at'];
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ['id','username','email','created_at'];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ['telegram_user_id','firebase_fcm_token','line_user_id','phone','timezone','updated_at'];
            foreach ($check_nullable_str as $col) {
                if(!is_null($dt[$col])){
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }

            $this->assertContains($dt['telegram_is_valid'], [0, 1]);
        }

        Audit::auditRecordText("Test - Get All User", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All User", "TC-XXX", 'TC-XXX test_get_all_user', json_encode($data));
    }

    public function test_get_content_year(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("my_year", [
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

        foreach ($data['data'] as $dt) {
            $this->assertArrayHasKey('year', $dt);
            $this->assertNotNull($dt['year']);
            $this->assertIsInt($dt['year']);
            $this->assertGreaterThan(0, $dt['year']);
        }

        Audit::auditRecordText("Test - Get Content Year", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Content Year", "TC-XXX", 'TC-XXX test_get_content_year', json_encode($data));
    }
}
