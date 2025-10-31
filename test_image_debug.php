<?php
require 'vendor/autoload.php';

use App\Models\Product;

$app = require 'bootstrap/app.php';

// Get products
$products = Product::where('status', '!=', 'inactive')->limit(5)->get();

echo "Total Products: " . $products->count() . "\n\n";

foreach ($products as $product) {
    echo "=== Product ID: {$product->id} ===\n";
    echo "Title: {$product->title}\n";
    echo "Card ID: {$product->card_id}\n";
    
    // Check raw attribute
    $rawImage = $product->attributes['image'] ?? null;
    echo "Raw image attribute: " . (!empty($rawImage) ? substr($rawImage, 0, 100) . '...' : 'EMPTY') . "\n";
    
    // Check via property (should trigger makeVisible if we've called it)
    $img = $product->image ?? null;
    echo "Via property access: " . (!empty($img) ? substr($img, 0, 100) . '...' : 'HIDDEN') . "\n";
    
    echo "\n";
}
?>
