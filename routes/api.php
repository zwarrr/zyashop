<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

Route::get('/debug-session', function (Request $request) {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'auth_check' => Auth::check(),
        'auth_id' => Auth::id(),
        'cookies' => $request->cookies->all(),
        'session_driver' => config('session.driver'),
        'session_domain' => config('session.domain'),
    ]);
});

Route::get('/test-login', function (Request $request) {
    $user = \App\Models\User::first();
    
    if ($user) {
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('test', 'value');
        $request->session()->save();
        
        return response()->json([
            'status' => 'logged_in',
            'user' => $user->email,
            'session_id' => session()->getId(),
            'auth_check' => Auth::check(),
        ]);
    }
    
    return response()->json(['status' => 'no_user']);
});

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
