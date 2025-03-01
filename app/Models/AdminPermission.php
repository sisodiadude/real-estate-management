<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'admin_permissions';

    // Fillable Fields
    protected $fillable = [
        'group',
        'action',
        'description',
        'is_active',
    ];

    // Casts
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
