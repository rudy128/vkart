<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class productImageController extends Controller
{
    public function update(Request $request)
    {
        echo "=== IMAGE UPLOAD START ===\n";
        echo "Request method: " . $request->method() . "\n";
        echo "Has file 'image': " . ($request->hasFile('image') ? 'YES' : 'NO') . "\n";
        echo "Product ID: " . $request->product_id . "\n";
        
        try {
            if (!$request->hasFile('image')) {
                echo "ERROR: No image file in request\n";
                return response()->json([
                    'status' => false,
                    'message' => 'No image file uploaded',
                ]);
            }

            $image = $request->file('image');
            echo "Image original name: " . $image->getClientOriginalName() . "\n";
            echo "Image size: " . $image->getSize() . "\n";
            echo "Image mime type: " . $image->getMimeType() . "\n";
            
            $ext = $image->getClientOriginalExtension();
            echo "Image extension: " . $ext . "\n";
            
            // Create product image record
            echo "Creating ProductImage record...\n";
            $productImage = new ProductImage();
            $productImage->product_id = $request->product_id;
            $productImage->image = 'temp';
            $productImage->save();
            echo "ProductImage created with ID: " . $productImage->id . "\n";

            // Generate unique filename
            $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
            echo "Generated filename: " . $imageName . "\n";
            
            // Ensure directories exist
            $largePath = public_path('uploads/product/large');
            $smallPath = public_path('uploads/product/small');
            echo "Large path: " . $largePath . "\n";
            echo "Small path: " . $smallPath . "\n";
            
            if (!File::exists($largePath)) {
                echo "Creating large directory...\n";
                File::makeDirectory($largePath, 0775, true);
            }
            if (!File::exists($smallPath)) {
                echo "Creating small directory...\n";
                File::makeDirectory($smallPath, 0775, true);
            }

            // Move uploaded file to large folder
            echo "Moving file to large folder...\n";
            $image->move($largePath, $imageName);
            echo "File moved successfully\n";
            
            $largeFilePath = $largePath.'/'.$imageName;
            echo "Large file exists: " . (file_exists($largeFilePath) ? 'YES' : 'NO') . "\n";
            
            // Copy to small folder
            echo "Copying to small folder...\n";
            $copyResult = File::copy($largeFilePath, $smallPath.'/'.$imageName);
            echo "Copy result: " . ($copyResult ? 'SUCCESS' : 'FAILED') . "\n";
            
            $smallFilePath = $smallPath.'/'.$imageName;
            echo "Small file exists: " . (file_exists($smallFilePath) ? 'YES' : 'NO') . "\n";
            
            // Update database with actual filename
            echo "Updating database with filename: " . $imageName . "\n";
            $productImage->image = $imageName;
            $productImage->save();
            
            $assetPath = asset('uploads/product/small/'.$imageName);
            echo "Asset path: " . $assetPath . "\n";
            echo "=== IMAGE UPLOAD SUCCESS ===\n";

            return response()->json([
                'status' => true,
                'image_id' => $productImage->id,
                'imagePath' => $assetPath,
                'message' => 'Image uploaded successfully',
            ]);
            
        } catch (\Exception $e) {
            echo "=== IMAGE UPLOAD ERROR ===\n";
            echo "Exception: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "=== END ERROR ===\n";
            
            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $productImage = ProductImage::find($request->id);

        if(empty($productImage))
        {
            return response()->json([
                'status' => false,
                'message' => 'Image not found',
            ]);
        }

        // delete Image from Folder
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $productImage->delete();


        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully',
        ]);
    }
}
