<?php

namespace Tests\Unit;

use App\Helpers\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /** @test */
    public function convertPriceKConvertsPriceToK()
    {
        // Test Data: Price values
        $price1 = 1000;
        $price2 = 1500;
        $price3 = 999;

        // Validate price conversion
        $this->assertEquals('1K', Converter::convertPriceK($price1));
        $this->assertEquals('1.5K', Converter::convertPriceK($price2));
        $this->assertEquals(999, Converter::convertPriceK($price3));
    }

    /** @test */
    public function calculateDistanceCalculatesDistanceInKm()
    {
        // Test Data: Jakarta (Telkom Landmark Tower) -> Bandung (Telkom University)
        $distance = Converter::calculateDistance(
            -6.230407062870788, 106.81830169695517, -6.97351194914099, 107.6305203850431
        );

        // Validate returned value is numeric
        $this->assertIsNumeric($distance);

        // Validate calculated distance
        $this->assertEquals(121.96, $distance);
    }

    /** @test */
    public function calculateDistanceReturnsZeroDistanceForSameCoordinates() {
        // Test Data: Same latitude and longitude
        $distance = Converter::calculateDistance(
            -6.97351194914099, 107.6305203850431, -6.97351194914099, 107.6305203850431
        );

        // Validate distance is zero
        $this->assertEquals('0.00', $distance);
    }
}