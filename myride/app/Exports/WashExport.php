<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class WashExport implements FromCollection, WithHeadings, WithTitle
{
    private $wash;

    public function __construct($wash)
    {
        $this->wash = $wash;
    }
    public function collection()
    {
        return $this->wash;
    }
    public function headings(): array
    {
        return ['vehicle_name', 'wash_desc', 'wash_by', 'is_wash_body', 'is_wash_window', 'is_wash_dashboard', 'is_wash_tires', 'is_wash_trash', 'is_wash_engine', 'is_wash_seat', 'is_wash_carpet', 'is_wash_pillows', 'wash_address', 'wash_start_time', 'wash_end_time', 'is_fill_window_washing_water', 'is_wash_hollow', 'datetime'];
    }
    public function title(): string
    {
        return "Wash History";
    }
}
