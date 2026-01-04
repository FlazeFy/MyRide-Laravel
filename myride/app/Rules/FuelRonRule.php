<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

// Model
use App\Models\DictionaryModel;

class FuelRonRule implements Rule
{
    public function passes($attribute, $value)
    {
        $types = DictionaryModel::getDictionaryByType('fuel_ron')->pluck('dictionary_name')->toArray();

        return in_array($value, $types, true);
    }

    public function message()
    {
        return 'Fuel Ron is not available';
    }
}