<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class DataExport implements FromCollection
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }
}
