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
     * Get the user that receives the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}