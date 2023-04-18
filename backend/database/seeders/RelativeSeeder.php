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
                "relationship" => "Madre",
                "name" => "Francisca",
                "last_name" => "Portillo",
                "dui" => 164769199,
                "phone_number" => 71040596,
                "mail" => "fran.portillo@gmail.com",
            ],
            [
                "id" => 2,
                "relationship" => "Madre",
                "name" => "Ana",
                "last_name" => "Vásquez",
                "dui" => 132769199,
                "phone_number" => 71404596,
                "mail" => "an.vasquez@gmail.com",
            ],
            [
                "id" => 3,
                "relationship" => "Padre",
                "name" => "Luis",
                "last_name" => "Ortiz",
                "dui" => 167425389,
                "phone_number" => 71234567,
                "mail" => "luis.ortiz@example.com",
            ],
            [
                "id" => 4,
                "relationship" => "Padre",
                "name" => "Juan",
                "last_name" => "Torres",
                "dui" => 172945683,
                "phone_number" => 61234567,
                "mail" => "juan.torres@example.com",
            ],
            [
                "id" => 5,
                "relationship" => "Madre",
                "name" => "Ana",
                "last_name" => "García",
                "dui" => 128475936,
                "phone_number" => 61234067,
                "mail" => "ana.garcia@example.com",
            ],
            [
                "id" => 6,
                "relationship" => "Madre",
                "name" => "María",
                "last_name" => "Pérez",
                "dui" => 149236875,
                "phone_number" => 71534567,
                "mail" => "ana.garcia@example.com",
            ],
            [
                "id" => 7,
                "relationship" => "Madre",
                "name" => "Andrea",
                "last_name" => "Flores",
                "dui" => 129465837,
                "phone_number" => 61234568,
                "mail" => "andrea.flores@example.com",
            ],
            [
                "id" => 8,
                "relationship" => "Padre",
                "name" => "Miguel",
                "last_name" => "Hernández",
                "dui" => 198256473,
                "phone_number" => 71234568,
                "mail" => "miguel.hernandez@example.com",
            ],
            [
                "id" => 9,
                "relationship" => "Madre",
                "name" => "Sofía",
                "last_name" => "Sánchez",
                "dui" => 175836942,
                "phone_number" => 71236568,
                "mail" => "sofia.sanchez@example.com",
            ],
            [
                "id" => 10,
                "relationship" => "Padre",
                "name" => " Carlos",
                "last_name" => "Castro",
                "dui" => 146985723,
                "phone_number" => 73695824,
                "mail" => "carlos.castro@example.com",
            ],
            [
                "id" => 11,
                "relationship" => "Madre",
                "name" => "Laura",
                "last_name" => "Méndez",
                "dui" => 193246857,
                "phone_number" => 61234569,
                "mail" => "laura.mendez@example.com",
            ],
            [
                "id" => 12,
                "relationship" => "Padre",
                "name" => "Antonio",
                "last_name" => "Ruiz",
                "dui" => 129547863,
                "phone_number" => 71234569,
                "mail" => "antonio.ruiz@example.com",
            ],
        ]);
    }
}
