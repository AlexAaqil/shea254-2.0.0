<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = [];

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function measurement_unit()
    {
        return $this->belongsTo(ProductMeasurement::class, 'measurement_id');
    }

    public function product_reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function average_rating()
    {
        return $this->product_reviews()->avg('rating');
    }

    public function getProductImages() {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('image_order', 'asc');
    }

    // public function priceTiers()
    // {
    //     return $this->hasMany(ProductPriceTier::class)->orderByDesc('min_quantity');
    // }

    public function getTranslatedInStock()
    {
        return $this->in_stock == 1 ? 'Yes' : 'No';
    }

    public function getTranslatedFeatured()
    {
        return $this->featured == 1 ? 'Yes' : 'No';
    }

    public function getFirstImage() {
        $productImages = $this->getProductImages()->get();

        if ($productImages->isEmpty()) {
            return asset('assets/images/default_image.jpg');
        }

        $firstImage = $productImages->first();

        if (!$firstImage || !$firstImage->image) {
            return asset('assets/images/default_image.jpg');
        }

        $imagePath = $firstImage->image;

        // Check if the image exists in storage, otherwise return the default image path
        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::url($imagePath);
        } else {
            return asset('assets/images/default_image.jpg');
        }
    }

    public function calculateDiscount()
    {
        if ($this->discount_price != 0 && $this->discount_price < $this->selling_price) {
            // Calculate the discount percentage
            $discountPercentage = (($this->selling_price - $this->discount_price) / $this->selling_price) * 100;

            // Set the new price and percentage in the model
            $this->new_price = $this->discount_price;
            $this->discount_percentage = round($discountPercentage, 0);
        } else {
            // If no discount, set the new price as the regular price
            $this->new_price = $this->selling_price;
            $this->discount_percentage = 0;
        }

        return $this->discount_percentage;
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->discount_price > 0 && $this->discount_price < $this->selling_price) {
            return $this->discount_price;
        }
        return $this->selling_price;
    }

    public function getEffectiveDefaultPrice(): float
    {
        if ($this->discount_price > 0 && $this->discount_price < $this->selling_price) {
            return $this->discount_price;
        }
        return $this->selling_price;
    }

    public function getEffectivePriceForQuantity(int $quantity): float
    {
        // Start with base selling price
        $final_price = $this->selling_price;

        // Apply discount if available and valid
         if ($this->discount_price > 0 && $this->discount_price < $final_price) {
            $final_price = $this->discount_price;
        }

        // Check for tier prices (only if quantity > 1)
        if ($quantity > 1) {
            $tier_price = $this->priceTiers
                ->where('min_quantity', '<=', $quantity)
                ->sortByDesc('min_quantity') // Gets the best tier for this quantity
                ->first()?->price;

            if ($tier_price && $tier_price < $final_price) {
                $final_price = $tier_price;
            }
        }

        return $final_price;
    }

    public function getCategorySlugAttribute(): ?string
    {
        return $this->product_category?->slug;
    }

    public function getCategoryTitleAttribute()
    {
        return $this->product_category?->title ?? 'untitled';
    }
}
