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
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users');
            $table->date('tanggal_kembali_aktual');
            $table->unsignedInteger('keterlambatan');
            $table->decimal('denda_per_hari', 10, 2);
            $table->decimal('denda_otomatis', 10, 2);
            $table->decimal('denda_dibayar', 10, 2)->nullable();
            $table->enum('kondisi_buku', ['baik', 'rusak', 'hilang']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
