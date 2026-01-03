<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'email_verified_at',
        'username',
        'password',
        'is_active',
        'photo',
    ];

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

    public function scopeFilterDateRange($query, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
        return $query;
    }

    public function scopeFilterRole($query, $slug)
    {
        if ($slug) {
            $query->whereHas('role', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }
        return $query;
    }

    public function scopeFilterRoles($query, $slugs)
    {
        if (!empty($slugs)) {
            $query->whereHas('role', function ($q) use ($slugs) {
                $q->whereIn('slug', (array) $slugs);
            });
        }

        return $query;
    }


    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
