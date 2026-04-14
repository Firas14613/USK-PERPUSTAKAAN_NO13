<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_kategori', function (Blueprint $table) {
            $table->foreignId('buku_id')->constrained('buku')->cascadeOnDelete();
            $table->foreignId('kategori_buku_id')->constrained('kategori_buku')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['buku_id', 'kategori_buku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_kategori');
    }
};

