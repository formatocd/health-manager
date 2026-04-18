<?php

namespace App\Models;

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
        'username',
        'avatar',
        'password',
        'role',
    ];

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar)
            : null;
    }


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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (static::count() === 0) {
                $user->role = 'admin';
            }
        });
    }

    public function allowedViewers()
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'owner_id', 'viewer_id');
    }

    public function accessibleUsers()
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'viewer_id', 'owner_id');
    }

    public function canView($targetUserId)
    {
        return $this->id === $targetUserId ||
               $this->accessibleUsers()->where('owner_id', $targetUserId)->exists();
    }
}
