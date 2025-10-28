<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'title',
        'description',
        'image_url',
        'link_shopee',
        'link_tiktok',
        'price',
        'range',
        'stock',
        'status',
        'specifications'
    ];

    /**
     * Get the full URL for the product image.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getImageUrl($value)
        );
    }

    /**
     * Process image URL value
     */
    private function getImageUrl($value)
    {
        if (!$value) {
            return null;
        }
        
        // If image is already base64 data, return it directly
        if (str_starts_with($value, 'data:image/')) {
            return $value;
        }
        
        // If already full URL, return as is
        if (str_starts_with($value, 'http') || str_starts_with($value, '/storage')) {
            return $value;
        }
        
        // Otherwise, prepend /storage/
        return asset('storage/' . $value);
    }

    /**
     * Relationship: Product belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Product belongs to Card
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}

