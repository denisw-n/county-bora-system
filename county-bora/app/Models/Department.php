<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;

    // Tells Laravel the ID is a string (UUID), not an integer
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'dept_name',
        'admin_id',
    ];

    // Automatically generates a UUID when a new department is created
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship: A department oversees many reports.
     * FIXED: Changed 'department_id' to 'dept_id' to match your schema.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'dept_id');
    }
}