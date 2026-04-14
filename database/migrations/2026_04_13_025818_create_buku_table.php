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
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku', 50)->unique();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->year('tahun_terbit');
            $table->foreignId('kategori_id')->constrained('kategori_buku');
            $table->string('isbn', 20)->nullable();
            $table->unsignedInteger('jumlah_halaman')->nullable();
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('stok')->default(0);
            $table->string('lokasi_rak', 50)->nullable();
            $table->string('cover_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
