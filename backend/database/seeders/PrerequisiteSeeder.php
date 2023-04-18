<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prerequisite;

class PrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Prerequisite::insert([
            [
                "id" => 1,
                "subject_id" => 4,
                "pensum_subject_detail_id" => 1,
            ],
            [
                "id" => 2,
                "subject_id" => 5,
                "pensum_subject_detail_id" => 2,
            ],
            [
                "id" => 3,
                "subject_id" => 6,
                "pensum_subject_detail_id" => 3,
            ],
        ]);
    }
}
