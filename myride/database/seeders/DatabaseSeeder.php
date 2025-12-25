<?php

namespace Database\Seeders;

use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\FAQModel;
use App\Models\VehicleModel;
use App\Models\TripModel;
use App\Models\WashModel;
use App\Models\FuelModel;
use App\Models\HistoryModel;
use App\Models\InventoryModel;
use App\Models\ServiceModel;
use App\Models\DriverModel;
use App\Models\DriverVehicleRelationModel;

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
        WashModel::truncate();
        FuelModel::truncate();
        HistoryModel::truncate();
        InventoryModel::truncate();
        VehicleModel::truncate();
        DriverModel::truncate();
        UserModel::truncate();
        AdminModel::truncate();
        ServiceModel::truncate();
        DriverVehicleRelationModel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Factory
        AdminModel::factory(5)->create();
        UserModel::factory(15)->create();
        FAQModel::factory(8)->state(['is_show' => 1])->create();
        FAQModel::factory(10)->state(['is_show' => 0])->create();
        VehicleModel::factory(45)->create();
        DriverModel::factory(50)->create();
        TripModel::factory(300)->create();
        WashModel::factory(300)->create();
        FuelModel::factory(200)->create();
        HistoryModel::factory(200)->create();
        InventoryModel::factory(200)->create();
        ServiceModel::factory(50)->create();
        DriverVehicleRelationModel::factory(70)->create();
    }
}
