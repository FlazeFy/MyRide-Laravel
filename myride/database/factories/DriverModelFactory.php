<?php

namespace Database\Factories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DriverModel;
use App\Models\VehicleModel;

class DriverModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);
        $password = 'nopass123';

        return [
            'id' => Generator::getUUID(),
            'username' => fake()->username(), 
            'password' => Hash::make($password), 
            'email' => fake()->unique()->freeEmail(), 
            'fullname' => fake()->name(), 
            'telegram_user_id' => null,
            'telegram_is_valid' => 0,
            'phone' => fake()->phoneNumber(), 
            'notes' => fake()->paragraph(), 
            'created_by' => $user_id, 
            'created_at' => Generator::getRandomDate(0), 
            'updated_at' => Generator::getRandomDate($ran),
        ];
    }
}
