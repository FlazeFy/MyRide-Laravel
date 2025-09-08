<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

// Helper
use App\Helpers\Generator;
// Model
use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\DictionaryModel;

class FuelModelFactory extends Factory
{
    public function definition(): array
    {
        $ran = mt_rand(0, 1);
        $fuel_vol = mt_rand(5, 30);
        $fuel_brand = DictionaryModel::getRandom(0,'fuel_brand');
        $user_id = UserModel::getRandomWithVehicle(0);
        $fuel_type = null;
        $fuel_ron = null;
        $fuel_price = null;

        if ($fuel_brand == "Pertamina") {
            $fuel_type_list = ["Pertamax", "Pertalite", "Pertamax Turbo"];
            $fuel_type = $fuel_type_list[mt_rand(0, count($fuel_type_list) - 1)];
            switch ($fuel_type) {
                case "Pertamax":
                    $fuel_ron = 92;
                    $fuel_price = $fuel_vol * 14000;
                    break;
                case "Pertalite":
                    $fuel_ron = 90;
                    $fuel_price = $fuel_vol * 10000;
                    break;
                case "Pertamax Turbo":
                    $fuel_ron = 98;
                    $fuel_price = $fuel_vol * 16000;
                    break;
            }
        } else if ($fuel_brand == "BP") {
            $fuel_type_list = ["BP 90", "BP 92", "BP 95"];
            $fuel_type = $fuel_type_list[mt_rand(0, count($fuel_type_list) - 1)];
            switch ($fuel_type) {
                case "BP 90":
                    $fuel_ron = 90;
                    $fuel_price = $fuel_vol * 10500;
                    break;
                case "BP 92":
                    $fuel_ron = 92;
                    $fuel_price = $fuel_vol * 13500;
                    break;
                case "BP 95":
                    $fuel_ron = 95;
                    $fuel_price = $fuel_vol * 15500;
                    break;
            }
        } else if ($fuel_brand == "Shell") {
            $fuel_type_list = ["Shell Super", "Shell V-Power", "Shell V-Power Nitro+"];
            $fuel_type = $fuel_type_list[mt_rand(0, count($fuel_type_list) - 1)];
            switch ($fuel_type) {
                case "Shell Super":
                    $fuel_ron = 92;
                    $fuel_price = $fuel_vol * 13500;
                    break;
                case "Shell V-Power":
                    $fuel_ron = 95;
                    $fuel_price = $fuel_vol * 15000;
                    break;
                case "Shell V-Power Nitro+":
                    $fuel_ron = 98;
                    $fuel_price = $fuel_vol * 16500;
                    break;
            }
        } else if ($fuel_brand == "Vivo") {
            $fuel_type_list = ["Vivo Revvo 90", "Vivo Revvo 92", "Vivo Revvo 95"];
            $fuel_type = $fuel_type_list[mt_rand(0, count($fuel_type_list) - 1)];
            switch ($fuel_type) {
                case "Vivo Revvo 90":
                    $fuel_ron = 90;
                    $fuel_price = $fuel_vol * 10200;
                    break;
                case "Vivo Revvo 92":
                    $fuel_ron = 92;
                    $fuel_price = $fuel_vol * 13200;
                    break;
                case "Vivo Revvo 95":
                    $fuel_ron = 95;
                    $fuel_price = $fuel_vol * 15200;
                    break;
            }
        }


        return [
            'id' => Generator::getUUID(), 
            'vehicle_id' => VehicleModel::getRandom(0,$user_id), 
            'fuel_volume' => $fuel_vol, 
            'fuel_price_total' => $fuel_price, 
            'fuel_brand' => $fuel_brand, 
            'fuel_type' => $fuel_type, 
            'fuel_ron' => $fuel_ron, 
            'fuel_bill' => null,
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => $user_id, 
        ];
    }
}
