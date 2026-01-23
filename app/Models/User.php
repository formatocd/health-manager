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
        'username',
        'password',
        'role',
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

    // ...

    // 1. Personas a las que YO he dado permiso (Mis espectadores)
    // Relación: Yo soy el owner_id
    public function allowedViewers()
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'owner_id', 'viewer_id');
    }

    // 2. Personas que me han dado permiso a MÍ (A quién puedo ver)
    // Relación: Yo soy el viewer_id
    public function accessibleUsers()
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'viewer_id', 'owner_id');
    }

    // 3. Helper de Seguridad Rápida
    // Verifica si tengo derecho a ver al usuario $targetUserId
    public function canView($targetUserId)
    {
        // Puedo ver si:
        // A) Soy yo mismo
        // B) Ese usuario me ha incluido en su lista de espectadores
        return $this->id === $targetUserId ||
               $this->accessibleUsers()->where('owner_id', $targetUserId)->exists();
    }
}
