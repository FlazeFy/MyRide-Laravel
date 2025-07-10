<?php

namespace Database\Seeders;

use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\FAQModel;
use App\Models\VehicleModel;
use App\Models\TripModel;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Delete All 
        TripModel::truncate();
        VehicleModel::truncate();
        FAQModel::truncate();
        UserModel::truncate();
        AdminModel::truncate();

        // Factory
        AdminModel::factory(5)->create();
        UserModel::factory(15)->create();
        FAQModel::factory(8)->state(['is_show' => 1])->create();
        FAQModel::factory(10)->state(['is_show' => 0])->create();
        VehicleModel::factory(45)->create();
        TripModel::factory(300)->create();
    }
}
