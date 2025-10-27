<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'cards',
        'types',
        'category_id',
        'status',
        'user_id',
    ];

    protected $casts = [
        'types' => 'array',
    ];

    /**
     * Get the user that owns the category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cards for this category.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'category', 'cards');
    }
}
