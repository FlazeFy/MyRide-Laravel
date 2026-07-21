<?php

namespace Database\Factories;

use App\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

// Helper
use App\Helpers\Generator;

class UserModelFactory extends Factory
{
    protected $model = UserModel::class;

    public function definition(): array
    {
        $ran = mt_rand(0, 1);

        return [
            'id' => Generator::getUUID(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('nopass123'),
            'email' => fake()->unique()->freeEmail(),
            'telegram_user_id' => null,
            'telegram_is_valid' => 0,
            'created_at' => Generator::getRandomDate(0),
            'updated_at' => Generator::getRandomDate($ran),
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