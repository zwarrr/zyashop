<?php

// Test route untuk cek storage writable di Vercel
Route::get('/test-storage', function () {
    $results = [
        'storage_path' => storage_path('app/public'),
        'is_writable' => is_writable(storage_path('app/public')),
        'exists' => file_exists(storage_path('app/public')),
        'cards_dir_exists' => file_exists(storage_path('app/public/cards')),
        'cards_dir_writable' => is_writable(storage_path('app/public/cards')),
        'tmp_writable' => is_writable('/tmp'),
    ];
    
    // Try create test file
    try {
        $testFile = storage_path('app/public/test.txt');
        file_put_contents($testFile, 'test');
        $results['test_write'] = 'SUCCESS';
        @unlink($testFile);
    } catch (\Exception $e) {
        $results['test_write'] = 'FAILED: ' . $e->getMessage();
    }
    
    return response()->json($results);
});
