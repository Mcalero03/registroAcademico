<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classroom;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classroom::insert([
            [
                "id" => 1,
                "classroom_code" => "CLASS1",
                "classroom_name" => "Classroom1",
                "capacity" => 10,
                "status" => "Habilitado",
                "school_id" => 1,
            ],
            [
                "id" => 2,
                "classroom_code" => "CLASS2",
                "classroom_name" => "Classroom2",
                "capacity" => 10,
                "status" => "Habilitado",
                "school_id" => 2,
            ],
        ]);
    }
}
