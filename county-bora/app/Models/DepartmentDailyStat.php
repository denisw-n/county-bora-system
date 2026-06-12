<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DepartmentDailyStat extends Model
{
    use HasUuids;

    // UUIDs are not auto-incrementing integers
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'dept_id',
        'ward_id',
        'date',
        'resolved_count',
        'pending_count',
    ];

    /**
     * Default values for new records
     */
    protected $attributes = [
        'resolved_count' => 0,
        'pending_count' => 0,
    ];

    /**
     * Relationship to the Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }

    /**
     * Relationship to the Ward
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }
}