<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inscription;

class InscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inscription::insert([
            [
                "id" => 1,
                "inscription_date" => "2023-01-14",
                "subject_average" => 1.0,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 1,
                "student_id" => 1,
                "group_id" => 1,
                "subject_id" => 1,
            ],
            [
                "id" => 2,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 1,
                "student_id" => 2,
                "group_id" => 1,
                "subject_id" => 1,
            ],
            [
                "id" => 3,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 1,
                "student_id" => 3,
                "group_id" => 2,
                "subject_id" => 2,
            ],
            [
                "id" => 4,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 1,
                "student_id" => 4,
                "group_id" => 2,
                "subject_id" => 2,
            ],
            [
                "id" => 5,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 1,
                "student_id" => 5,
                "group_id" => 3,
                "subject_id" => 3,
            ],
            [
                "id" => 6,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 1,
                "student_id" => 6,
                "group_id" => 3,
                "subject_id" => 3,
            ],
            [
                "id" => 7,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 2,
                "student_id" => 7,
                "group_id" => 4,
                "subject_id" => 4,
            ],
            [
                "id" => 8,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 2,
                "student_id" => 8,
                "group_id" => 4,
                "subject_id" => 4,
            ],
            [
                "id" => 9,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 2,
                "student_id" => 9,
                "group_id" => 5,
                "subject_id" => 5,
            ],
            [
                "id" => 10,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 2,
                "student_id" => 10,
                "group_id" => 5,
                "subject_id" => 5,
            ],
            [
                "id" => 11,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 2,
                "student_id" => 11,
                "group_id" => 6,
                "subject_id" => 6,
            ],
            [
                "id" => 12,
                "inscription_date" => "2023-01-14",
                "subject_average" => 0.9,
                "attendance_quantity" => 1,
                "status" => "Reprobado",
                "cycle_id" => 2,
                "student_id" => 12,
                "group_id" => 6,
                "subject_id" => 6,
            ],
        ]);
    }
}
