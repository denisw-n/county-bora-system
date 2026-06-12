<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TransparencySnapshot extends Model
{
    use HasUuids;

    // Ensures Eloquent treats the UUID correctly for relationships
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'metric_type',
        'entity_id',
        'total_count',
        'resolved_count',
        'percentage',
        'snapshot_date',
    ];

    /**
     * Define the relationship to the Department model.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'entity_id', 'id');
    }
}