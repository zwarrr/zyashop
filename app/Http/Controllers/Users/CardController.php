<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
    /**
     * Display a listing of the resource (for users).
     */
    public function index()
    {
        // TODO: Get cards for users view
        return view('users.cards');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // TODO: Get card details
        return view('users.card-detail');
    }

    /**
     * Get card image separately (to avoid payload too large on home page)
     */
    public function getImage(string $id)
    {
        try {
            $card = Card::findOrFail($id);
            
            if (!$card->image) {
                return response()->json([
                    'success' => false,
                    'placeholder' => "https://placehold.co/1080x1080?text=" . urlencode($card->title)
                ])->header('Content-Type', 'application/json');
            }
            
            return response()->json([
                'success' => true,
                'image' => $card->image
            ])->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            \Log::error('Card image fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 404)->header('Content-Type', 'application/json');
        }
    }
}
