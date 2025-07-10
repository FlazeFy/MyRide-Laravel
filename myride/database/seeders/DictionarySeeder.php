<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

# Model
use App\Models\DictionaryModel;
# Helper
use App\Helpers\Generator;

class DictionarySeeder extends Seeder
{
    public function run(): void
    {
        $dictionaries = [
            'vehicle_fuel_status' => ['Normal','Full','High','Low','Empty','Not Monitored'],
            'vehicle_status' => ['Available','Under Maintenance','Damaged','Reserved'],
            'vehicle_transmission' => ['CVT','Manual','Automatic'],
            'vehicle_type' => [
                'City Car','Minibus','Motorcycle','Hatchback','Sedan','SUV','Pickup Truck',
                'Convertible','Coupe','Van','Wagon','Crossover','Electric'
            ],
            'vehicle_category' => ['Operational', 'Parents Car', 'Rental', 'Project'],
            'vehicle_default_fuel' => ['Pertamina Pertalite', 'Pertamina Pertamax', 'Solar', 'Dexlite', 'Shell Super', 'Shell V-Power', 'Shell V-Power Diesel', 'Shell V-Power Nitro+'],
            'trip_category' => ['Routine Check', 'Office Trip', 'Project Visit', 'Logistics']
        ];
        $now = Carbon::now();

        foreach ($dictionaries as $type => $dt) {
            foreach ($dt as $name) {
                DictionaryModel::create([
                    'id' => Generator::getUUID(), 
                    'dictionary_type' => $type,
                    'dictionary_name' => $name,
                    'created_at' => $now,
                    'created_by' => null
                ]);
            }
        }
    }
}
