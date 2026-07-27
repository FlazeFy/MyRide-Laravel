<?php

namespace Tests\Integration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Models
use App\Models\FAQModel;
// Helper
use App\Helpers\Audit;

class QuestionTest extends TestCase
{
    protected $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/question/',
            'http_errors' => false
        ]);

        // Pre-Condition: At least one faq exists
        FAQModel::factory(1)->state(['is_show' => 1])->create();
        FAQModel::factory(1)->state(['is_show' => 0])->create();
    }

    public function test_get_showing_faq(): void
    {
        // Exec
        $response = $this->httpClient->get("faq");

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('data', $data);

        $context = ["faq_question","faq_answer"];

        foreach ($data['data'] as $dt) {
            foreach ($context as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }
        }

        Audit::auditRecordText("Test - Get Showing FAQ", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Showing FAQ", "TC-XXX", 'TC-XXX test_get_showing_faq', json_encode($data));
    }
}
