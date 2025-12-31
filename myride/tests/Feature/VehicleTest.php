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

class VehicleTest extends TestCase
{
    protected $httpClient;
    use LoginHelperTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/v1/vehicle/',
            'http_errors' => false
        ]);
    }

    public function test_get_all_vehicle_header(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("header", [
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
            $check_object = ["id", "vehicle_name", "vehicle_desc", "vehicle_merk", "vehicle_type", "vehicle_distance", "vehicle_category", "vehicle_status", 
                "vehicle_plate_number", "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color", "vehicle_capacity", "vehicle_img_url", "updated_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_category", "vehicle_status", 
                "vehicle_plate_number", "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ["vehicle_desc", "vehicle_img_url", "updated_at"];
            foreach ($check_nullable_str as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $check_not_null_int = ["vehicle_distance","vehicle_capacity"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertGreaterThan(0, $dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Vehicle Header", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Vehicle Header", "TC-XXX", 'TC-XXX test_get_all_vehicle_header', json_encode($data));
    }

    public function test_get_vehicle_readiness(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("readiness", [
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
            $check_object = ["id", "vehicle_name", "vehicle_type", "vehicle_status", "vehicle_plate_number", "vehicle_fuel_status", "vehicle_capacity", "vehicle_transmission", "deleted_at", "readiness"];
            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "vehicle_name", "vehicle_type", "vehicle_status", "vehicle_plate_number", "vehicle_fuel_status", "vehicle_transmission"];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            if (!is_null($dt["deleted_at"])) {
                $this->assertIsString($dt["deleted_at"]);
            }

            $check_not_null_int = ["vehicle_capacity","readiness"];
            foreach ($check_not_null_int as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsInt($dt[$col]);
                $this->assertGreaterThanOrEqual(0, $dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get Vehicle Readiness", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Vehicle Readiness", "TC-XXX", 'TC-XXX test_get_vehicle_readiness', json_encode($data));
    }

    public function test_get_vehicle_detail(): void
    {
        // Exec
        $vehicle_id = "4f33d5e4-de9f-11ed-b5ea-0242ac120002";
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("detail/$vehicle_id", [
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

        $check_object = ["id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_price", "vehicle_desc", 
            "vehicle_distance", "vehicle_category", "vehicle_status", "vehicle_year_made", "vehicle_plate_number", 
            "vehicle_fuel_status", "vehicle_fuel_capacity", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission", 
            "vehicle_img_url", "vehicle_other_img_url", "vehicle_capacity", "vehicle_document", "created_at", "updated_at", "deleted_at"];

        foreach ($check_object as $col) {
            $this->assertArrayHasKey($col, $data['data']);
        }

        $check_not_null_str = ["id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_category", "vehicle_status", "vehicle_plate_number", 
            "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission", "created_at"];
        foreach ($check_not_null_str as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsString($data['data'][$col]);
        }

        $check_nullable_str = ["vehicle_desc", "vehicle_img_url", "updated_at", "deleted_at"];
        foreach ($check_nullable_str as $col) {
            if (!is_null($data['data'][$col])) {
                $this->assertIsString($data['data'][$col]);
            }
        }

        $check_not_null_int = ["vehicle_price", "vehicle_distance", "vehicle_capacity"];
        foreach ($check_not_null_int as $col) {
            $this->assertNotNull($data['data'][$col]);
            $this->assertIsInt($data['data'][$col]);
            $this->assertGreaterThan(0, $data['data'][$col]);
        }

        $check_nullable_int = ["vehicle_fuel_capacity"];
        foreach ($check_nullable_int as $col) {
            if (!is_null($data['data'][$col])) {
                $this->assertIsInt($data['data'][$col]);
                $this->assertGreaterThan(0, $data['data'][$col]);
            }
        }

        if (!is_null($data['data']['vehicle_document'])) {
            foreach ($data['data']['vehicle_document'] as $dt) {
                $check_object = ["id", "attach_type", "attach_name", "attach_url"];

                foreach ($check_object as $col) {
                    $this->assertArrayHasKey($col, $dt);
                }

                $check_not_null_str = ["id", "attach_type", "attach_name", "attach_url"];
                foreach ($check_not_null_str as $col) {
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }
        }

        $this->assertEquals(36,strlen($data['data']['id']));

        Audit::auditRecordText("Test - Get All Vehicle Detail", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Vehicle Detail", "TC-XXX", 'TC-XXX test_get_all_vehicle_detail', json_encode($data));
    }

    public function test_get_all_vehicle_name(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("name", [
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
            $check_object = ["id", "vehicle_name", "vehicle_plate_number"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Vehicle Name", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Vehicle Name", "TC-XXX", 'TC-XXX test_get_all_vehicle_name', json_encode($data));
    }

    public function test_get_all_vehicle_fuel(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("fuel", [
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
            $check_object = ["id", "vehicle_name", "vehicle_plate_number", "vehicle_fuel_status"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $this->assertIsInt($dt["vehicle_fuel_capacity"]);
            $this->assertGreaterThan(0, $dt['vehicle_fuel_capacity']);

            $this->assertEquals(36,strlen($dt['id']));
        }

        Audit::auditRecordText("Test - Get All Vehicle Fuel", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get All Vehicle Fuel", "TC-XXX", 'TC-XXX test_get_all_vehicle_fuel', json_encode($data));
    }

    public function test_post_vehicle(): void
    {
        // Exec
        $token = $this->login_trait("user");
        
        // Create fake images
        $img1 = UploadedFile::fake()->image('image1.jpg');
        $img2 = UploadedFile::fake()->image('image2.jpg');
        $img3 = UploadedFile::fake()->image('image3.jpg');

        $form = [
            ['name' => 'vehicle_name', 'contents' => 'Kijang Innova 2.0 Type G MT'],
            ['name' => 'vehicle_merk', 'contents' => 'Toyota'],
            ['name' => 'vehicle_type', 'contents' => 'Minibus'], 
            ['name' => 'vehicle_price', 'contents' => 275000000],
            ['name' => 'vehicle_desc', 'contents' => 'sudah jarang digunakan 2'],
            ['name' => 'vehicle_distance', 'contents' => 90000],
            ['name' => 'vehicle_category', 'contents' => 'Parents Car'], 
            ['name' => 'vehicle_status', 'contents' => 'Available'], 
            ['name' => 'vehicle_year_made', 'contents' => 2011],
            ['name' => 'vehicle_plate_number', 'contents' => 'PA 1234 ZX'],
            ['name' => 'vehicle_fuel_status', 'contents' => 'Not Monitored'], 
            ['name' => 'vehicle_fuel_capacity', 'contents' => 50],
            ['name' => 'vehicle_default_fuel', 'contents' => 'Pertamina Pertalite'],
            ['name' => 'vehicle_color', 'contents' => 'White'],
            ['name' => 'vehicle_transmission', 'contents' => 'Manual'], 
            ['name' => 'vehicle_capacity', 'contents' => 8],
            [
                'name'     => 'vehicle_image',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ],
            [
                'name'     => 'vehicle_other_img_url[]',
                'contents' => fopen($img2->getPathname(), 'r'),
                'filename' => 'image2.jpg',
            ],
            [
                'name'     => 'vehicle_other_img_url[]',
                'contents' => fopen($img3->getPathname(), 'r'),
                'filename' => 'image3.jpg',
            ],
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
        $this->assertEquals("vehicle created", $data['message']);

        Audit::auditRecordText("Test - Post Vehicle", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Vehicle", "TC-XXX", 'TC-XXX test_post_vehicle', json_encode($data));
    }

    public function test_post_vehicle_doc(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "563e01b3-e06f-01a9-2fae-4d525177fdd1";
        
        // Create fake images & doc
        $img1 = UploadedFile::fake()->image('image1.jpg');
        $pdf1 = UploadedFile::fake()->create('document1.pdf', 100, 'application/pdf'); 

        $form = [
            [
                'name'     => 'vehicle_document[]',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ],
            [
                'name'     => 'vehicle_document[]',
                'contents' => fopen($pdf1->getPathname(), 'r'),
                'filename' => 'document1.pdf',
            ],
            [
                'name'     => 'vehicle_document_caption[]',
                'contents' => 'this is an image',
            ],
            [
                'name'     => 'vehicle_document_caption[]',
                'contents' => 'this is a doc',
            ],
        ];

        $response = $this->httpClient->post("doc/$id", [
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
        $this->assertEquals("vehicle document created", $data['message']);

        Audit::auditRecordText("Test - Post Vehicle Doc", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Vehicle Doc", "TC-XXX", 'TC-XXX test_post_vehicle_doc', json_encode($data));
    }

    public function test_hard_delete_vehicle_image_collection_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $vehicle_id = "563e01b3-e06f-01a9-2fae-4d525177fdd1";
        $image_id = "3be503d1-5566-1bd0-2864-9c25404294ca";

        $response = $this->httpClient->delete("image_collection/destroy/$vehicle_id/$image_id", [
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
        $this->assertEquals("vehicle image deleted", $data['message']);

        Audit::auditRecordText("Test - Hard Delete Vehicle Image Collection By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Vehicle Image Collection By ID", "TC-XXX", 'TC-XXX test_hard_delete_vehicle_image_collection_by_id', json_encode($data));
    }
}
