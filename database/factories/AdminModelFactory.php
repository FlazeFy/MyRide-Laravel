<?php

namespace Database\Factories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

// Model
use App\Models\AdminModel;
// Helper
use App\Helpers\Generator;

class AdminModelFactory extends Factory
{
    protected $model = AdminModel::class;

    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $ran2 = mt_rand(0, 1);
        
        return [
            'id' => Generator::getUUID(), 
            'username' => fake()->username(), 
            'password' => Hash::make('nopass123'),
            'email' => fake()->unique()->safeEmail(), 
            'telegram_user_id' => null,
            'telegram_is_valid' => 0,
            'created_at' => Generator::getRandomDate(0), 
            'updated_at' => Generator::getRandomDate($ran)
        ];
    }

    public function apiPayload(): static
    {
        return $this->state(fn () => [
            'password' => 'nopass123',
            'telegram_user_id' => env('TELEGRAM_USER_ID'),
        ]);
    }
}
