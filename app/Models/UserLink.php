<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'url',
        'icon_type',
        'order'
    ];

    /**
     * Relationship: UserLink belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

