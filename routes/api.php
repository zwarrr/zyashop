<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/test-session-table', function () {
    try {
        $exists = Schema::hasTable('sessions');
        
        if (!$exists) {
            // Create table if not exists
            Schema::create('sessions', function ($table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
            return response()->json([
                'status' => 'created',
                'message' => 'Sessions table created successfully'
            ]);
        }
        
        return response()->json([
            'status' => 'exists',
            'message' => 'Sessions table already exists',
            'count' => DB::table('sessions')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
