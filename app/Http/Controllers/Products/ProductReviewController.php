<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\Product;
use App\Models\Products\ProductReview;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function create($product)
    {
        $product = Product::where('slug', $product)->with('product_reviews')->firstOrFail();

        $alreadyReviewed = false;
        if (Auth::check()) {
            $alreadyReviewed = $product->product_reviews()
                ->where('user_id', Auth::id())
                ->exists();
        }

        return view('pages.general.product.review', compact('product', 'alreadyReviewed'));
    }

    public function store(Request $request, $product)
    {
        $product = Product::where('slug', $product)->firstOrFail();

        $alreadyReviewed = ProductReview::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyReviewed) {
            session()->flash('notify', ['message' => 'You have already reviewed this product.']);
            return redirect()->route('shop-page');
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'required|string|max:1500',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['product_id'] = $product->id;

        ProductReview::create($validated);

        session()->flash('notify', ['message' => 'Thank you! Your review has been submitted.']);
        return redirect()->route('shop-page');
    }
}
