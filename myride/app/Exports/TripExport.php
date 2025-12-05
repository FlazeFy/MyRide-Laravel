<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TripExport implements FromCollection, WithHeadings, WithTitle
{
    private $trip;

    public function __construct($trip)
    {
        $this->trip = $trip;
    }
    public function collection()
    {
        return $this->trip;
    }
    public function headings(): array
    {
        return ['vehicle_name','vehicle_type','vehicle_plate_number','driver_name','trip_desc','trip_category','trip_person','trip_origin_name','trip_origin_coordinate','trip_destination_name','trip_destination_coordinate', 'created_at','updated_at'];
    }
    public function title(): string
    {
        return "Trip History";
    }
}
