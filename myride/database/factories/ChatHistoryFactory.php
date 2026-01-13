<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;

class ChatHistoryModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);

        return [
            'id' => Generator::getUUID(), 
            'question' => fake()->words(mt_rand(5,9), true), 
            'answer' => fake()->words(mt_rand(10,30), true), 
            'intent' => DictionaryModel::getRandom(0,'chat_intent'),
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id
        ];
    }
}
