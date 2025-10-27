<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
