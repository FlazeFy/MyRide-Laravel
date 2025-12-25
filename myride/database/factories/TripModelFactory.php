<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;
use App\Models\VehicleModel;

class TripModelFactory extends Factory
{
    public function getRandomLocationFromCsv()
    {
        $path = public_path('sample_locations_jkt.csv');
        $locations = [];

        if (($handle = fopen($path, 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); 

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $locations[] = (object)[
                    'location_name' => $row[0],
                    'location_coordinate' => $row[1]
                ];
            }

            fclose($handle);
        }

        return $locations ? $locations[array_rand($locations)] : null;
    }

    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $ran2 = mt_rand(0, 1);
        $ranDesc = mt_rand(3, 3);
        $user_id = UserModel::getRandomWithVehicle(0);
        $location_origin = $this->getRandomLocationFromCsv();
        $location_destination = $this->getRandomLocationFromCsv();

        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'trip_desc' => fake()->sentence($ranDesc), 
            'trip_category' => DictionaryModel::getRandom(0,'trip_category'), 
            'trip_person' => fake()->name(), 
            'trip_origin_name' => $location_origin->location_name, 
            'trip_origin_coordinate' => $location_origin->location_coordinate,  
            'trip_destination_name' => $location_destination->location_name, 
            'trip_destination_coordinate' => $location_destination->location_coordinate, 
            'created_by' => $user_id, 
            'created_at' => Generator::getRandomDate(0), 
            'updated_at' => Generator::getRandomDate($ran),
            'deleted_at' => Generator::getRandomDate($ran2)
        ];
    }
}
