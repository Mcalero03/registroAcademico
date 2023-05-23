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
        Schema::create('student_pensum_detail', function (Blueprint $table) {
            $table->id();
            $table->string('status', 100)->comment('Retirado', 'Finalizado', 'En curso');
            $table->foreignId('student_id')->constrained('student');
            $table->foreignId('pensum_id')->constrained('pensum');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_pensum_detail');
    }
};
