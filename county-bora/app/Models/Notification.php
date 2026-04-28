<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
        'type'
    ];

    /**
     * The attributes that should be cast.
     * Ensures is_read is always treated as a boolean.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user that receives the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}