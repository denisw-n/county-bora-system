<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Invite extends Model
{
    use HasUuids; // This handles your ID automatically

    protected $fillable = ['email', 'token', 'role', 'expires_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invite) {
            // Explicitly force these values
            $invite->token = Str::random(64);
            $invite->expires_at = now()->addDays(7);
        });
    }
}