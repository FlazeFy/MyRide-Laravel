<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CleanExport implements FromCollection, WithHeadings, WithTitle
{
    private $clean;

    public function __construct($clean)
    {
        $this->clean = $clean;
    }
    public function collection()
    {
        return $this->clean;
    }
    public function headings(): array
    {
        return ['vehicle_name', 'clean_desc', 'clean_by', 'clean_tools', 'is_clean_body', 'is_clean_window', 'is_clean_dashboard', 'is_clean_tires', 'is_clean_trash', 'is_clean_engine', 'is_clean_seat', 'is_clean_carpet', 'is_clean_pillows', 'clean_address', 'clean_start_time', 'clean_end_time', 'is_fill_window_cleaning_water', 'is_fill_fuel', 'is_clean_hollow', 'datetime'];
    }
    public function title(): string
    {
        return "Clean History";
    }
}
