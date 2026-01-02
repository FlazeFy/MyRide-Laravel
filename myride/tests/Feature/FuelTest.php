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

class FuelTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/fuel/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_fuel(): void
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

        $check_object = ["id", "vehicle_type", "vehicle_plate_number", "fuel_volume", "fuel_price_total", "fuel_brand", "fuel_type", "fuel_ron", "created_at", "fuel_bill"];
        $check_not_null_str = ["id", "vehicle_type", "vehicle_plate_number", "fuel_brand", "created_at"];
        $check_nullable_int = ["fuel_price_total", "fuel_ron"];
        $check_nullable_str = ["fuel_type","fuel_bill"];

        foreach ($data['data']['data'] as $dt) {
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            foreach ($check_nullable_int as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsInt($dt[$col]);
                    $this->assertGreaterThan(0, $dt[$col]);
                }
            }
        
            $this->assertIsInt($dt["fuel_volume"]);
            $this->assertGreaterThan(0, $dt["fuel_volume"]);

            if(!is_null($dt["fuel_ron"])){
                $this->assertContains($dt["fuel_ron"], [90,92,95,98]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Fuel", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Fuel", "TC-XXX", 'TC-XXX test_get_all_fuel', json_encode($data));
    }

    public function test_get_last_fuel(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("last", [
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

        $check_object = ["vehicle_type", "vehicle_plate_number", "fuel_volume", "fuel_price_total", "fuel_brand", "fuel_type", "fuel_ron", "created_at"];
        $check_not_null_str = ["vehicle_type", "vehicle_plate_number", "fuel_brand", "created_at"];
        $check_nullable_int = ["fuel_price_total", "fuel_ron"];
        $check_nullable_str = ["fuel_type"];

        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $data['data']);
        }

        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsString($data['data'][$col]);
        }

        foreach ($check_nullable_str as $col) {
            if (!is_null($data['data'][$col])) {
                $this->assertIsString($data['data'][$col]);
            }
        }

        foreach ($check_nullable_int as $col) {
            if (!is_null($data['data'][$col])) {
                $this->assertIsInt($data['data'][$col]);
                $this->assertGreaterThan(0, $data['data'][$col]);
            }
        }
    
        $this->assertIsInt($data['data']["fuel_volume"]);
        $this->assertGreaterThan(0, $data['data']["fuel_volume"]);

        if(!is_null($data['data']["fuel_ron"])){
            $this->assertContains($data['data']["fuel_ron"], [90,92,95,98]);
        }

        Audit::auditRecordText("Test - Get Last Fuel", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Last Fuel", "TC-XXX", 'TC-XXX test_get_last_fuel', json_encode($data));
    }

    public function test_hard_delete_fuel_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "1d2eb3ed-ab10-610b-2475-35789f801dc3";
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
        $this->assertEquals('fuel permentally deleted',$data['message']);

        Audit::auditRecordText("Test - Hard Delete Fuel By Id", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Fuel By Id", "TC-XXX", 'TC-XXX test_hard_delete_fuel_by_id', json_encode($data));
    }

    public function test_get_monthly_fuel_summary(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $month_year = "09-2025";
        $response = $this->httpClient->get("summary/$month_year", [
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

        $check_not_nullable_int = ['total_fuel_price','total_fuel_volume'];
        foreach ($check_not_nullable_int as $col) {
            $this->assertNotNull($data['data'][$col]);
            
            if($data['data'][$col]){
                $this->assertIsInt($data['data'][$col]);
            }
        }

        $check_not_null_int = ['total_refueling'];
        foreach ($check_not_null_int as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsInt($data['data'][$col]);
        }

        Audit::auditRecordText("Test - Get Monthly Fuel Summary", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Monthly Fuel Summary", "TC-XXX", 'TC-XXX test_get_monthly_fuel_summary', json_encode($data));
    }

    public function test_post_create_fuel(): void
    {
        $token = $this->login_trait("user");

        $fuelBill = UploadedFile::fake()->image('fuel_bill.jpg');

        $form = [
            ['name' => 'vehicle_id', 'contents' => 'ac278923-14ab-cb8b-0ca5-6cb6cdff094b'],
            ['name' => 'fuel_volume', 'contents' => 20],
            ['name' => 'fuel_price_total', 'contents' => 300000],
            ['name' => 'fuel_brand', 'contents' => 'Shell'],
            ['name' => 'fuel_type', 'contents' => 'Shell Super'],
            ['name' => 'fuel_ron', 'contents' => 92],
            [
                'name' => 'fuel_bill', 
                'contents' => fopen($fuelBill->getPathname(), 'r'), 
                'filename' => 'fuel_bill.jpg'
            ],
        ];

        // Exec
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
        $this->assertEquals("fuel created", $data['message']);

        Audit::auditRecordText("Test - Post Create Fuel", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Create Fuel", "TC-XXX", 'TC-XXX test_post_create_fuel', json_encode($data));
    }

    public function test_put_update_fuel_by_id(): void
    {
        $token = $this->login_trait("user");
        $id = "de1dc721-44db-e42a-013e-229537e7754b";

        $body = [
            'vehicle_id' => 'ac278923-14ab-cb8b-0ca5-6cb6cdff094b', 
            'fuel_volume' => 20,
            'fuel_price_total' => 300000, 
            'fuel_brand' => 'Pertamina', 
            'fuel_type' => 'Pertamax Turbo', 
            'fuel_ron' => 98, 
        ];

        // Exec
        $response = $this->httpClient->put("$id", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("fuel updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Fuel By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Fuel By ID", "TC-XXX", 'TC-XXX test_put_update_fuel_by_id', json_encode($data));
    }
}
