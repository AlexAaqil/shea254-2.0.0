<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductImageController extends Controller
{
    public function delete(ProductImage $image) 
    {
        DB::beginTransaction();

        try {
            $image_path = $image->image;
    
            $image->delete();

            $full_path = $image_path;
    
            if (Storage::disk('public')->exists($full_path)) {
                Storage::disk('public')->delete($full_path);
            }

            DB::commit();
    
            session()->flash('notify', ['message' => 'product image deleted successfully', 'type' => 'success']);
        } catch (Throwable $e) {
            DB::rollback();
            
            if (app()->isLocal()) {
                dd($e->getMessage());
            }
            
            report($e);
            session()->flash('notify', ['message' => 'Failed to delete product image', 'type' => 'error']);
        }

        return redirect()->back();
    }
}
