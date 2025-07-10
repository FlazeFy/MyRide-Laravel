<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;

class VehicleModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $ran2 = mt_rand(0, 1);
        $ranName = mt_rand(2, 3);

        return [
            'id' => Generator::getUUID(), 
            'vehicle_name' => fake()->sentence($ranName), 
            'vehicle_merk' => fake()->company(), 
            'vehicle_type' => DictionaryModel::getRandom(0,'vehicle_type'), 
            'vehicle_price' => (string)mt_rand(150, 10000) * 1000000, 
            'vehicle_desc' => fake()->paragraph(), 
            'vehicle_distance' => mt_rand(1, 200) * 1000, 
            'vehicle_category' => DictionaryModel::getRandom(0,'vehicle_category'), 
            'vehicle_status' => DictionaryModel::getRandom(0,'vehicle_status'), 
            'vehicle_year_made' => mt_rand(2010, 2024), 
            'vehicle_plate_number' => Generator::getPlateNumber(), 
            'vehicle_fuel_status' => DictionaryModel::getRandom(0, 'vehicle_fuel_status'), 
            'vehicle_fuel_capacity' => mt_rand(30, 80), 
            'vehicle_default_fuel' => DictionaryModel::getRandom(0,'vehicle_default_fuel'),
            'vehicle_color' => fake()->colorName(), 
            'vehicle_transmission' => DictionaryModel::getRandom(0,'vehicle_transmission'), 
            'vehicle_img_url' => null, 
            'vehicle_other_img_url' => null, 
            'vehicle_capacity' => mt_rand(2,8),  
            'vehicle_document' => null, 
            'created_by' => UserModel::getRandom(0), 
            'created_at' => Generator::getRandomDate(0), 
            'updated_at' => Generator::getRandomDate($ran),
            'deleted_at' => Generator::getRandomDate($ran2)
        ];
    }
}
