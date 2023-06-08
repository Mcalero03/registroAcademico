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
                "group_id" => 1,
            ],

        ]);
    }
}
