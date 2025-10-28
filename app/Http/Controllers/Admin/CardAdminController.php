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
        // Get cards with image URLs
        $cards = auth()->user()->cards()->get()->map(function($card) {
            $cardData = $card->toArray();
            if ($card->image) {
                $cardData['image_url'] = asset('storage/' . $card->image);
            } else {
                $cardData['image_url'] = null;
            }
            return $cardData;
        });
        
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
                \Log::info('Store - Image dimensions', ['width' => $dimensions[0], 'height' => $dimensions[1]]);
                
                if ($dimensions[0] != 1080 || $dimensions[1] != 1080) {
                    return response()->json(['error' => 'Gambar harus berukuran 1080x1080 pixel'], 422);
                }
                
                // Ensure cards directory exists - use /tmp in production
                $basePath = app()->environment('production') ? '/tmp/storage' : storage_path('app/public');
                $cardsDir = $basePath . '/cards';
                if (!file_exists($cardsDir)) {
                    \Log::info('Store - Creating cards directory', ['path' => $cardsDir]);
                    if (!mkdir($cardsDir, 0755, true)) {
                        return response()->json(['error' => 'Tidak dapat membuat direktori untuk menyimpan gambar'], 500);
                    }
                }
                
                // Generate custom filename: card-{slug}.{extension}
                $slug = Str::slug($validated['title']);
                $extension = $image->getClientOriginalExtension();
                $filename = 'card-' . $slug . '.' . $extension;
                
                // Check if file exists, add counter if needed
                $counter = 1;
                $finalPath = $cardsDir . '/' . $filename;
                while (file_exists($finalPath)) {
                    $filename = 'card-' . $slug . '-' . $counter . '.' . $extension;
                    $finalPath = $cardsDir . '/' . $filename;
                    $counter++;
                }
                
                \Log::info('Store - Saving image file', [
                    'filename' => $filename, 
                    'destination' => $finalPath,
                    'tmp_file' => $image->path(),
                    'tmp_size' => filesize($image->path())
                ]);
                
                // Read file content and write directly (more reliable than move on Vercel)
                $imageContent = file_get_contents($image->path());
                if ($imageContent === false) {
                    \Log::error('Store - Failed to read uploaded file');
                    return response()->json(['error' => 'Gagal membaca file gambar'], 500);
                }
                
                $bytesWritten = file_put_contents($finalPath, $imageContent);
                if ($bytesWritten === false || $bytesWritten === 0) {
                    \Log::error('Store - Failed to write file', ['bytes' => $bytesWritten]);
                    return response()->json(['error' => 'Gagal menyimpan file gambar'], 500);
                }
                
                $path = 'cards/' . $filename;
                $fileSize = filesize($finalPath);
                
                \Log::info('Store - Image saved successfully', [
                    'path' => $path,
                    'full_path' => $finalPath,
                    'bytes_written' => $bytesWritten,
                    'final_size' => $fileSize
                ]);
                
                if ($fileSize === 0) {
                    \Log::error('Store - WARNING: File size is 0 after save!');
                    return response()->json(['error' => 'File tersimpan tapi kosong'], 500);
                }
                
                $validated['image'] = $path;
            } else {
                \Log::info('Store - No image file in request');
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
            ], 201)->header('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            \Log::error('Card store error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
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
            
            // Delete old image
            if ($card->image) {
                $basePath = app()->environment('production') ? '/tmp/storage' : storage_path('app/public');
                $oldImagePath = $basePath . '/' . $card->image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    \Log::info('Update - Deleted old image', ['path' => $card->image]);
                }
            }
            
            // Ensure cards directory exists
            $basePath = app()->environment('production') ? '/tmp/storage' : storage_path('app/public');
            $cardsDir = $basePath . '/cards';
            if (!file_exists($cardsDir)) {
                mkdir($cardsDir, 0755, true);
            }
            
            // Generate custom filename: card-{slug}.{extension}
            $slug = Str::slug($validated['title']);
            $extension = $image->getClientOriginalExtension();
            $filename = 'card-' . $slug . '.' . $extension;
            
            // Check if file exists, add counter if needed
            $counter = 1;
            $finalPath = $cardsDir . '/' . $filename;
            while (file_exists($finalPath) && $filename !== basename($card->image)) {
                $filename = 'card-' . $slug . '-' . $counter . '.' . $extension;
                $finalPath = $cardsDir . '/' . $filename;
                $counter++;
            }
            
            \Log::info('Update - Saving image file', [
                'filename' => $filename,
                'destination' => $finalPath,
                'tmp_file' => $image->path(),
                'tmp_size' => filesize($image->path())
            ]);
            
            // Read file content and write directly (more reliable than move on Vercel)
            $imageContent = file_get_contents($image->path());
            if ($imageContent === false) {
                \Log::error('Update - Failed to read uploaded file');
                return response()->json(['error' => 'Gagal membaca file gambar'], 500);
            }
            
            $bytesWritten = file_put_contents($finalPath, $imageContent);
            if ($bytesWritten === false || $bytesWritten === 0) {
                \Log::error('Update - Failed to write file', ['bytes' => $bytesWritten]);
                return response()->json(['error' => 'Gagal menyimpan file gambar'], 500);
            }
            
            $path = 'cards/' . $filename;
            $fileSize = filesize($finalPath);
            
            \Log::info('Update - Image saved successfully', [
                'path' => $path,
                'full_path' => $finalPath,
                'bytes_written' => $bytesWritten,
                'final_size' => $fileSize
            ]);
            
            if ($fileSize === 0) {
                \Log::error('Update - WARNING: File size is 0 after save!');
                return response()->json(['error' => 'File tersimpan tapi kosong'], 500);
            }
            
            $validated['image'] = $path;
        } else {
            \Log::info('Update - No image file in request');
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

        return response()->json(['success' => 'Card berhasil diperbarui', 'card' => $cardData])
            ->header('Content-Type', 'application/json');
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
