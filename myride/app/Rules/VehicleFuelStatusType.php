<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VehicleFuelStatusType implements Rule
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
        $type = ['Normal','Full','High','Low','Empty','Not Monitored'];

        foreach ($type as $format) {
            if ($format === $value) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'Vehicle Fuel Status Type is not available';
    }
}