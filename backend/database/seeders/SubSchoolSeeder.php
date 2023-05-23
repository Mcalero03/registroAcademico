<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubSchool;

class SubSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubSchool::insert([
            [
                "id" => 1,
                "sub_school_name" => "Ballet",
                "school_id" => 2,
            ],
            [
                "id" => 2,
                "sub_school_name" => "Pre Danza",
                "school_id" => 2,
            ],
            [
                "id" => 3,
                "sub_school_name" => "Contemporánea",
                "school_id" => 2,
            ],
        ]);
    }
}
