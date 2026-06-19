<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Request;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

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
     * Accessor for full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    /**
     * Override the default password reset notification.
     * Detects if the request is API/App based to switch between 
     * Deep Link and Web Route.
     */
    public function sendPasswordResetNotification($token)
    {
        // Detect if request is coming from an API/Mobile context
        $isApi = Request::expectsJson() || Str::startsWith(Request::path(), 'api/');

        if ($isApi) {
            // Mobile App Deep Link
            $url = "countybora://reset-password?token=$token&email={$this->email}";
        } else {
            // Web Route
            $url = route('password.reset', ['token' => $token, 'email' => $this->email]);
        }

        $notification = new ResetPasswordNotification($token);
        
        $notification->toMailUsing(function ($notifiable, $token) use ($url) {
            return (new MailMessage)
                ->subject('Reset Your Password')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', $url)
                ->line('If you did not request a password reset, no further action is required.');
        });

        $this->notify($notification);
    }

    /**
     * Boot function to handle UUID generation.
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
     * Get the public alerts created BY this user.
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