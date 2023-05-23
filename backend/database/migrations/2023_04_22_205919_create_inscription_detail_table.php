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
        Schema::create('inscription_detail', function (Blueprint $table) {
            $table->id();
            $table->string('status', 100)->comment('Retirado', 'Reprobado', 'Aprobado', 'En curso');
            $table->foreignId('inscription_id')->constrained('inscription');
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
        Schema::dropIfExists('inscription_detail');
    }
};
