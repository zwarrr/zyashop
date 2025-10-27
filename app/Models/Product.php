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

