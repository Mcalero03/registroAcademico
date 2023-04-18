<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pensum', function (Blueprint $table) {
            $table->id();
            $table->string('program_name', 100);
            $table->integer('uv_total');
            $table->integer('required_subject');
            $table->integer('optional_subject');
            $table->integer('cycle_quantity');
            $table->string('study_plan_year', 45);
            $table->foreignId('college_id')->constrained('college');
            $table->foreignId('pensum_type_id')->constrained('pensum_type');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pensum');
    }
};
