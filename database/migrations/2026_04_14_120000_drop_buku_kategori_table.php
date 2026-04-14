<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('buku_kategori');
    }

    public function down(): void
    {
        Schema::create('buku_kategori', function (Blueprint $table) {
            $table->unsignedBigInteger('buku_id');
            $table->unsignedBigInteger('kategori_buku_id');
            $table->timestamps();

            $table->primary(['buku_id', 'kategori_buku_id']);
            $table->foreign('buku_id')->references('id')->on('buku')->cascadeOnDelete();
            $table->foreign('kategori_buku_id')->references('id')->on('kategori_buku')->cascadeOnDelete();
        });
    }
};

