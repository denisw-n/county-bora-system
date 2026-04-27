<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'content',
        'type',
        'author_id',
    ];

    /**
     * Relationship: Get the admin who authored the alert.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}