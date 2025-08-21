<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Products\ProductReview;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'user_level',
        'user_status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    static public function getAdmins()
    {
        return DB::table('users')
        ->select('users.*')
        ->where('user_level', 1)
        ->where('user_status', 1)
        ->orderByDesc('id')
        ->get();
    }

    static public function getUsers()
    {
        return DB::table('users')
        ->select('users.*')
        ->where('user_level', 2)
        ->where('user_status', 1)
        ->orderByDesc('id')
        ->get();
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function hasReviewedProduct($product_id)
    {
        return $this->productReviews()->where('product_id', $product_id)->exists();
    }
}
