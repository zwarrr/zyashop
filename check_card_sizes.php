<?php
require 'bootstrap/app.php';

use App\Models\Card;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$cards = Card::all();
$total = 0;
foreach($cards as $card) {
    if ($card->image) {
        $size = strlen($card->image) / 1024;
        echo $card->title . ': ' . round($size, 2) . ' KB' . PHP_EOL;
        $total += $size;
    }
}
echo 'Total: ' . round($total, 2) . ' KB' . PHP_EOL;
