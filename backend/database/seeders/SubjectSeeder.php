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
                "subject_code" => "COMPMU",
                "subject_name" => "Composición musical",
                "average_approval" => 7.0,
                "units_value" => 2,
                "status" => "0"
            ],
            [
                "id" => 2,
                "subject_code" => "SENSO",
                "subject_name" => "Sensoriomotora",
                "average_approval" => 8.0,
                "units_value" => 2,
                "status" => "0"
            ],
            [
                "id" => 3,
                "subject_code" => "ARES",
                "subject_name" => "Artes escénicas",
                "average_approval" => 7.0,
                "units_value" => 2,
                "status" => "0"
            ],
            [
                "id" => 4,
                "subject_code" => "DRAM",
                "subject_name" => "Dramaturgia",
                "average_approval" => 7.0,
                "units_value" => 2,
                "status" => "1"
            ],
            [
                "id" => 5,
                "subject_code" => "TECDA",
                "subject_name" => "Técnicas de danza",
                "average_approval" => 8.0,
                "units_value" => 2,
                "status" => "1"
            ],
            [
                "id" => 6,
                "subject_code" => "COMPCO",
                "subject_name" => "Composición coreográfica",
                "average_approval" => 7.0,
                "units_value" => 2,
                "status" => "1"
            ],
        ]);
    }
}
