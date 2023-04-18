<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::insert([
            [
                "id" => 1,
                "subject_name" => "Composición musical",
                "average_approval" => 7.0,
                "units_value" => 2,
            ],
            [
                "id" => 2,
                "subject_name" => "Sensoriomotora",
                "average_approval" => 8.0,
                "units_value" => 2,
            ],
            [
                "id" => 3,
                "subject_name" => "Artes escénicas",
                "average_approval" => 7.0,
                "units_value" => 2,
            ],
            [
                "id" => 4,
                "subject_name" => "Dramaturgia",
                "average_approval" => 7.0,
                "units_value" => 2,
            ],
            [
                "id" => 5,
                "subject_name" => "Técnicas de danza",
                "average_approval" => 8.0,
                "units_value" => 2,
            ],
            [
                "id" => 6,
                "subject_name" => "Composición coreográfica",
                "average_approval" => 7.0,
                "units_value" => 2,
            ],
        ]);
    }
}
