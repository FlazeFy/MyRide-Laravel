<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;
use App\Models\VehicleModel;

class CleanModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);

        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'clean_desc' => fake()->paragraph(), 
            'clean_by' => fake()->company(), 
            'clean_tools' => fake()->paragraph(), 
            'is_clean_body' => 1, 
            'is_clean_window' => 1, 
            'is_clean_dashboard' => mt_rand(0, 1), 
            'is_clean_tires' => mt_rand(0, 1), 
            'is_clean_trash' => mt_rand(0, 1), 
            'is_clean_engine' => mt_rand(0, 1),
            'is_clean_seat' => mt_rand(0, 1), 
            'is_clean_carpet' => mt_rand(0, 1), 
            'is_clean_pillows' => mt_rand(0, 1), 
            'clean_address' => fake()->address(), 
            'clean_start_time' => Generator::getRandomDate(0), 
            'clean_end_time' => Generator::getRandomDate($ran),
            'is_fill_window_cleaning_water' => mt_rand(0, 1), 
            'is_clean_hollow' => mt_rand(0, 1),  
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id, 
            'updated_at' => Generator::getRandomDate($ran),
        ];
    }
}
