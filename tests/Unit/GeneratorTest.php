<?php

namespace Tests\Unit;

use App\Helpers\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    /** @test */
    public function getUuidGeneratesAValidUuid() {
        // Test Data: Generate UUID
        $uuid = Generator::getUUID();

        // Validate UUID format
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/',
            $uuid
        );
    }

    /** @test */
    public function getUuidGeneratesUniqueUuids() {
        // Test Data: Generate two UUIDs
        $uuid1 = Generator::getUUID();
        $uuid2 = Generator::getUUID();

        // Validate both UUIDs are unique
        $this->assertNotEquals($uuid1, $uuid2);
    }

    /** @test */
    public function getTokenValidationGeneratesValidationTokenWithRequestedLength() {
        // Test Data: Generate validation token with length of 8
        $token = Generator::getTokenValidation(8);

        // Validate token length
        $this->assertEquals(8, strlen($token));
    }

    /** @test */
    public function getTokenValidationGeneratesValidationTokenUsingOnlyValidCharacters() {
        // Test Data: Generate validation token
        $token = Generator::getTokenValidation(20);

        // Validate token contains only uppercase letters and numbers
        $this->assertMatchesRegularExpression('/^[0-9A-Z]+$/', $token);
    }

    /** @test */
    public function getTokenValidationGeneratesEmptyTokenWhenLengthIsZero() {
        // Test Data: Generate validation token with zero length
        $token = Generator::getTokenValidation(0);

        // Validate token is empty
        $this->assertEquals('', $token);
    }

    /** @test */
    public function getRandomDateReturnsRandomDateWhenNullIsZero() {
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
    public function getRandomDateReturnsNullWhenNullParameterIsNotZero() {
        // Test Data: Generate random date with non-zero parameter
        $date = Generator::getRandomDate(1);

        // Validate returned value is null
        $this->assertNull($date);
    }
    
    /** @test */
    public function generateMonthNameReturnsExpectedMonthNameBasedOnType() {
        // Test Data: Month index with long and short type
        $longMonth = Generator::generateMonthName(1, 'long');
        $shortMonth = Generator::generateMonthName(1, 'short');

        // Validate generated month names
        $this->assertEquals('January', $longMonth);
        $this->assertEquals('Jan', $shortMonth);
    }

    /** @test */
    public function getMessageTemplateReturnsExpectedMessageBasedOnType() {
        // Test Data: Various message types
        $create = Generator::getMessageTemplate('create', 'User');
        $update = Generator::getMessageTemplate('update', 'User');
        $delete = Generator::getMessageTemplate('delete', 'User');
        $permanentlyDelete = Generator::getMessageTemplate('permanently delete', 'User');
        $fetch = Generator::getMessageTemplate('fetch', 'User');
        $recover = Generator::getMessageTemplate('recover', 'User');
        $analyze = Generator::getMessageTemplate('analyze', 'User');
        $generate = Generator::getMessageTemplate('generate', 'User');
        $notFound = Generator::getMessageTemplate('not_found', 'User');
        $unknownError = Generator::getMessageTemplate('unknown_error', '');
        $conflict = Generator::getMessageTemplate('conflict', 'Email');
        $custom = Generator::getMessageTemplate('custom', 'Custom message');
        $validationFailed = Generator::getMessageTemplate('validation_failed', 'email is required');
        $permission = Generator::getMessageTemplate('permission', 'Admin');
        $default = Generator::getMessageTemplate('invalid', '');

        // Validate generated messages
        $this->assertEquals('User created', $create);
        $this->assertEquals('User updated', $update);
        $this->assertEquals('User deleted', $delete);
        $this->assertEquals('User permanently deleted', $permanentlyDelete);
        $this->assertEquals('User fetched', $fetch);
        $this->assertEquals('User recovered', $recover);
        $this->assertEquals('User analyzed', $analyze);
        $this->assertEquals('User generated', $generate);
        $this->assertEquals('User not found', $notFound);
        $this->assertEquals('something wrong. please contact admin', $unknownError);
        $this->assertEquals('Email has been used. try another', $conflict);
        $this->assertEquals('Custom message', $custom);
        $this->assertEquals('validation failed : email is required', $validationFailed);
        $this->assertEquals('permission denied. only Admin can use this feature', $permission);
        $this->assertEquals('failed to get respond message', $default);
    }

    /** @test */
    public function generateDocTemplateReturnsExpectedTemplateBasedOnType() {
        // Test Data: Generate document templates
        $footer = Generator::generateDocTemplate('footer');
        $header = Generator::generateDocTemplate('header');
        $style = Generator::generateDocTemplate('style');

        // Validate footer template
        $this->assertStringContainsString('Parts of FlazenApps', $footer);
        $this->assertStringContainsString('Generated at', $footer);

        // Validate header template
        $this->assertStringContainsString('MyRide', $header);
        $this->assertStringContainsString('Management Apps for your vehicle', $header);

        // Validate style template
        $this->assertStringContainsString('<style>', $style);
        $this->assertStringContainsString('font-family: Helvetica', $style);
    }

    /** @test */
    public function getPlateNumberGeneratesValidPlateNumber() {
        // Test Data: Generate plate number
        $plateNumber = Generator::getPlateNumber();

        // Validate plate number format
        $this->assertMatchesRegularExpression('/^[A-Z]{1,2} [1-9][0-9]{1,3} [A-Z]{2,3}$/', $plateNumber);
    }
}