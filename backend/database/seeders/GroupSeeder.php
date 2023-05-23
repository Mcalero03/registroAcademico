<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::insert([
            [
                "id" => 1,
                "group_code" => "COMPMU01",
                'subject_id' => 1,
                'teacher_id' => 1,
                "students_quantity" => 2,
            ],
            [
                "id" => 2,
                "group_code" => "SENMO01",
                'subject_id' => 2,
                'teacher_id' => 2,
                "students_quantity" => 2,
            ],
            [
                "id" => 3,
                "group_code" => "ARES01",
                'subject_id' => 3,
                'teacher_id' => 3,
                "students_quantity" => 2,
            ],
            [
                "id" => 4,
                "group_code" => "DRAM02",
                'subject_id' => 4,
                'teacher_id' => 1,
                "students_quantity" => 2,
            ],
            [
                "id" => 5,
                "group_code" => "TECDA02",
                'subject_id' => 5,
                'teacher_id' => 2,
                "students_quantity" => 2,
            ],
            [
                "id" => 6,
                "group_code" => "COMCO02",
                'subject_id' => 6,
                'teacher_id' => 3,
                "students_quantity" => 2,
            ],
        ]);
    }
}
