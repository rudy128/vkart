<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\tempImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class TempImagesController extends Controller
{

        public function create(Request $request)
        {
            try {
                if (!$request->hasFile('image')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No image file uploaded',
                    ]);
                }

                $image = $request->file('image');
                $ext = $image->getClientOriginalExtension();
                $newName = time().'.'.$ext;

                // Ensure temp directories exist
                $tempPath = public_path('temp');
                $thumbPath = public_path('temp/thumb');
                
                if (!file_exists($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }
                if (!file_exists($thumbPath)) {
                    mkdir($thumbPath, 0755, true);
                }

                $tempImage = new tempImage();
                $tempImage->name = $newName;
                $tempImage->save();

                // Move uploaded file
                $image->move($tempPath, $newName);

                // Copy to thumb folder (without resizing for now)
                copy($tempPath.'/'.$newName, $thumbPath.'/'.$newName);

                return response()->json([
                    'status' => true,
                    'image_id' => $tempImage->id,
                    'imagePath' => asset('temp/thumb/'.$newName),
                    'message' => 'Image uploaded successfully',
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Upload failed: ' . $e->getMessage(),
                ]);
            }
        }

}
