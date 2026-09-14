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
        Schema::create('ont_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number', 100)->unique();
            $table->string('brand', 50)->nullable();
            $table->date('tanggal_masuk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ont_masuks');
    }
};
