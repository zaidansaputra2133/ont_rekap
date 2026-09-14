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
        Schema::create('ont_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number', 100);
            $table->string('nama_teknisi', 150);
            $table->date('tanggal_keluar');
            $table->string('keterangan', 50)->nullable(); // hanya diisi "Rusak" atau null
            $table->text('catatan')->nullable();
            $table->timestamps();

            // FK ke ont_masuks.serial_number
            $table->foreign('serial_number')
                  ->references('serial_number')
                  ->on('ont_masuks')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ont_keluars');
    }
};
