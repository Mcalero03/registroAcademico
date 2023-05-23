<?php

namespace Database\Seeders;

use App\Models\InscriptionDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InscriptionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InscriptionDetail::insert([
            [
                "id" => 1,
                "inscription_id" => 1,
                "group_id" => 1,
                "status" => "En curso",
            ]
        ]);
    }
}
