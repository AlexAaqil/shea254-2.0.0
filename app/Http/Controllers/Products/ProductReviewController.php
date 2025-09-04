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

        return view('pages.general.products.reviews.create', compact('product', 'alreadyReviewed'));
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

        $validated_data = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'required|string|max:1500',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated_data['user_id'] = Auth::id();
        $validated_data['product_id'] = $product->id;

        ProductReview::create($validated_data);

        session()->flash('notify', ['message' => 'Thank you! Your review has been submitted.']);
        return redirect()->route('shop-page');
    }

    public function edit(ProductReview $product_review)
    {
        $product_review->load('product', 'user');

        return view('pages.general.products.reviews.edit', compact('product_review'));
    }

    public function update(Request $request, ProductReview $product_review)
    {
        $validated_data = $request->validate([
            'ordering' => 'required|numeric|min:0',
        ]);

        $product_review->fill($validated_data);
        $product_review->is_visible = $request->boolean('is_visible');
        $product_review->save();

        session()->flash('notify', ['message' => 'Review updated successfully.']);
        return redirect()->route('product-reviews.index');
    }
}
