<?php

namespace Tests\Unit;

use App\Helpers\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /** @test */
    public function it_converts_price_to_k()
    {
        // Test Data: Price values
        $price1 = 1000;
        $price2 = 1500;
        $price3 = 999;

        // Validate price conversion
        $this->assertEquals('1K', Converter::convert_price_k($price1));
        $this->assertEquals('1.5K', Converter::convert_price_k($price2));
        $this->assertEquals(999, Converter::convert_price_k($price3));
    }

    /** @test */
    public function it_calculates_distance_in_km()
    {
        // Test Data: Jakarta (Telkom Landmark Tower) -> Bandung (Telkom University)
        $distance = Converter::calculate_distance(
            -6.230407062870788, 106.81830169695517, -6.97351194914099, 107.6305203850431
        );

        // Validate returned value is numeric
        $this->assertIsNumeric($distance);

        // Validate calculated distance
        $this->assertEquals(121.96, $distance);
    }

    /** @test */
    public function it_returns_zero_distance_for_same_coordinates()
    {
        // Test Data: Same latitude and longitude
        $distance = Converter::calculate_distance(
            -6.97351194914099, 107.6305203850431, -6.97351194914099, 107.6305203850431
        );

        // Validate distance is zero
        $this->assertEquals('0.00', $distance);
    }
}