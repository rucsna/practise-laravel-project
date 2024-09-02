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
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('measurement_day_id');
            $table->unsignedBigInteger('parameter_id');
            $table->integer('value');
            $table->timestamps();

            $table->foreign('parameter_id')->references('id')->on('parameters');
            $table->foreign('measurement_day_id')->references('id')->on('measurement_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
