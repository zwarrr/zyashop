<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\UserLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileAdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = $user->profile;
        $links = $user->links()->orderBy('order')->get();

        return view('admin.profile', [
            'profile' => $profile,
            'links' => $links
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info('ProfileAdminController - Update request received', [
            'has_file' => $request->hasFile('profile_image'),
            'all_files' => $request->files->keys(),
            'all_input_keys' => $request->keys()
        ]);

        $validated = $request->validate([
            'username' => 'required|string|max:50|alpha_dash',
            'display_name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'verified_badge' => 'required|in:yes,no',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $profile = $user->profile;
        
        if (!$profile) {
            $profile = new UserProfile();
            $profile->user_id = $user->id;
        }

        $profile->username = $validated['username'];
        $profile->display_name = $validated['display_name'];
        $profile->bio = $validated['bio'];
        $profile->verified_badge = $validated['verified_badge'];

        // Handle image upload - store as base64 for Vercel compatibility (like products and cards)
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            
            \Log::info('Update - Profile image upload detected', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime' => $image->getMimeType(),
                'is_valid' => $image->isValid()
            ]);
            
            // Check if image is valid
            if (!$image->isValid()) {
                return response()->json(['error' => 'File gambar tidak valid'], 422);
            }
            
            // Store image in base64 format in database for Vercel compatibility
            $imageContent = file_get_contents($image->getRealPath());
            $base64Image = base64_encode($imageContent);
            $mimeType = $image->getMimeType();
            
            $profile->profile_image = 'data:' . $mimeType . ';base64,' . $base64Image;
            
            \Log::info('Update - Profile image stored as base64', [
                'image_size_bytes' => strlen($profile->profile_image),
                'mime_type' => $mimeType,
                'first_100_chars' => substr($profile->profile_image, 0, 100)
            ]);
        } else {
            \Log::info('Update - No image file provided in request');
        }

        $profile->save();

        \Log::info('Update - Profile saved successfully', [
            'profile_id' => $profile->id,
            'has_image' => !empty($profile->profile_image),
            'image_length' => strlen($profile->profile_image ?? '')
        ]);

        return response()->json(['success' => 'Profile berhasil diperbarui!'], 200);
    }

    public function storeLink(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        // Get max order
        $maxOrder = $user->links()->max('order') ?? 0;

        $link = new UserLink();
        $link->user_id = $user->id;
        $link->title = $validated['title'];
        $link->url = $validated['url'];
        $link->order = $maxOrder + 1;
        $link->save();

        return response()->json(['success' => 'Link berhasil ditambahkan!'], 200);
    }

    public function updateLink(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $link = UserLink::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $link->title = $validated['title'];
        $link->url = $validated['url'];
        $link->save();

        return response()->json(['success' => 'Link berhasil diperbarui!'], 200);
    }

    public function destroyLink($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $link = UserLink::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $link->delete();

        return response()->json(['success' => 'Link berhasil dihapus!'], 200);
    }
}
