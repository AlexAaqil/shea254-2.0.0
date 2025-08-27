<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($product) {
            if(empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->title);
            }
        });

        static::updating(function ($product) {
            $original_title = $product->getOriginal('title');
            $original_slug = $product->getOriginal('slug');

            if ($product->isDirty('title') || empty($product->slug)) {
                $new_slug = static::generateUniqueSlug($product->title, $product->id);
                $product->slug = $new_slug;

                // Rename associated image files
                foreach ($product->product_images as $image) {
                    $old_filename = $image->image;

                    // Only handle files that contain the old slug
                    if (Str::startsWith($old_filename, $original_slug)) {
                        $extension = pathinfo($old_filename, PATHINFO_EXTENSION);
                        $random = Str::random(6);
                        $new_filename = $new_slug . '-' . $random . '.' . $extension;

                        $old_path = "products/{$old_filename}";
                        $new_path = "products/{$new_filename}";

                        if (Storage::disk('public')->exists($old_path)) {
                            Storage::disk('public')->move($old_path, $new_path);
                            $image->update(['image' => $new_filename]);
                        }
                    }
                }
            }
        });

        static::deleting(function ($product) {
            foreach($product->product_images as $image) {
                $path = "products/{$image->image}";

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    protected static function generateUniqueSlug(string $title, $ignore_id = null): string
    {
        $base_slug = Str::slug($title);
        $slug = $base_slug;
        $i = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignore_id, fn ($query) => $query->where('id', '!=', $ignore_id))
                ->exists()
        ) {
            $slug = $base_slug . '-' . $i++;
        }

        return $slug;
    }

    public function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'featured' => 'boolean',
        ];
    }

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

    public function product_images() {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('image_order', 'asc');
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

    public function getIsVisibleLabelAttribute(): string
    {
        return $this->is_visible ? 'Visible' : 'Invisible';
    }

    public function getIsFeaturedLabelAttribute(): string
    {
        return $this->featured ? 'Featured' : 'Not Featured';
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

    public function getImageUrlAttribute()
    {
        $image = $this->relationLoaded('product_images')
            ? $this->product_images->sortBy('image_order')->first()
            : $this->product_images()->orderBy('image_order')->first();

        if ($image && Storage::disk('public')->exists("{$image->image}")) {
            return Storage::url("{$image->image}");
        }

        // Always return a fallback instead of null
        return asset('assets/images/default-image.jpg');
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
