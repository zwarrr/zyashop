<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource (for users).
     */
    public function index()
    {
        // TODO: Get kategoris for users view
        return view('users.kategori');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // TODO: Get kategori details
        return view('users.kategori-detail');
    }
}
