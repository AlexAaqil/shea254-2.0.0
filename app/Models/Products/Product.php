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
            $original_slug = $product->getOriginal('slug');

            if ($product->isDirty('title') || empty($product->slug)) {
                $new_slug = static::generateUniqueSlug($product->title, $product->id);
                $product->slug = $new_slug;

                foreach ($product->product_images as $image) {
                    $old_filename = $image->image;

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

    protected static function generateUniqueSlug(string $title, $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
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

    public function product_images() {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('image_order', 'asc');
    }

    public function coverImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->orderBy('image_order', 'asc')->orderBy('id', 'asc');
    }

    // public function priceTiers()
    // {
    //     return $this->hasMany(ProductPriceTier::class)->orderByDesc('min_quantity');
    // }

    public function getImageUrlAttribute(): string
    {
        $image = $this->relationLoaded('coverImage')
            ? $this->coverImage
            : $this->coverImage()->first();

        if ($image && !empty($image->image)) {
            return Storage::url("{$image->image}");
        }

        return asset('assets/images/default-image.jpg');
    }

    public function getIsVisibleLabelAttribute(): string
    {
        return $this->is_visible ? 'Visible' : 'Invisible';
    }

    public function getIsFeaturedLabelAttribute(): string
    {
        return $this->featured ? 'Featured' : 'Unfeatured';
    }

    public function getCategoryTitleAttribute()
    {
        return $this->product_category?->title ?? 'uncategorized';
    }

    public function getCategorySlugAttribute(): ?string
    {
        return $this->product_category?->slug ?: null;
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->discount_price > 0 && $this->discount_price < $this->selling_price) {
            return $this->discount_price;
        }
        return $this->selling_price;
    }

    public function getEffectivePriceForQuantity(int $quantity): float
    {
        $price = $this->effective_price;

        if ($quantity > 1 && $this->relationLoaded('priceTiers')) {
            $tier = $this->priceTiers
                ->where('min_quantity', '<=', $quantity)
                ->sortByDesc('min_quantity')
                ->first();

            if ($tier && $tier->price < $price) {
                $price = $tier->price;
            }
        }

        return $price;
    }

    public function average_rating()
    {
        return $this->product_reviews()->avg('rating');
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->discount_price != 0 && $this->discount_price < $this->selling_price) {
            return (int) round(
                (($this->selling_price - $this->discount_price) / $this->selling_price) * 100
            );
        }

        return 0;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percentage > 0;
    }
}
