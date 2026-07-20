<?php

namespace Tests\Unit;

use App\Helpers\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /** @test */
    public function it_converts_price_to_k()
    {
        $this->assertEquals('1K', Converter::convert_price_k(1000));
        $this->assertEquals('1.5K', Converter::convert_price_k(1500));
        $this->assertEquals(999, Converter::convert_price_k(999));
    }

    /** @test */
    public function it_calculates_distance_in_km()
    {
        // Jakarta (Telkom Landmark Tower) -> Bandung (Telkom University)
        $distance = Converter::calculate_distance(-6.230407062870788, 106.81830169695517, -6.97351194914099, 107.6305203850431);

        $this->assertIsNumeric($distance);
        $this->assertEquals(121.96, $distance); // Actual distance
    }

    /** @test */
    public function distance_between_same_coordinates_is_zero()
    {
        $distance = Converter::calculate_distance(-6.97351194914099, 107.6305203850431, -6.97351194914099, 107.6305203850431);

        $this->assertEquals('0.00', $distance);
    }
}