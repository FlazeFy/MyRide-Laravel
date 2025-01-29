<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VehicleType implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        $type = ['City Car','Minibus','Motorcycle','Hatchback','Sedan','SUV','Pickup Truck','Convertible','Coupe','Van','Wagon','Crossover','Electric'];

        foreach ($type as $format) {
            if ($format === $value) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'Vehicle Type is not available';
    }
}