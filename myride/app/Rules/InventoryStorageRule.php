<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

// Model
use App\Models\DictionaryModel;

class InventoryStorageRule implements Rule
{
    public function passes($attribute, $value)
    {
        $types = DictionaryModel::getDictionaryByType('inventory_storage')->pluck('dictionary_name')->toArray();

        return in_array($value, $types, true);
    }

    public function message()
    {
        return 'Inventory Storage is not available';
    }
}