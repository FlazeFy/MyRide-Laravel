<?php

namespace Tests\Unit;

use App\Helpers\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    /** @test */
    public function it_generates_a_valid_uuid()
    {
        // Test Data: Generate UUID
        $uuid = Generator::getUUID();

        // Validate UUID format
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/',
            $uuid
        );
    }

    /** @test */
    public function it_generates_unique_uuids()
    {
        // Test Data: Generate two UUIDs
        $uuid1 = Generator::getUUID();
        $uuid2 = Generator::getUUID();

        // Validate both UUIDs are unique
        $this->assertNotEquals($uuid1, $uuid2);
    }

    /** @test */
    public function it_generates_validation_token_with_requested_length()
    {
        // Test Data: Generate validation token with length of 8
        $token = Generator::getTokenValidation(8);

        // Validate token length
        $this->assertEquals(8, strlen($token));
    }

    /** @test */
    public function it_generates_validation_token_using_only_valid_characters()
    {
        // Test Data: Generate validation token
        $token = Generator::getTokenValidation(20);

        // Validate token contains only uppercase letters and numbers
        $this->assertMatchesRegularExpression('/^[0-9A-Z]+$/', $token);
    }

    /** @test */
    public function it_generates_empty_token_when_length_is_zero()
    {
        // Test Data: Generate validation token with zero length
        $token = Generator::getTokenValidation(0);

        // Validate token is empty
        $this->assertEquals('', $token);
    }

    /** @test */
    public function it_returns_random_date_when_null_is_zero()
    {
        // Test Data: Generate random date
        $date = Generator::getRandomDate(0);

        // Validate date is not null
        $this->assertNotNull($date);

        // Validate date format
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $date);

        // Validate date is after 2023-01-01
        $this->assertGreaterThanOrEqual(strtotime('2023-01-01 00:00:00'), strtotime($date));

        // Validate date is not in the future
        $this->assertLessThanOrEqual(time(), strtotime($date));
    }

    /** @test */
    public function it_returns_null_when_null_parameter_is_not_zero()
    {
        // Test Data: Generate random date with non-zero parameter
        $date = Generator::getRandomDate(1);

        // Validate returned value is null
        $this->assertNull($date);
    }
}