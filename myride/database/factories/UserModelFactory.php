<?php

namespace Database\Factories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;

class UserModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $ran2 = mt_rand(0, 1);
        $password = 'nopass123';

        return [
            'id' => Generator::getUUID(), 
            'username' => fake()->username(), 
            'password' => Hash::make($password), 
            'email' => fake()->unique()->freeEmail(), 
            'telegram_user_id' => null,
            'telegram_is_valid' => 0,
            'created_at' => Generator::getRandomDate(0), 
            'updated_at' => Generator::getRandomDate($ran)
        ];
    }
}
