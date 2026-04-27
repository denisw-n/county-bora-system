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
        'user_id',       // Links to users.id
        'category',      // e.g., "Pothole", "Water Leakage"
        'description',   // Citizen's message
        'latitude',      // GPS north/south
        'longitude',     // GPS east/west
        'status',        // 'Pending', 'In Progress', 'Resolved'
        'priority',      // 'Low' to 'Critical'
        'dept_id',       // Links to departments.id
        'admin_comment', // Official feedback
        'audit_remarks', // Internal notes for accountability
    ];

    /**
     * Cast coordinates to high-precision decimals.
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * ACCESSOR: Generates the NCC Tracking Number.
     * This takes the first 8 characters of the UUID.
     * Usage: {{ $report->tracking_number }}
     */
    public function getTrackingNumberAttribute()
    {
        // Extracts the first 8 characters and converts to uppercase
        $shortId = strtoupper(substr($this->id, 0, 8));
        return "NCC-{$shortId}";
    }

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
     * Relationship: A report is submitted by a citizen.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A report is assigned to a department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}