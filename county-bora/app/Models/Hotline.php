<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotline extends Model
{
    protected $fillable = [
        'service_name', 
        'phone_number', 
        'is_active'
    ]; // [cite: 55, 56, 57]

    protected $casts = [
        'is_active' => 'boolean', // [cite: 57]
    ];
}
