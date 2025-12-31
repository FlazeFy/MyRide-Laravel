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

    public function test_get_vehicle_detail_by_id(): void
    {
        // Exec
        $vehicle_id = "3fd091f0-68e9-87e8-0b38-ff129e29e0af";
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

        Audit::auditRecordText("Test - Get Vehicle Detail By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Vehicle Detail By ID", "TC-XXX", 'TC-XXX test_get_vehicle_detail_by_id', json_encode($data));
    }

    public function test_get_vehicle_full_detail_by_id(): void
    {
        // Exec
        $vehicle_id = "3fd091f0-68e9-87e8-0b38-ff129e29e0af";
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("detail/full/$vehicle_id", [
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

        $detail_data = $data['data']['detail'];
        $trip_data = $data['data']['trip']['data'];
        $wash_data = $data['data']['wash']['data'];
        $driver_data = $data['data']['driver'];
        
        // Test Detail Data
        $check_object_detail = ["id","vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_price", "vehicle_desc", 
            "vehicle_distance", "vehicle_category", "vehicle_status", "vehicle_year_made", "vehicle_plate_number", 
            "vehicle_fuel_status", "vehicle_fuel_capacity", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission", 
            "vehicle_img_url", "vehicle_other_img_url", "vehicle_capacity", "vehicle_document", "created_at", "updated_at", "deleted_at"];

        foreach ($check_object_detail as $col) {
            $this->assertArrayHasKey($col, $detail_data);
        }

        $check_not_null_str_detail = ["id", "vehicle_name", "vehicle_merk", "vehicle_type", "vehicle_category", "vehicle_status", "vehicle_plate_number", 
            "vehicle_fuel_status", "vehicle_default_fuel", "vehicle_color", "vehicle_transmission", "created_at"];
        foreach ($check_not_null_str_detail as $col) {
            $this->assertNotNull($detail_data[$col]);
            $this->assertIsString($detail_data[$col]);
        }

        $check_nullable_str_detail = ["vehicle_desc", "vehicle_img_url", "updated_at", "deleted_at"];
        foreach ($check_nullable_str_detail as $col) {
            if (!is_null($detail_data[$col])) {
                $this->assertIsString($detail_data[$col]);
            }
        }

        $check_not_null_int_detail = ["vehicle_price", "vehicle_distance", "vehicle_capacity"];
        foreach ($check_not_null_int_detail as $col) {
            $this->assertNotNull($detail_data[$col]);
            $this->assertIsInt($detail_data[$col]);
            $this->assertGreaterThan(0, $detail_data[$col]);
        }

        $check_nullable_int_detail = ["vehicle_fuel_capacity"];
        foreach ($check_nullable_int_detail as $col) {
            if (!is_null($detail_data[$col])) {
                $this->assertIsInt($detail_data[$col]);
                $this->assertGreaterThan(0, $detail_data[$col]);
            }
        }

        if (!is_null($detail_data['vehicle_document'])) {
            foreach ($detail_data['vehicle_document'] as $dt) {
                $check_object_detail_doc = ["id", "attach_type", "attach_name", "attach_url"];

                foreach ($check_object_detail_doc as $col) {
                    $this->assertArrayHasKey($col, $dt);
                }

                $check_not_null_str_detail_doc = ["id", "attach_type", "attach_name", "attach_url"];
                foreach ($check_not_null_str_detail_doc as $col) {
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }
        }

        $this->assertEquals(36,strlen($detail_data['id']));

        // Test Detail Data Trip
        foreach ($trip_data as $dt) {
            $check_object_trip = ["id", "trip_desc", "trip_category", "trip_origin_name", "trip_person", "trip_origin_coordinate", "trip_destination_name", "trip_destination_coordinate", "created_at"];

            foreach ($check_object_trip as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str_trip = ["id", "trip_desc", "trip_category", "trip_origin_name", "trip_destination_name", "created_at"];
            foreach ($check_not_null_str_trip as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str_trip = ["trip_person", "trip_origin_coordinate", "trip_destination_coordinate"];
            foreach ($check_nullable_str_trip as $col) {
                if (!is_null($dt[$col])) {
                    $this->assertIsString($dt[$col]);
                }
            }

            $this->assertEquals(36,strlen($dt['id']));
        }

        // Test Detail Data Wash
        foreach ($wash_data as $dt) {
            $check_object = ["id", "wash_desc", "wash_by", "is_wash_body", "is_wash_window", 
                "is_wash_dashboard", "is_wash_tires", "is_wash_trash", "is_wash_engine", "is_wash_seat", "is_wash_carpet", "is_wash_pillows", "wash_address", 
                "wash_start_time", "wash_end_time", "is_fill_window_washing_water",  "is_wash_hollow", "created_at", "updated_at"];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ["id", "wash_start_time", "created_at"];
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

        // Test Detail Data Driver
        foreach ($driver_data as $dt) {
            $check_object = ['username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'assigned_at'];

            foreach ($check_object as $col) {
                $this->assertArrayHasKey($col, $dt);
            }

            $check_not_null_str = ['username', 'fullname', 'email', 'phone', 'assigned_at'];
            foreach ($check_not_null_str as $col) {
                $this->assertNotNull($dt[$col]);
                $this->assertIsString($dt[$col]);
            }

            $check_nullable_str = ['telegram_user_id', 'notes'];
            foreach ($check_nullable_str as $col) {
                if($dt[$col]){
                    $this->assertNotNull($dt[$col]);
                    $this->assertIsString($dt[$col]);
                }
            }

            $this->assertNotNull($dt["telegram_is_valid"]);
            $this->assertIsInt($dt["telegram_is_valid"]);
            $this->assertContains($dt["telegram_is_valid"], [0, 1]);
        }

        Audit::auditRecordText("Test - Get Vehicle Full Detail By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Vehicle Full Detail By ID", "TC-XXX", 'TC-XXX test_get_vehicle_full_detail_by_id', json_encode($data));
    }

    public function test_get_vehicle_trip_summary_by_id(): void
    {
        // Exec
        $vehicle_id = "3fd091f0-68e9-87e8-0b38-ff129e29e0af";
        $token = $this->login_trait("user");
        $response = $this->httpClient->get("trip/summary/$vehicle_id", [
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

        $check_object_trip = ["most_person_with","vehicle_total_trip_distance","most_origin","most_destination","most_category"];
        foreach ($check_object_trip as $col) {
            $this->assertArrayHasKey($col, $data["data"]);
        }

        $check_not_null_str_trip = ["most_origin","most_destination","most_category"];
        foreach ($check_not_null_str_trip as $col) {
            $this->assertNotNull($data["data"][$col]);
            $this->assertIsString($data["data"][$col]);
        }

        if (!is_null($data["data"]["most_person_with"])) {
            $this->assertIsString($data["data"]["most_person_with"]);
        }

        $this->assertIsFloat($data["data"]["vehicle_total_trip_distance"]);
        $this->assertGreaterThan(0, $data["data"]["vehicle_total_trip_distance"]);

        Audit::auditRecordText("Test - Get Vehicle Trip Summary By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Get Vehicle Trip Summary By ID", "TC-XXX", 'TC-XXX test_get_vehicle_trip_summary_by_id', json_encode($data));
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
                'name' => 'vehicle_image',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ],
            [
                'name' => 'vehicle_other_img_url[]',
                'contents' => fopen($img2->getPathname(), 'r'),
                'filename' => 'image2.jpg',
            ],
            [
                'name' => 'vehicle_other_img_url[]',
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
                'name' => 'vehicle_document[]',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ],
            [
                'name' => 'vehicle_document[]',
                'contents' => fopen($pdf1->getPathname(), 'r'),
                'filename' => 'document1.pdf',
            ],
            [
                'name' => 'vehicle_document_caption[]',
                'contents' => 'this is an image',
            ],
            [
                'name' => 'vehicle_document_caption[]',
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

    public function test_post_update_vehicle_image_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "3fd091f0-68e9-87e8-0b38-ff129e29e0af";
        
        // Create fake image
        $img1 = UploadedFile::fake()->image('image1.jpg');

        $form = [
            [
                'name' => 'vehicle_image',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ]
        ];

        $response = $this->httpClient->post("image/$id", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'multipart' => $form,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("vehicle updated", $data['message']);

        Audit::auditRecordText("Test - Post Update Vehicle Image By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Update Vehicle Image By ID", "TC-XXX", 'TC-XXX test_post_update_vehicle_image_by_id', json_encode($data));
    }

    public function test_post_update_vehicle_image_collection_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "3fd091f0-68e9-87e8-0b38-ff129e29e0af";
        
        // Create fake images
        $img1 = UploadedFile::fake()->image('image1.jpg');
        $img2 = UploadedFile::fake()->image('image2.jpg');

        $form = [
            [
                'name' => 'vehicle_other_img_url[]',
                'contents' => fopen($img1->getPathname(), 'r'),
                'filename' => 'image1.jpg',
            ],
            [
                'name' => 'vehicle_other_img_url[]',
                'contents' => fopen($img2->getPathname(), 'r'),
                'filename' => 'image2.jpg',
            ]
        ];

        $response = $this->httpClient->post("image_collection/$id", [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'multipart' => $form,
        ]);

        $data = json_decode($response->getBody(), true);

        // Test Parameter
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("vehicle updated", $data['message']);

        Audit::auditRecordText("Test - Post Update Vehicle Image Collection By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Post Update Vehicle Image Collection By ID", "TC-XXX", 'TC-XXX test_post_update_vehicle_image_collection_by_id', json_encode($data));
    }

    public function test_put_recover_vehicle_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "9f0484ff-3099-6205-0dae-b3ccbc222a2c";

        $response = $this->httpClient->put("recover/$id", [
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
        $this->assertEquals("vehicle recovered", $data['message']);

        Audit::auditRecordText("Test - Put Recover Vehicle By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Recover Vehicle By ID", "TC-XXX", 'TC-XXX test_put_recover_vehicle_by_id', json_encode($data));
    }

    public function test_put_update_vehicle_detail_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "3fd091f0-68e9-87e8-0b38-ff129e29e0af";

        $body = [
            'vehicle_name' => 'Kijang Innova 2.0 Type G MT',
            'vehicle_merk' => 'Toyota',
            'vehicle_type' => 'Minibus',
            'vehicle_price' => 275000000,
            'vehicle_desc' => 'sudah jarang digunakan 2',
            'vehicle_distance' => 90000,
            'vehicle_category' => 'Parents Car',
            'vehicle_status' => 'Available',
            'vehicle_year_made' => 2011,
            'vehicle_plate_number' => 'PA 1234 ZX',
            'vehicle_fuel_status' => 'Not Monitored',
            'vehicle_fuel_capacity' => 50,
            'vehicle_default_fuel' => 'Pertamina Pertalite',
            'vehicle_color' => 'White',
            'vehicle_transmission' => 'Manual',
            'vehicle_capacity' => 8,
        ];

        $response = $this->httpClient->put("detail/$id", [
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
        $this->assertEquals("vehicle updated", $data['message']);

        Audit::auditRecordText("Test - Put Update Vehicle Detail By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Put Update Vehicle Detail By ID", "TC-XXX", 'TC-XXX test_put_update_vehicle_detail_by_id', json_encode($data));
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

    public function test_hard_delete_vehicle_document_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $vehicle_id = "563e01b3-e06f-01a9-2fae-4d525177fdd1";
        $doc_id = "304d5c4a-b0a7-8c1a-1cac-50efb3413403";

        $response = $this->httpClient->delete("document/destroy/$vehicle_id/$doc_id", [
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
        $this->assertEquals("vehicle document deleted", $data['message']);

        Audit::auditRecordText("Test - Hard Delete Vehicle Document By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Vehicle Document By ID", "TC-XXX", 'TC-XXX test_hard_delete_vehicle_document_by_id', json_encode($data));
    }

    public function test_soft_delete_vehicle_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "563e01b3-e06f-01a9-2fae-4d525177fdd1";

        $response = $this->httpClient->delete("delete/$id", [
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
        $this->assertEquals("vehicle deleted", $data['message']);

        Audit::auditRecordText("Test - Soft Delete Vehicle By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Soft Delete Vehicle By ID", "TC-XXX", 'TC-XXX test_soft_delete_vehicle_by_id', json_encode($data));
    }

    public function test_hard_delete_vehicle_by_id(): void
    {
        // Exec
        $token = $this->login_trait("user");
        $id = "563e01b3-e06f-01a9-2fae-4d525177fdd1";

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
        $this->assertEquals("vehicle permentally deleted", $data['message']);

        Audit::auditRecordText("Test - Hard Delete Vehicle By ID", "TC-XXX", "Result : ".json_encode($data));
        Audit::auditRecordSheet("Test - Hard Delete Vehicle By ID", "TC-XXX", 'TC-XXX test_hard_delete_vehicle_by_id', json_encode($data));
    }
}
