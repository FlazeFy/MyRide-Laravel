<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

// Model
use App\Models\DictionaryModel;

class VehicleFuelStatusRule implements Rule
{
    public function passes($attribute, $value)
    {
        $types = DictionaryModel::getDictionaryByType('vehicle_fuel_status')->pluck('dictionary_name')->toArray();

        return in_array($value, $types, true);
    }

    public function message()
    {
        return 'Vehicle Fuel Status is not available';
    }
}