<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'admin_id',
        'action',
        'target_id',
        'timestamp',
    ];

    // --- ADD THIS METHOD ---
    public function admin()
    {
        // This tells Laravel: 
        // 1. The 'admin_id' in this table belongs to the 'User' model.
        return $this->belongsTo(User::class, 'admin_id');
    }
    // -----------------------

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}