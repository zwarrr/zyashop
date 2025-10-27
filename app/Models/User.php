<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User has one Profile
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Relationship: User has many Links
     */
    public function links()
    {
        return $this->hasMany(UserLink::class);
    }

    /**
     * Relationship: User has many Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relationship: User has many Cards
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Relationship: User has many Categories
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
