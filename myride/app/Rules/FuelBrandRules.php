<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FuelBrandRules implements Rule
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
        $type = ['Pertamina','Vivo','BP','Shell','Electric'];

        foreach ($type as $format) {
            if ($format === $value) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'Fuel Brand is not available';
    }
}