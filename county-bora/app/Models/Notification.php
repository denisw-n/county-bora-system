<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    // Since your migration uses $table->id(), the primary key is an integer
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'user_id', // This is a UUID
        'title',
        'message',
        'is_read',
        'type'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user that receives the notification.
     * Ensure it maps to the User UUID correctly.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}