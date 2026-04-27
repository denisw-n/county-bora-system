<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'user_id',       // Links to users.id [cite: 25]
        'category',      // e.g., "Pothole", "Water Leakage" [cite: 26]
        'description',   // Citizen's message [cite: 27]
        'latitude',      // GPS north/south [cite: 28]
        'longitude',     // GPS east/west [cite: 29]
        'status',        // 'Pending', 'In Progress', 'Resolved' [cite: 30]
        'priority',      // 'Low' to 'Critical' [cite: 32]
        'dept_id',       // Links to departments.id [cite: 31]
        'admin_comment', // Official feedback [cite: 33]
        'audit_remarks', // Internal notes for accountability [cite: 152]
    ];

    /**
     * Cast coordinates to high-precision decimals for spatial accuracy.
     * Essential for the Live Incident Map[cite: 158].
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

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
     * Relationship: A report is submitted by a citizen[cite: 25].
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A report is assigned to a department for resolution[cite: 31].
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}