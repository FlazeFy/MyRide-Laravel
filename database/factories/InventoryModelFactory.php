<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;
use App\Models\VehicleModel;

class InventoryModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);

        return [
            'id' => Generator::getUUID(), 
            'gudangku_inventory_id' => null, 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'inventory_name' => fake()->words(mt_rand(2,3), true), 
            'inventory_category' => DictionaryModel::getRandom(0,'inventory_category'), 
            'inventory_qty' => mt_rand(1,4), 
            'inventory_storage' => DictionaryModel::getRandom(0,'inventory_storage'), 
            'inventory_image_url' => null, 
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id, 
            'updated_at' => Generator::getRandomDate($ran),
        ];
    }
}
