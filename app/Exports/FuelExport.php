<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class FuelExport implements FromCollection, WithHeadings, WithTitle
{
    private $fuel;

    public function __construct($fuel)
    {
        $this->fuel = $fuel;
    }
    public function collection()
    {
        return $this->fuel;
    }
    public function headings(): array
    {
        return ["vehicle_name","vehicle_plate_number", "vehicle_type", "fuel_volume", "fuel_price_total", "fuel_brand", "fuel_type", "fuel_ron", "datetime"];
    }
    public function title(): string
    {
        return "Fuel History";
    }
}
