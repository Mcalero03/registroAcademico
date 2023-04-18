<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pensum_Type;

class Pensum_TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pensum_Type::insert([
            [
                "id" => 1,
                "pensum_type_name" => "Curso Regular",
            ],
            [
                "id" => 2,
                "pensum_type_name" => "Curso Libre",
            ],
        ]);
    }
}
