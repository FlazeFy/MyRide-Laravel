<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

use App\Models\DictionaryModel;
use App\Helpers\Generator;

use Illuminate\Support\Facades\DB;

class DictionarySeeder extends Seeder
{
    public function run(): void
    {
        // Delete All 
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DictionaryModel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
            'trip_category' => [
                'Culinary Hunting','Business Trip','Family Vacation','Worship','Refreshing','Strolling Around','City Exploration','Nature Retreat',
                'Cultural Festival','Road Trip','Backpacking','Photography','Shopping','Sport Event',
            ],
            'fuel_ron' => ['90','92','95','98'],
            'fuel_brand' => ['Pertamina','Vivo','BP','Shell','Electric'],
            'fuel_type_Pertamina' => ['Pertalite', 'Pertamax', 'Pertamax Turbo', 'Dexlite', 'Pertamina Dex'],
            'fuel_type_Vivo' => ['Revvo 89', 'Revvo 92', 'Revvo 95'],
            'fuel_type_BP' => ['BP 90', 'BP 92', 'BP Ultimate'],
            'fuel_type_Shell' => ['Shell Super', 'Shell V-Power', 'Shell Diesel'],
            'inventory_category' => ['Safety', 'Maintenance', 'Personal', 'Electronics', 'Documents'],
            'inventory_storage' => ['Glove Compartment', 'Trunk', 'Dashboard', 'Back Seat Pocket', 'Roof Box'],
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
