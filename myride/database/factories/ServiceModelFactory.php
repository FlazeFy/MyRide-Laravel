<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;
use App\Models\VehicleModel;

class ServiceModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);

        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'service_category' => DictionaryModel::getRandom(0,'service_category'), 
            'service_price_total' => mt_rand(100000, 5000000), 
            'service_location' => fake()->words(mt_rand(2,3), true), 
            'service_note' => fake()->paragraph(), 
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id, 
            'updated_at' => Generator::getRandomDate($ran), 
            'remind_at' => Generator::getRandomDate($ran), 
        ];
    }
}
