<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher_Subject_Detail;

class Teacher_Subject_DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Teacher_Subject_Detail::insert([
            [
                "id" => 1,
                "subject_id" => 1,
                "teacher_id" => 1,
            ],
            [
                "id" => 2,
                "subject_id" => 2,
                "teacher_id" => 2,
            ],
            [
                "id" => 3,
                "subject_id" => 3,
                "teacher_id" => 3,
            ],
            [
                "id" => 4,
                "subject_id" => 4,
                "teacher_id" => 1,
            ],
            [
                "id" => 5,
                "subject_id" => 5,
                "teacher_id" => 2,
            ],
            [
                "id" => 6,
                "subject_id" => 6,
                "teacher_id" => 3,
            ],
        ]);
    }
}
