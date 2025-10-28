<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the full URL for the card image.
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
        
        // If image path already starts with 'cards/', use it as is
        $imagePath = str_starts_with($this->image, 'cards/') 
            ? $this->image 
            : 'cards/' . $this->image;
        
        return asset('storage/' . $imagePath);
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
