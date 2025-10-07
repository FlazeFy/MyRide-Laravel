<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DriverExport implements FromCollection, WithHeadings, WithTitle
{
    private $driver;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }
    public function collection()
    {
        return $this->driver;
    }
    public function headings(): array
    {
        return ['username', 'fullname', 'email', 'telegram_user_id', 'telegram_is_valid', 'phone', 'notes', 'created_at', 'updated_at'];
    }
    public function title(): string
    {
        return "Driver";
    }
}
