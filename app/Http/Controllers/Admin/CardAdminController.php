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
        // Get cards WITHOUT images to avoid large payloads
        $cards = auth()->user()->cards()
            ->select('id', 'title', 'category', 'slug', 'status', 'user_id', 'created_at', 'updated_at')
            ->get();
        
        \Log::info('CardAdminController index() - rendering view', [
            'cards_count' => $cards->count()
        ]);
        
        // If AJAX request, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['cards' => $cards]);
        }
        
        // Otherwise return view
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
                
                \Log::info('Store - Image upload detected', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType()
                ]);
                
                // Check if image is valid
                if (!$image->isValid()) {
                    return response()->json(['error' => 'File gambar tidak valid'], 422);
                }
                
                $dimensions = getimagesize($image->path());
                if ($dimensions[0] != 1080 || $dimensions[1] != 1080) {
                    return response()->json(['error' => 'Gambar harus berukuran 1080x1080 pixel'], 422);
                }
                
                // Generate filename: card-{slug}.{extension}
                $slug = Str::slug($validated['title']);
                $extension = $image->getClientOriginalExtension();
                $filename = 'card-' . $slug . '.' . $extension;
                
                // Store image in base64 format in database for Vercel compatibility
                $imageContent = file_get_contents($image->getRealPath());
                $base64Image = base64_encode($imageContent);
                $mimeType = $image->getMimeType();
                
                $validated['image'] = 'data:' . $mimeType . ';base64,' . $base64Image;
            }

            // Generate slug from title
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
            $validated['user_id'] = auth()->id();

            $card = Card::create($validated);
            
            \Log::info('Card created successfully', [
                'card_id' => $card->id,
                'has_image' => !empty($card->image),
                'image_length' => $card->image ? strlen($card->image) : 0,
                'image_starts_with' => $card->image ? substr($card->image, 0, 30) : 'null'
            ]);
            
            // Return minimal response without base64 image to avoid payload too large error
            return response()->json([
                'success' => 'Card berhasil ditambahkan', 
                'card_id' => $card->id,
                'title' => $card->title
            ], 200)->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            \Log::error('Card store error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Show the specified card with image (for loading image separately to avoid payload too large)
     */
    public function show($id)
    {
        try {
            $card = Card::findOrFail($id);
            
            $authId = auth()->id();
            \Log::info('Card show() called', [
                'card_id' => $id,
                'card_user_id' => $card->user_id,
                'auth_id' => $authId,
                'is_authenticated' => auth()->check(),
                'image_length' => $card->image ? strlen($card->image) : 0
            ]);
            
            // Check if user owns this card
            if ($authId && $card->user_id !== $authId) {
                \Log::warning('Unauthorized access attempt to card', [
                    'card_id' => $id,
                    'card_user_id' => $card->user_id,
                    'auth_id' => $authId
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Make image visible for this response (normally hidden to avoid large payloads)
            $card->makeVisible('image');
            
            // Return only the card with all fields including image
            return response()->json([
                'card' => $card
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Card not found', ['card_id' => $id]);
            return response()->json(['error' => 'Card not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in show()', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
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
                    'image_url' => $card->image_url,
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
            
            \Log::info('Update - Image upload detected', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime' => $image->getMimeType()
            ]);
            
            $dimensions = getimagesize($image->path());
            \Log::info('Update - Image dimensions', ['width' => $dimensions[0], 'height' => $dimensions[1]]);
            
            if ($dimensions[0] != 1080 || $dimensions[1] != 1080) {
                return response()->json(['error' => 'Gambar harus berukuran 1080x1080 pixel'], 422);
            }
            
            // Store image in base64 format in database for Vercel compatibility
            $imageContent = file_get_contents($image->getRealPath());
            $base64Image = base64_encode($imageContent);
            $mimeType = $image->getMimeType();
            
            $validated['image'] = 'data:' . $mimeType . ';base64,' . $base64Image;
        }

        // Update slug if title changed
        if ($validated['title'] !== $card->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        $card->update($validated);
        
        \Log::info('Card updated successfully', [
            'card_id' => $card->id,
            'has_image' => !empty($card->image),
            'image_length' => $card->image ? strlen($card->image) : 0
        ]);
        
        // Return minimal response without base64 image to avoid payload too large error
        return response()->json([
            'success' => 'Card berhasil diperbarui',
            'card_id' => $card->id,
            'title' => $card->title
        ])->header('Content-Type', 'application/json');
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

        return response()->json(['success' => 'Card berhasil dihapus'])
            ->header('Content-Type', 'application/json');
    }
}
