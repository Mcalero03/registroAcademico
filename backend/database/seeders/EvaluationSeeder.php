<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evaluation;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Evaluation::insert([
            [
                "id" => 1,
                "evaluation_name" => "Laboratorio",
                "ponder" => 0.10,
                "subject_id" => 1,
            ],
            [
                "id" => 2,
                "evaluation_name" => "Laboratorio",
                "ponder" => 0.10,
                "subject_id" => 2,
            ],
            [
                "id" => 3,
                "evaluation_name" => "Laboratorio",
                "ponder" => 0.10,
                "subject_id" => 3,
            ],
            [
                "id" => 4,
                "evaluation_name" => "Laboratorio",
                "ponder" => 0.10,
                "subject_id" => 4,
            ],
            [
                "id" => 5,
                "evaluation_name" => "Laboratorio",
                "ponder" => 0.10,
                "subject_id" => 5,
            ],
            [
                "id" => 6,
                "evaluation_name" => "Laboratorio",
                "ponder" => 0.10,
                "subject_id" => 6,
            ],

        ]);
    }
}
