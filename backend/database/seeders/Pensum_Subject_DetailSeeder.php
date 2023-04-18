<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pensum_Subject_Detail;

class Pensum_Subject_DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pensum_Subject_Detail::insert([
            [
                "id" => 1,
                "pensum_id" => 1,
                "subject_id" => 1,
            ],
            [
                "id" => 2,
                "pensum_id" => 2,
                "subject_id" => 2,
            ],
            [
                "id" => 3,
                "pensum_id" => 3,
                "subject_id" => 3,
            ],
            [
                "id" => 4,
                "pensum_id" => 1,
                "subject_id" => 4,
            ],
            [
                "id" => 5,
                "pensum_id" => 2,
                "subject_id" => 5,
            ],
            [
                "id" => 6,
                "pensum_id" => 3,
                "subject_id" => 6,
            ],
        ]);
    }
}
