<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize blank values so MySQL can allow multiple NULLs under a unique index.
        DB::table('buku')
            ->where('isbn', '')
            ->orWhereRaw("TRIM(isbn) = ''")
            ->update(['isbn' => null]);

        // If duplicate ISBNs exist, keep the oldest record and null-out the others
        // so the unique index can be applied without breaking the migration.
        DB::statement("
            UPDATE buku b
            INNER JOIN (
                SELECT isbn, MIN(id) AS keep_id
                FROM buku
                WHERE isbn IS NOT NULL AND TRIM(isbn) <> ''
                GROUP BY isbn
                HAVING COUNT(*) > 1
            ) d ON d.isbn = b.isbn
            SET b.isbn = NULL
            WHERE b.id <> d.keep_id
        ");

        Schema::table('buku', function (Blueprint $table) {
            $table->unique('isbn');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropUnique(['isbn']);
        });
    }
};
