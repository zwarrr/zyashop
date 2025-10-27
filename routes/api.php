<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/create-sessions-table', function () {
    try {
        // Drop table if exists
        Schema::dropIfExists('sessions');
        
        // Create fresh table
        Schema::create('sessions', function ($table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Sessions table created successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/check-tables', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $tableList = array_map(function($table) {
            $values = array_values((array)$table);
            return $values[0];
        }, $tables);
        
        $hasSessionsTable = in_array('sessions', $tableList);
        $sessionCount = $hasSessionsTable ? DB::table('sessions')->count() : 0;
        
        return response()->json([
            'status' => 'success',
            'all_tables' => $tableList,
            'has_sessions_table' => $hasSessionsTable,
            'session_records' => $sessionCount
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
