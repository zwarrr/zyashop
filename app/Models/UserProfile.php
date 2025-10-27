<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'display_name',
        'bio',
        'profile_image',
        'verified_badge'
    ];

    /**
     * Relationship: UserProfile belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

