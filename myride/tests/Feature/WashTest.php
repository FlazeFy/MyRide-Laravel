<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use GuzzleHttp\Client;
use Tests\TestCase;

// Helper
use App\Helpers\Audit;

class WashTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/wash/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_wash_history(): void
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
            $check_object = ["id", "vehicle_name", "vehicle_plate_number", "wash_desc", "wash_by", "is_wash_body", "is_wash_window", 
                "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "wash_address", 
                "wash_start_time", "wash_end_time", "is_fill_window_washing_water",  "is_wash_hollow", "created_at", "updated_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_plate_number", "wash_start_time", "created_at"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["wash_desc", "wash_by", "wash_address", "wash_end_time", "updated_at"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", 
                "is_wash_carpet", "is_wash_pillows", "is_fill_window_washing_water", "is_wash_hollow"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertTrue($dt[$col] === 0 || $dt[$col] === 1);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Wash History", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Wash History", "TC-XXX", 'TC-XXX test_get_all_wash_history', json_encode($data));
    }

    public function test_hard_delete_wash_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "ab8b8d0e-d74d-11ed-afa1-0242ac120002";
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
        $this->assertEquals('wash permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Wash By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Wash By Id", "TC-XXX", 'TC-XXX test_hard_delete_wash_by_id', json_encode($data));
    }

    public function test_post_create_wash(): void
    {
        // Exec
        $token = $this->login_trait("user");

        $body = [
            'vehicle_id' => '3fd091f0-68e9-87e8-0b38-ff129e29e0af',
            'wash_desc' => 'Full body and interior wash',
            'wash_by' => 'Car Wash',
            'is_wash_body' => 1,
            'is_wash_window' => 1,
            'is_wash_dashboard' => 0,
            'is_wash_tires' => 1,
            'is_wash_trash' => 1,
            'is_wash_engine' => 0,
            'is_wash_seat' => 1,
            'is_wash_carpet' => 1,
            'is_wash_pillows' => 0,
            'is_wash_hollow' => 0,
            'wash_address' => 'Jl. Raya No. 12',
            'wash_start_time' => '2025-12-16 14:00:00',
            'wash_end_time' => '2025-12-16 15:30:00',
            'wash_price' => 150000,
            'is_fill_window_washing_water' => 1,
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
        $this->assertEquals("wash created", $data['message']);

        Audit::auditRecordText("Test - Post Create Wash", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Wash", "TC-XXX", 'TC-XXX test_post_create_wash', json_encode($data));
    }

    public function test_put_update_wash_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "d43b7860-d3cd-3671-25cc-d792cef3cdd4";

        $body = [
            'vehicle_id' => '3fd091f0-68e9-87e8-0b38-ff129e29e0af',
            'wash_desc' => 'Full body and interior wash',
            'wash_by' => 'Car Wash',
            'is_wash_body' => 1,
            'is_wash_window' => 1,
            'is_wash_dashboard' => 0,
            'is_wash_tires' => 1,
            'is_wash_trash' => 1,
            'is_wash_engine' => 0,
            'is_wash_seat' => 1,
            'is_wash_carpet' => 1,
            'is_wash_pillows' => 0,
            'is_wash_hollow' => 0,
            'wash_address' => 'Jl. Raya No. 14',
            'wash_start_time' => '2025-12-16 14:00:00',
            'wash_end_time' => '2025-12-16 15:30:00',
            'wash_price' => 120000,
            'is_fill_window_washing_water' => 1,
        ];

        $response = $this->httpClient->put("$id", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("wash updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Wash By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Wash By ID", "TC-XXX", 'TC-XXX test_put_update_wash_by_id', json_encode($data));
    }

    public function test_put_finish_wash_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "d43b7860-d3cd-3671-25cc-d792cef3cdd4";

        $response = $this->httpClient->put("finish/$id", [
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
        $this->assertEquals("wash updated", $data['message']);

        Audit::auditRecordText("Test - Put Finish Wash By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Finish Wash By ID", "TC-XXX", 'TC-XXX test_put_finish_wash_by_id', json_encode($data));
    }

    public function test_get_wash_summary_by_vehicle_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("summary", [
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
            $check_object = ["vehicle_name", "vehicle_plate_number", "vehicle_type", "total_wash","total_wash_body","total_wash_window","total_wash_dashboard","total_wash_tires","total_wash_trash",
                "total_wash_engine","total_wash_seat","total_wash_carpet","total_wash_pillows","total_fill_window_washing_water","total_wash_hollow","total_price","avg_price_per_wash"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["vehicle_name", "vehicle_plate_number", "vehicle_type"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_not_null_int = ["total_wash","total_wash_body","total_wash_window","total_wash_dashboard","total_wash_tires","total_wash_trash",
            "total_wash_engine","total_wash_seat","total_wash_carpet","total_wash_pillows","total_fill_window_washing_water","total_wash_hollow","total_price","avg_price_per_wash"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertGreaterThanOrEqual(0, $dt[$col]);
            }
        }

        Audit::auditRecordText("Test - Get Wash Summary By Vehicle ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Wash Summary By Vehicle ID", "TC-XXX", 'TC-XXX test_get_wash_summary_by_vehicle_id', json_encode($data));
    }

    public function test_get_last_wash_by_vehicle_id(): void
    {
        $token = $this->login_trait("user");
        $vehicle_id = "c73c179b-a204-2b8f-2c46-5e77091462b0";

        // Exec
        $response = $this->httpClient->get("last/$vehicle_id", [
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

        $check_object = ["wash_desc", "wash_by", "is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", 
            "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "wash_address", "is_fill_window_washing_water",  "is_wash_hollow", "created_at"];

        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $data['data']);
        }

        $check_not_null_str = ["created_at","wash_by"];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsString($data['data'][$col]);
        }

        $check_nullable_str = ["wash_desc", "wash_address"];
        foreach ($check_nullable_str as $col) {
            if (!is_null($data['data'][$col])) {
                $this->assertIsString($data['data'][$col]);
            }
        }

        $check_not_null_int = ["is_wash_body", "is_wash_window", "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", 
            "is_wash_carpet", "is_wash_pillows", "is_fill_window_washing_water", "is_wash_hollow"];
        foreach ($check_not_null_int as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsInt($data['data'][$col]);
            $this->assertTrue($data['data'][$col] === 0 || $data['data'][$col] === 1);
        }

        Audit::auditRecordText("Test - Get Last Wash By Vehicle ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Last Wash By Vehicle ID", "TC-XXX", 'TC-XXX test_get_last_wash_by_vehicle_id', json_encode($data));
    }
}
