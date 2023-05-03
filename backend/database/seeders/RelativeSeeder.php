<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Relative;

class RelativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relative::insert([
            [
                "id" => 1,
                "name" => "Francisca",
                "last_name" => "Portillo",
                "dui" => 164769199,
                "phone_number" => 71040596,
                "mail" => "fran.portillo@gmail.com",
                "student_id" => 1,
                "kinship_id" => 1,
            ],
            [
                "id" => 2,
                "name" => "Ana",
                "last_name" => "Vásquez",
                "dui" => 132769199,
                "phone_number" => 71404596,
                "mail" => "an.vasquez@gmail.com",
                "student_id" => 2,
                "kinship_id" => 1,

            ],
            [
                "id" => 3,
                "name" => "Luis",
                "last_name" => "Ortiz",
                "dui" => 167425389,
                "phone_number" => 71234567,
                "mail" => "luis.ortiz@example.com",
                "student_id" => 3,
                "kinship_id" => 2,

            ],
            [
                "id" => 4,
                "name" => "Juan",
                "last_name" => "Torres",
                "dui" => 172945683,
                "phone_number" => 61234567,
                "mail" => "juan.torres@example.com",
                "student_id" => 4,
                "kinship_id" => 2,
            ],
            [
                "id" => 5,
                "name" => "Ana",
                "last_name" => "García",
                "dui" => 128475936,
                "phone_number" => 61234067,
                "mail" => "ana.garcia@example.com",
                "student_id" => 5,
                "kinship_id" => 1,

            ],
            [
                "id" => 6,
                "name" => "María",
                "last_name" => "Pérez",
                "dui" => 149236875,
                "phone_number" => 71534567,
                "mail" => "ana.garcia@example.com",
                "student_id" => 6,
                "kinship_id" => 1,

            ],
            [
                "id" => 7,
                "name" => "Andrea",
                "last_name" => "Flores",
                "dui" => 129465837,
                "phone_number" => 61234568,
                "mail" => "andrea.flores@example.com",
                "student_id" => 7,
                "kinship_id" => 1,

            ],
            [
                "id" => 8,
                "name" => "Miguel",
                "last_name" => "Hernández",
                "dui" => 198256473,
                "phone_number" => 71234568,
                "mail" => "miguel.hernandez@example.com",
                "student_id" => 8,
                "kinship_id" => 2,

            ],
            [
                "id" => 9,
                "name" => "Sofía",
                "last_name" => "Sánchez",
                "dui" => 175836942,
                "phone_number" => 71236568,
                "mail" => "sofia.sanchez@example.com",
                "student_id" => 9,
                "kinship_id" => 1,

            ],
            [
                "id" => 10,
                "name" => " Carlos",
                "last_name" => "Castro",
                "dui" => 146985723,
                "phone_number" => 73695824,
                "mail" => "carlos.castro@example.com",
                "student_id" => 10,
                "kinship_id" => 2,

            ],
            [
                "id" => 11,
                "name" => "Laura",
                "last_name" => "Méndez",
                "dui" => 193246857,
                "phone_number" => 61234569,
                "mail" => "laura.mendez@example.com",
                "student_id" => 11,
                "kinship_id" => 1,

            ],
            [
                "id" => 12,
                "name" => "Antonio",
                "last_name" => "Ruiz",
                "dui" => 129547863,
                "phone_number" => 71234569,
                "mail" => "antonio.ruiz@example.com",
                "student_id" => 12,
                "kinship_id" => 2,

            ],
        ]);
    }
}
