<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class KategoriAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = auth()->user()->categories()->get();
        return view('admin.kategori', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cards' => 'required|string',
            'type_tiktok' => 'nullable|boolean',
            'type_shopee' => 'nullable|boolean',
        ]);

        // Collect selected types
        $types = [];
        if ($request->has('type_tiktok')) {
            $types[] = 'tiktok';
        }
        if ($request->has('type_shopee')) {
            $types[] = 'shopee';
        }

        if (empty($types)) {
            return response()->json(['error' => 'Minimal pilih 1 type'], 422);
        }

        // Generate category_id from cards value
        $categoryId = 'CTR' . str_replace('-', '_', $validated['cards']);

        $category = Category::updateOrCreate(
            ['category_id' => $categoryId],
            [
                'cards' => $validated['cards'],
                'types' => $types,
                'status' => 'active',
                'user_id' => auth()->id(),
            ]
        );

        return response()->json(['success' => 'Kategori berhasil ditambahkan', 'category' => $category], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        
        // Check if user owns this category
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Return JSON for AJAX modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'category' => [
                    'id' => $category->id,
                    'cards' => $category->cards,
                    'types' => $category->types,
                    'status' => $category->status,
                ]
            ]);
        }
        
        return view('admin.kategori', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        
        // Check if user owns this category
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'cards' => 'required|string',
            'type_tiktok' => 'nullable|boolean',
            'type_shopee' => 'nullable|boolean',
        ]);

        // Collect selected types
        $types = [];
        if ($request->has('type_tiktok')) {
            $types[] = 'tiktok';
        }
        if ($request->has('type_shopee')) {
            $types[] = 'shopee';
        }

        if (empty($types)) {
            return response()->json(['error' => 'Minimal pilih 1 type'], 422);
        }

        $category->update([
            'cards' => $validated['cards'],
            'types' => $types,
        ]);

        return response()->json(['success' => 'Kategori berhasil diperbarui', 'category' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Kategori berhasil dihapus']);
    }
}
