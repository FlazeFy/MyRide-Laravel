<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

class DictionaryTypeRule implements Rule
{
    public function passes($attribute, $value)
    {
        $type = ['trip_category','vehicle_type','vehicle_category','vehicle_status','vehicle_default_fuel','vehicle_fuel_status','vehicle_transmission'];

        foreach ($type as $format) {
            if ($format === $value) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'Dictionary Type is not available';
    }
}