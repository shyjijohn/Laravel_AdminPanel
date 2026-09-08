<?php

use Illuminate\Database\Migrations\Migration;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The original table migrations already create these unique indexes.
        // Retained as a no-op so existing migration histories remain valid.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes are made in up(), so there is nothing to reverse.
    }
};
