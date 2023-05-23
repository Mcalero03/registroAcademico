<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_classroom_group_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('cycle_id');
            $table->foreignId('schedule_id')->constrained('schedule');
            $table->foreignId('classroom_id')->constrained('classroom');
            $table->foreignId('group_id')->constrained('group');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_classroom_subject_detail');
    }
};
