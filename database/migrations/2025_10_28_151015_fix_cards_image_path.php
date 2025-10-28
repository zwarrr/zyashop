<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all cards that have 'cards/' prefix in image path
        DB::table('cards')
            ->where('image', 'like', 'cards/%')
            ->update([
                'image' => DB::raw("REPLACE(image, 'cards/', '')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back by adding 'cards/' prefix
        DB::table('cards')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->update([
                'image' => DB::raw("CONCAT('cards/', image)")
            ]);
    }
};
