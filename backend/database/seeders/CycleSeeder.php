<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cycle;

class CycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cycle::insert([
            [
                "id" => 1,
                "cycle_number" => 1,
                "year" => 2023,
                "start_date" => "2023-01-15",
                "end_date" => "2023-06-15",
                "status" => "Abierto",
            ],
            [
                "id" => 2,
                "cycle_number" => 2,
                "year" => 2023,
                "start_date" => "2023-07-15",
                "end_date" => "2023-12-15",
                "status" => "Creado",
            ],
        ]);
    }
}
