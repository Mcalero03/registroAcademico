<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CycleSubjectDetail;

class CycleSubjectDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CycleSubjectDetail::insert([
            [
                "id" => 1,
                "subject_id" => 1,
                "cycle_id" => 1
            ],
        ]);
    }
}
