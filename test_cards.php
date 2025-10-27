<?php

use App\Models\User;
use App\Models\Card;

// Get first user
$user = User::first();

if ($user) {
    echo "User: {$user->name}\n";
    $cards = $user->cards()->get();
    echo "Cards count: {$cards->count()}\n";
    echo "Cards data:\n";
    foreach ($cards as $card) {
        echo "- {$card->title} ({$card->category})\n";
    }
} else {
    echo "No user found\n";
}

// Also check all cards
echo "\n\nAll Cards in DB:\n";
$allCards = Card::all();
echo "Total: {$allCards->count()}\n";
foreach ($allCards as $card) {
    echo "- ID: {$card->id}, Title: {$card->title}, User: {$card->user_id}\n";
}
