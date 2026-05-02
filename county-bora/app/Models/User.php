<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Disable auto-incrementing since we use UUIDs.
     */
    public $incrementing = false;

    /**
     * Set the key type to string for UUID compatibility.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     * Includes the new national_id_image_url for admin verification.
     */
    protected $fillable = [
        'id', 
        'first_name', 
        'middle_name', 
        'last_name', 
        'email', 
        'password', 
        'national_id', 
        'national_id_image_url', 
        'phone_number', 
        'ward_id', 
        'role', 
        'is_verified'
    ];

    /**
     * Boot function to handle UUID generation on creation.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship: The Primary Ward the user resides in.
     * Essential for displaying "Upper Hill" on the Profile Card.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    /**
     * Get the personalized notifications sent TO this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }

    /**
     * Get only the unread personalized notifications.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->where('is_read', false)->latest();
    }

    /**
     * Get the public alerts created BY this user (if they are an admin).
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'author_id');
    }

    /**
     * Get the reports submitted by this user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id')->latest();
    }
}