<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

// Model
use App\Models\DictionaryModel;

class VehicleTransmissionRule implements Rule
{
    public function passes($attribute, $value)
    {
        $types = DictionaryModel::getDictionaryByType('vehicle_transmission')->pluck('dictionary_name')->toArray();

        return in_array($value, $types, true);
    }

    public function message()
    {
        return 'Vehicle Transmission is not available';
    }
}