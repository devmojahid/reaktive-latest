<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle image upload from HTML editor
     */
    public function editorImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        $image = $request->file('image');
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        
        // Create directory if it doesn't exist
        $uploadDir = 'uploads/editor';
        if (!file_exists(public_path($uploadDir))) {
            mkdir(public_path($uploadDir), 0755, true);
        }
        
        $image->move(public_path($uploadDir), $filename);
        
        return response()->json([
            'url' => asset($uploadDir . '/' . $filename)
        ]);
    }
} 