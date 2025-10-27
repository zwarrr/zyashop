<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = auth()->user()->cards()->get();
        $categories = auth()->user()->categories()->get();
        return view('admin.cards', compact('cards', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,gif|max:10240',
        ]);

        // Validate image dimensions (1080x1080) if image provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $dimensions = getimagesize($image->path());
            if ($dimensions[0] != 1080 || $dimensions[1] != 1080) {
                return response()->json(['error' => 'Gambar harus berukuran 1080x1080 pixel'], 422);
            }
            
            $path = $image->store('cards', 'public');
            $validated['image'] = $path;
        }

        // Generate slug from title
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['user_id'] = auth()->id();

        $card = Card::create($validated);

        return response()->json(['success' => 'Card berhasil ditambahkan', 'card' => $card], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $card = Card::findOrFail($id);
        
        // Check if user owns this card
        if ($card->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Return JSON for AJAX modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'card' => [
                    'id' => $card->id,
                    'title' => $card->title,
                    'category' => $card->category,
                    'description' => $card->description,
                    'status' => $card->status,
                    'image_url' => $card->image ? '/storage/' . $card->image : null,
                ]
            ]);
        }
        
        return view('admin.cards', compact('card'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $card = Card::findOrFail($id);
        
        // Check if user owns this card
        if ($card->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,gif|max:10240',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $dimensions = getimagesize($image->path());
            if ($dimensions[0] != 1080 || $dimensions[1] != 1080) {
                return response()->json(['error' => 'Gambar harus berukuran 1080x1080 pixel'], 422);
            }
            
            // Delete old image
            if ($card->image) {
                \Storage::disk('public')->delete($card->image);
            }
            
            $path = $image->store('cards', 'public');
            $validated['image'] = $path;
        }

        // Update slug if title changed
        if ($validated['title'] !== $card->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $card->update($validated);

        return response()->json(['success' => 'Card berhasil diperbarui', 'card' => $card]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $card = Card::findOrFail($id);

        // Delete image
        if ($card->image) {
            \Storage::disk('public')->delete($card->image);
        }

        $card->delete();

        return response()->json(['success' => 'Card berhasil dihapus']);
    }
}
