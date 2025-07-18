<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;
use App\Helpers\Audit;

class DictionaryTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/dictionary/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_dictionary_by_type(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $type = "trip_category";
        $response = $this->httpClient->get("type/$type", [
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
            $this->assertArrayHasKey('dictionary_name', $dt);
            $this->assertArrayHasKey('dictionary_type', $dt);

            $this->assertNotNull($dt['dictionary_name']);
            $this->assertIsString($dt['dictionary_name']);

            $this->assertNotNull($dt['dictionary_type']);
            $this->assertIsString($dt['dictionary_type']);
        }

        Audit::auditRecordText("Test - Get All Dictionary By Type", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Dictionary By Type", "TC-XXX", 'TC-XXX test_get_all_dictionary_by_type', json_encode($data));
    }

    public function test_hard_delete_dictionary_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "20a41534-875a-11ee-8f4a-3216422910e9";
        $response = $this->httpClient->delete("$id", [
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
        $this->assertEquals('dictionary permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Dictionary By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Dictionary By Id", "TC-XXX", 'TC-XXX test_hard_delete_dictionary_by_id', json_encode($data));
    }

    public function test_post_dictionary(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $body = [
            "dictionary_type" => "trip_category",
            "dictionary_name" => $this->faker->word
        ];
        $response = $this->httpClient->post("", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('dictionary created',$data['message']);

        Audit::auditRecordText("Test - Post Dictionary", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Dictionary", "TC-XXX", 'TC-XXX test_post_dictionary', json_encode($data));
    }
}
