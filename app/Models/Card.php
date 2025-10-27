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
        'description',
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
