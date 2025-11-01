<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();

echo "=== USER INFO ===\n";
echo 'User ID: ' . $user->id . "\n";
echo 'User Name: ' . $user->name . "\n";

echo "\n=== CARDS COUNT ===\n";
echo 'Total Cards: ' . $user->cards()->count() . "\n";
echo 'Active Cards: ' . $user->cards()->where('status', 'active')->count() . "\n";

echo "\n=== CARDS DATA (First 3) ===\n";
$cards = $user->cards()->where('status', 'active')->limit(3)->get();
foreach ($cards as $card) {
    echo 'Card: ' . $card->title . ' (ID: ' . $card->id . ', Status: ' . $card->status . ")\n";
    echo '  Products Count: ' . $card->products()->count() . "\n";
    echo '  Active Products: ' . $card->products()->where('status', '!=', 'inactive')->count() . "\n";
}

echo "\n=== PRODUCTS COUNT ===\n";
echo 'Total Products: ' . $user->products()->count() . "\n";
echo 'Active Products: ' . $user->products()->where('status', 'active')->count() . "\n";

echo "\n=== PROFILE ===\n";
$profile = $user->profile;
if ($profile) {
    echo 'Profile exists: ' . $profile->name . "\n";
} else {
    echo 'No profile\n';
}

echo "\n=== LINKS ===\n";
echo 'Total Links: ' . $user->links()->count() . "\n";
