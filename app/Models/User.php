<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'age',
        'sex',
        'avatar',
        'password',
        'role',
        'uid',
    ];

    protected $appends = ['display_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            // generate a stable unique UID for display (A000001 for admin, U000001 for users)
            if (empty($user->uid)) {
                $prefix = ($user->role === 'admin') ? 'A' : 'U';
                $user->uid = $prefix . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                $user->saveQuietly();
            }
        });
    }

    public function getDisplayIdAttribute()
    {
        return $this->uid ?? ($this->id ? 'U' . str_pad($this->id, 6, '0', STR_PAD_LEFT) : null);
    }
}
