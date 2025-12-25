<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\DictionaryModel;
use App\Models\VehicleModel;

class ReminderModelFactory extends Factory
{
    public function getRandomLocationFromCsv()
    {
        $path = public_path('sample_locations_jkt.csv');
        $locations = [];

        if (($handle = fopen($path, 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); 

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $locations[] = $row[1];
            }

            fclose($handle);
        }

        return $locations ? $locations[array_rand($locations)] : null;
    }
    
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $user_id = UserModel::getRandomWithVehicle(0);

        $ranCoor = mt_rand(0, 1);

        $reminder_location = null;
        if($ranCoor === 1){
            $reminder_location = (object)[
                'attachment_type' => 'location',
                'attachment_value' => $this->getRandomLocationFromCsv(),
            ];
        }

        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'reminder_title' => fake()->sentence(mt_rand(2, 6)), 
            'reminder_context' => DictionaryModel::getRandom(0,'reminder_context'), 
            'reminder_body' => fake()->sentence(mt_rand(4, 8)), 
            'reminder_attachment' => $reminder_location ? [$reminder_location] : null, 
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id, 
            'remind_at' => Generator::getRandomDate(0), 
        ];
    }
}
