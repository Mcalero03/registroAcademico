<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pensum;

class PensumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pensum::insert([
            [
                "id" => 1,
                "program_name" => "Danza Clásica",
                "uv_total" => 40,
                "required_subject" => 15,
                "optional_subject" => 5,
                "cycle_quantity" => 4,
                "study_plan_year" => "2023",
                "sub_school_id" => 1,
                "pensum_type_id" => 1,
            ],
            [
                "id" => 2,
                "program_name" => "Expresión corporal",
                "uv_total" => 32,
                "required_subject" => 15,
                "optional_subject" => 1,
                "cycle_quantity" => 3,
                "study_plan_year" => "2023",
                "sub_school_id" => 2,
                "pensum_type_id" => 1,
            ],
            [
                "id" => 3,
                "program_name" => "Danza Expresiva",
                "uv_total" => 40,
                "required_subject" => 15,
                "optional_subject" => 5,
                "cycle_quantity" => 4,
                "study_plan_year" => "2023",
                "sub_school_id" => 3,
                "pensum_type_id" => 1,
            ],
        ]);
    }
}
