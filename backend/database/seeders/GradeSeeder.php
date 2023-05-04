<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Grade::insert([
            [
                "id" => 1,
                "score" => 10,
                "score_date" => "2023-01-16",
                "status" => "Aprobado",
                "evaluation_id" => 1,
                "inscription_id" => 1,
            ],
            // [
            //     "id" => 2,
            //     "score" => 9,
            //     "score_date" => "2023-01-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 1,
            //     "inscription_id" => 2,
            // ],
            // [
            //     "id" => 3,
            //     "score" => 9,
            //     "score_date" => "2023-01-18",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 2,
            //     "inscription_id" => 3,
            // ],
            // [
            //     "id" => 4,
            //     "score" => 9,
            //     "score_date" => "2023-01-18",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 2,
            //     "inscription_id" => 4,
            // ],
            // [
            //     "id" => 5,
            //     "score" => 9,
            //     "score_date" => "2023-01-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 3,
            //     "inscription_id" => 5,
            // ],
            // [
            //     "id" => 6,
            //     "score" => 9,
            //     "score_date" => "2023-01-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 3,
            //     "inscription_id" => 6,
            // ],
            // [
            //     "id" => 7,
            //     "score" => 9,
            //     "score_date" => "2023-07-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 4,
            //     "inscription_id" => 7,
            // ],
            // [
            //     "id" => 8,
            //     "score" => 9,
            //     "score_date" => "2023-07-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 4,
            //     "inscription_id" => 8,
            // ],
            // [
            //     "id" => 9,
            //     "score" => 9,
            //     "score_date" => "2023-07-19",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 5,
            //     "inscription_id" => 9,
            // ],
            // [
            //     "id" => 10,
            //     "score" => 9,
            //     "score_date" => "2023-07-19",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 5,
            //     "inscription_id" => 10,
            // ],
            // [
            //     "id" => 11,
            //     "score" => 9,
            //     "score_date" => "2023-07-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 6,
            //     "inscription_id" => 11,
            // ],
            // [
            //     "id" => 12,
            //     "score" => 9,
            //     "score_date" => "2023-07-16",
            //     "status" => "Aprobado",
            //     "evaluation_id" => 6,
            //     "inscription_id" => 12,
            // ],
        ]);
    }
}
