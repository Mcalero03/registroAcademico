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
        Schema::create('calification', function (Blueprint $table) {
            $table->id();
            $table->float('score', 10, 0);
            $table->foreignId('evaluation_id')->constrained('evaluation');
            $table->foreignId('inscription_detail_id')->constrained('inscription_detail');
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
        Schema::dropIfExists('calification');
    }
};
