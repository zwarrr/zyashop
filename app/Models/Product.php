<?php

namespace App\Models;

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
        'image',
        'link_shopee',
        'link_tiktok',
        'price',
        'range',
        'stock',
        'status',
        'specifications'
    ];

    protected $appends = ['image_url'];

    /**
     * Get the full URL for the product image.
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        // If image is already base64 data, return it directly
        if (str_starts_with($this->image, 'data:image/')) {
            return $this->image;
        }
        
        // If image path already starts with 'products/', use it as is
        $imagePath = str_starts_with($this->image, 'products/') 
            ? $this->image 
            : 'products/' . $this->image;
        
        return asset('storage/' . $imagePath);
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

