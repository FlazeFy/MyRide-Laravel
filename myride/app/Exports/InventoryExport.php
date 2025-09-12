<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InventoryExport implements FromCollection, WithHeadings, WithTitle
{
    private $inventory;

    public function __construct($inventory)
    {
        $this->inventory = $inventory;
    }
    public function collection()
    {
        return $this->inventory;
    }
    public function headings(): array
    {
        return ["vehicle_name","vehicle_plate_number", "vehicle_type", 'inventory_name', 'inventory_category', 'inventory_qty', 'inventory_storage', 'created_at', 'updated_at'];
    }
    public function title(): string
    {
        return "Inventory";
    }
}
