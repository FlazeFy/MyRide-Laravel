<?php

namespace Database\Seeders;

use App\Models\AdminModel;
use App\Models\UserModel;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AdminModel::factory(1)->create();
        UserModel::factory(1)->create();
    }
}
