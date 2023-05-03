<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kinship;

class KinshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kinship::insert([
            [
                "id" => 1,
                "kinship" => "Madre",
            ],
            [
                "id" => 2,
                "kinship" => "Padre",
            ],
        ]);
    }
}
