<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;
use App\Models\VehicleModel;

class WashModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);
        $wash_by = DictionaryModel::getRandom(0,'wash_by');

        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'wash_desc' => fake()->paragraph(), 
            'wash_by' => $wash_by, 
            'is_wash_body' => 1, 
            'is_wash_window' => 1, 
            'is_wash_dashboard' => mt_rand(0, 1), 
            'is_wash_tires' => mt_rand(0, 1), 
            'is_wash_trash' => mt_rand(0, 1), 
            'is_wash_engine' => mt_rand(0, 1),
            'is_wash_seat' => mt_rand(0, 1), 
            'is_wash_carpet' => mt_rand(0, 1), 
            'is_wash_pillows' => mt_rand(0, 1), 
            'wash_address' => $wash_by !== "Myself" ? fake()->address() : null, 
            'wash_price' => $ran === 1 ? mt_rand(40000, 450000) : null, 
            'wash_start_time' => Generator::getRandomDate(0), 
            'wash_end_time' => Generator::getRandomDate($ran),
            'is_fill_window_washing_water' => mt_rand(0, 1), 
            'is_wash_hollow' => mt_rand(0, 1),  
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id, 
            'updated_at' => Generator::getRandomDate($ran),
        ];
    }
}
