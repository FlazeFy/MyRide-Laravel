<?php

namespace Database\Seeders;

use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\FAQModel;
use App\Models\VehicleModel;
use App\Models\TripModel;
use App\Models\CleanModel;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Delete All 
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TripModel::truncate();
        FAQModel::truncate();
        CleanModel::truncate();
        VehicleModel::truncate();
        UserModel::truncate();
        AdminModel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Factory
        AdminModel::factory(5)->create();
        UserModel::factory(15)->create();
        FAQModel::factory(8)->state(['is_show' => 1])->create();
        FAQModel::factory(10)->state(['is_show' => 0])->create();
        VehicleModel::factory(45)->create();
        TripModel::factory(300)->create();
        CleanModel::factory(300)->create();
    }
}
