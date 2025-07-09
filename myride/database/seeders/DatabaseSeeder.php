<?php

namespace Database\Seeders;

use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\FAQModel;
use App\Models\VehicleModel;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AdminModel::factory(1)->create();
        UserModel::factory(1)->create();
        FAQModel::factory(8)->state(['is_show' => 1])->create();
        FAQModel::factory(10)->state(['is_show' => 0])->create();
        VehicleModel::factory(1)->create();
    }
}
