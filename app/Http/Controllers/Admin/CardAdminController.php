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
    public function index(Request $request)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            $cards = auth()->user()->cards()->get();
            return response()->json(['cards' => $cards]);
        }
        
        // Otherwise return view
        $cards = auth()->user()->cards()->get();
        $categories = auth()->user()->categories()->get();
        return view('admin.cards', compact('cards', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            try {
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'category' => 'required|string',
                    'status' => 'required|in:active,inactive',
                    'image' => 'nullable|image|mimes:jpeg,png,gif|max:10240',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'error' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            // Validate image dimensions (1080x1080) if image provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Check if image is valid
                if (!$image->isValid()) {
                    return response()->json(['error' => 'File gambar tidak valid'], 422);
                }
                
                $dimensions = getimagesize($image->path());
                if ($dimensions[0] != 1080 || $dimensions[1] != 1080) {
                    return response()->json(['error' => 'Gambar harus berukuran 1080x1080 pixel'], 422);
                }
                
                // Ensure cards directory exists
                $cardsDir = storage_path('app/public/cards');
                if (!file_exists($cardsDir)) {
                    mkdir($cardsDir, 0755, true);
                }
                
                // Generate custom filename: card-{slug}.{extension}
                $slug = Str::slug($validated['title']);
                $extension = $image->getClientOriginalExtension();
                $filename = 'card-' . $slug . '.' . $extension;
                
                // Check if file exists, add counter if needed
                $counter = 1;
                while (\Storage::disk('public')->exists('cards/' . $filename)) {
                    $filename = 'card-' . $slug . '-' . $counter . '.' . $extension;
                    $counter++;
                }
                
                $path = $image->storeAs('cards', $filename, 'public');
                $validated['image'] = $path; // Store as "cards/filename.ext"
            }

            // Generate slug from title
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
            $validated['user_id'] = auth()->id();

            $card = Card::create($validated);
            
            // Add image URL to response
            $cardData = $card->toArray();
            if ($card->image) {
                $cardData['image_url'] = asset('storage/' . $card->image);
            }

            return response()->json([
                'success' => 'Card berhasil ditambahkan', 
                'card' => $cardData
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('Card store error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
            
            // Generate custom filename: card-{slug}.{extension}
            $slug = Str::slug($validated['title']);
            $extension = $image->getClientOriginalExtension();
            $filename = 'card-' . $slug . '.' . $extension;
            
            // Check if file exists, add counter if needed
            $counter = 1;
            while (\Storage::disk('public')->exists('cards/' . $filename) && $filename !== basename($card->image)) {
                $filename = 'card-' . $slug . '-' . $counter . '.' . $extension;
                $counter++;
            }
            
            $path = $image->storeAs('cards', $filename, 'public');
            $validated['image'] = $path;
        }

        // Update slug if title changed
        if ($validated['title'] !== $card->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $card->update($validated);
        
        // Add image URL to response
        $cardData = $card->fresh()->toArray();
        if ($card->image) {
            $cardData['image_url'] = asset('storage/' . $card->image);
        }

        return response()->json(['success' => 'Card berhasil diperbarui', 'card' => $cardData]);
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
