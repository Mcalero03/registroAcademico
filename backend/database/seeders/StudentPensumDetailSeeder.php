<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentPensumDetail;

class StudentPensumDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentPensumDetail::insert([
            [
                "id" => 1,
                "status" => "En curso",
                "student_id" => 1,
                "pensum_id" => 1,
            ],
            [
                "id" => 2,
                "status" => "En curso",
                "student_id" => 1,
                "pensum_id" => 2,
            ],
        ]);
    }
}
