<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ServiceExport implements FromCollection, WithHeadings, WithTitle
{
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }
    public function collection()
    {
        return $this->service;
    }
    public function headings(): array
    {
        return ["vehicle_name","vehicle_plate_number", "vehicle_type", 'service_category', 'service_price_total', 'service_location', 'service_note', 'remind_at', 'created_at', 'updated_at'];
    }
    public function title(): string
    {
        return "Service";
    }
}
