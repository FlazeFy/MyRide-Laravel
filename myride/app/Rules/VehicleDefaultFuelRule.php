<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

// Model
use App\Models\DictionaryModel;

class VehicleDefaultFuelRule implements Rule
{
    public function passes($attribute, $value)
    {
        $types = DictionaryModel::getDictionaryByType('vehicle_default_fuel')->pluck('dictionary_name')->toArray();

        return in_array($value, $types, true);
    }

    public function message()
    {
        return 'Vehicle Default Fuel is not available';
    }
}