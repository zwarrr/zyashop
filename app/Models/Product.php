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

    // Don't hide image - we'll manage visibility at controller level
    // protected $hidden = ['image'];

    /**
     * Get the raw image from attributes directly
     * This bypasses the hidden field restriction for property access
     */
    public function getRawImageAttribute()
    {
        return $this->attributes['image'] ?? null;
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

