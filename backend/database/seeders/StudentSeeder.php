<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Student::insert([
            [
                "id" => 1,
                "name" => "Marta",
                "last_name" => "González",
                "age" => 9,
                "card" => "0105",
                "nie" => 7845963,
                "phone_number" => 71040596,
                "mail" => "marta.gonzalez@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 111,
            ],
            [
                "id" => 2,
                "name" => "Pablo ",
                "last_name" => "García",
                "age" => 9,
                "card" => "0205",
                "nie" => 7445963,
                "phone_number" => 71404596,
                "mail" => "pablo.garcia@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 43,
            ],
            [
                "id" => 3,
                "name" => "Carla",
                "last_name" => "Ortiz",
                "age" => 9,
                "card" => "0305",
                "nie" => 8845963,
                "phone_number" => 71234567,
                "mail" => "carla.ortiz@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 96,
            ],
            [
                "id" => 4,
                "name" => "Diego",
                "last_name" => "Torres",
                "age" => 9,
                "card" => "0405",
                "nie" => 7845966,
                "phone_number" => 61234567,
                "mail" => "diego.torres@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 104,
            ],
            [
                "id" => 5,
                "name" => "Andrea",
                "last_name" => "Fernández",
                "age" => 9,
                "card" => "0505",
                "nie" => 7845962,
                "phone_number" => 61234067,
                "mail" => "andrea.fernandez@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 4,
            ],
            [
                "id" => 6,
                "name" => "Javier",
                "last_name" => "López",
                "age" => 9,
                "card" => "0605",
                "nie" => 7845961,
                "phone_number" => 71534567,
                "mail" => "javier.lopez@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 25,
            ],
            [
                "id" => 7,
                "name" => "Lucía",
                "last_name" => "Sánchez",
                "age" => 9,
                "card" => "0705",
                "nie" => 7845965,
                "phone_number" => 61234568,
                "mail" => "lucia.sanchez@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 104,
            ],
            [
                "id" => 8,
                "name" => "David",
                "last_name" => "Hernández",
                "age" => 9,
                "card" => "0805",
                "nie" => 7885963,
                "phone_number" => 71234568,
                "mail" => "david.hernández@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 104,
            ],
            [
                "id" => 9,
                "name" => "Alba",
                "last_name" => "Pérez",
                "age" => 9,
                "card" => "0905",
                "nie" => 7845663,
                "phone_number" => 71236568,
                "mail" => "alba.perez@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 111,
            ],
            [
                "id" => 10,
                "name" => "Juan",
                "last_name" => "Castro",
                "age" => 9,
                "card" => "1005",
                "nie" => 7844963,
                "phone_number" => 73695824,
                "mail" => "juan.castro@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 4,
            ],
            [
                "id" => 11,
                "name" => "Mario",
                "last_name" => "Castro",
                "age" => 9,
                "card" => "1105",
                "nie" => 7841963,
                "phone_number" => 61234569,
                "mail" => "mario.castro@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 4,
            ],
            [
                "id" => 12,
                "name" => "Alejandro",
                "last_name" => "Ruiz",
                "age" => 9,
                "card" => "1205",
                "nie" => 7845363,
                "phone_number" => 71234569,
                "mail" => "alejandro.ruiz@gmail.com",
                "admission_date" => "2023-01-14",
                "municipalities_id" => 9,
            ],
        ]);
    }
}
