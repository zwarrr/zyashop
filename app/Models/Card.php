<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Card extends Model
{
    protected $fillable = [
        'title',
        'category',
        'image',
        'slug',
        'status',
        'user_id',
    ];

    // Hide image from serialization by default - too large with base64
    protected $hidden = ['image'];

    // Add image_url to appends so it's automatically computed and included in JSON/array access
    protected $appends = ['image_url'];

    /**
     * Get the full URL for the card image using Attribute casting.
     * This will be accessible as $card->image_url even though image is hidden
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->image) {
                    return null;
                }
                
                // If image is already base64 data, return it directly
                if (str_starts_with($this->image, 'data:image/')) {
                    return $this->image;
                }
                
                // If image path already starts with 'cards/', use it as is
                $imagePath = str_starts_with($this->image, 'cards/') 
                    ? $this->image 
                    : 'cards/' . $this->image;
                
                return asset('storage/' . $imagePath);
            }
        );
    }

    /**
     * Get the user that owns the card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the card.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
