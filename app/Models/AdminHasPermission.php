<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminHasPermission extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'admin_has_permissions';

    // Fillable Fields
    protected $fillable = [
        'permission_id',
        'admin_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship: Admin Permission
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(AdminPermission::class, 'permission_id');
    }

    /**
     * Relationship: Admin User
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Relationship: Created By (Audit Trail)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Relationship: Updated By (Audit Trail)
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
