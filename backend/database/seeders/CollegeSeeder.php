<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\College;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        College::insert([
            [
                "id" => 1,
                "college_name" => "Ballet",
                "direction_id" => 2,
            ],
            [
                "id" => 2,
                "college_name" => "Pre Danza",
                "direction_id" => 2,
            ],
            [
                "id" => 3,
                "college_name" => "Contemporánea",
                "direction_id" => 2,
            ],
        ]);
    }
}
