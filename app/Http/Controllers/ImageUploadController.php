<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        try {
            $file = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Store in public/uploads/images directory
            $path = $file->storeAs('uploads/images', $filename, 'public');
            
            // Get the full URL
            $url = Storage::disk('public')->url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }
}