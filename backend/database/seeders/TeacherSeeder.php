<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Teacher::insert([
            [
                "id" => 1,
                "name" => "Mónica",
                "last_name" => "Peña",
                "teacher_card" => "037421",
                "dui" => 164669109,
                "nit" => 161415020,
                "phone_number" => 63138239,
                "mail" => "m.peña@gmail.com",
                "school_id" => 1,
            ],
            [
                "id" => 2,
                "name" => "Luis",
                "last_name" => "Hernández",
                "teacher_card" => "032421",
                "dui" => 144689109,
                "nit" => 191415020,
                "phone_number" => 61148239,
                "mail" => "l.hdz@gmail.com",
                "school_id" => 2,
            ],
            [
                "id" => 3,
                "name" => "Rosy",
                "last_name" => "Castillo",
                "teacher_card" => "038921",
                "dui" => 195689109,
                "nit" => 125115020,
                "phone_number" => 61148985,
                "mail" => "r.castillo@gmail.com",
                "school_id" => 1,
            ],
        ]);
    }
}
