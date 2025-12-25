<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DriverModel;
use App\Models\VehicleModel;

class DriverVehicleRelationModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $ran2 = mt_rand(0, 1);
        $ranDesc = mt_rand(0, 4);
        $user_id = UserModel::getRandomWithVehicleDriver(0);

        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'driver_id' => DriverModel::getRandom(0,$user_id), 
            'relation_note' => $ranDesc === 0 ? null : fake()->sentence($ranDesc), 
            'created_by' => $user_id, 
            'created_at' => Generator::getRandomDate(0), 
        ];
    }
}
