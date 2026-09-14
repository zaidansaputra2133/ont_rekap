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
        Schema::create('reporting_wos', function (Blueprint $table) {
            $table->id();
            $table->string('no_order', 100)->index();
            $table->string('cid', 50)->nullable();
            $table->string('serial_number', 100);
            $table->string('nama_teknisi', 150);
            $table->string('nik_teknisi', 50)->nullable();
            $table->string('status_wo', 50); // e.g. "Work Order Selesai"
            $table->date('tanggal_sa')->nullable();
            $table->string('vendor', 100)->nullable();
            $table->string('sektor', 50)->nullable();
            $table->string('cek_match', 50)->nullable(); // "SESUAI" / "Beda"
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
        Schema::dropIfExists('reporting_wos');
    }
};
