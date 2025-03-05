<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminDepartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admin_departments';

    /** -------------------------------
     *  Mass Assignable Attributes
     * ------------------------------- */
    protected $fillable = [
        // Basic Information
        'branch_id',
        'department_unique_id',
        'name',
        'slug',
        'description',

        // Leadership & Management
        'leader_id',

        // Contact Information
        'email',
        'email_verified_at',
        'mobile',
        'mobile_verified_at',

        // OTP Verification
        'email_change_otp',
        'email_change_otp_expires_at',
        'mobile_change_otp',
        'mobile_change_otp_expires_at',

        // Operating Hours
        'operating_hours',

        // Department Status
        'status',

        // Audit Trail
        'created_by_id',
        'created_by_type',
        'last_updated_by_id',
        'last_updated_by_type',
    ];

    /** -------------------------------
     *  Hidden Attributes
     * ------------------------------- */
    protected $hidden = [
        'email_change_otp',
        'email_change_otp_expires_at',
        'mobile_change_otp',
        'mobile_change_otp_expires_at',
        'created_by_id',
        'created_by_type',
        'last_updated_by_id',
        'last_updated_by_type',
    ];

    /** -------------------------------
     *  Attribute Casting
     * ------------------------------- */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'email_change_otp_expires_at' => 'datetime',
        'mobile_change_otp_expires_at' => 'datetime',
        'operating_hours' => 'array',
    ];

    /**
     * Get authenticated user ID and class type.
     */
    protected static function getAuthenticatedUser()
    {
        if (Auth::guard('admin')->check()) {
            return [
                'id' => Auth::guard('admin')->id(),
                'class' => \App\Models\Admin::class,
            ];
        }

        /*
        elseif (Auth::guard('admin_employee')->check()) {
            return [
                'id' => Auth::guard('admin_employee')->id(),
                'class' => \App\Models\AdminEmployee::class,
            ];
        }
            */
        return null;
    }

    /**
     * Generate a unique slug based on the name
     */
    public static function generateUniqueSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public static function generateUniqueDepartmentID($branch_id)
    {
        // Fetch Branch Details
        $branch = AdminBranch::find($branch_id);
        if (!$branch) {
            return null; // Handle invalid branch ID
        }

        // Get Branch Unique ID
        $branchUniqueId = $branch->branch_unique_id;

        // Get Last Department Number for the Same Branch
        $latestDepartment = self::where('branch_id', $branch_id)
            ->latest('id')
            ->first();

        // Determine Next Department Number
        $departmentNumber = $latestDepartment
            ? (intval(substr($latestDepartment->department_unique_id, strrpos($latestDepartment->department_unique_id, '-') + 1)) + 1)
            : 1;

        // Construct Unique Department ID
        $generatedDepartmentID = "{$branchUniqueId}-{$departmentNumber}";

        return $generatedDepartmentID;
    }

    /**
     * Set the last updated details for the department.
     */
    private static function setUpdater($department)
    {
        $authUser = self::getAuthenticatedUser();
        if ($authUser) {
            $department->last_updated_by_id = $authUser['id'];
            $department->last_updated_by_type = $authUser['class'];
        }
    }

    /**
     * Automatically generate a slug before creating a department
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($department) {
            if (empty($department->slug)) {
                $department->slug = self::generateUniqueSlug($department->name);
            }

            // Generate Unique department ID
            if (empty($department->department_id)) {
                $department->department_unique_id = self::generateUniqueDepartmentID($department->branch_id);
            }

            $authUser = self::getAuthenticatedUser();
            if ($authUser) {
                $department->created_by_id = $authUser['id'];
                $department->created_by_type = $authUser['class'];
            }
        });

        // Updating Event
        static::updating(function ($department) {
            self::setUpdater($department);
        });

        // Soft Deleting Event
        static::deleting(function ($department) {
            if ($department->isForceDeleting()) {
                return; // Skip if it's a permanent delete
            }
            self::setUpdater($department);
            $department->saveQuietly(); // Prevent infinite loop
        });

        // Restoring Event
        static::restoring(function ($department) {
            self::setUpdater($department);
            $department->saveQuietly();
        });
    }

    /** -------------------------------
     *  Relationships
     * ------------------------------- */
    public function branch()
    {
        return $this->belongsTo(AdminBranch::class, 'branch_id');
    }

    public function leader()
    {
        return $this->belongsTo(Admin::class, 'leader_id');
    }

    /**
     * Scope to filter departments by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter departments by status.
     */
    public function scopeByBranchID($query, $branchID)
    {
        return $query->where('branch_id', $branchID);
    }

    /** -------------------------------
     *  Audit Trail Relations
     * ------------------------------- */
    public function getCreatorDetailsAttribute()
    {
        if ($this->created_by_type === Admin::class) {
            return Admin::find($this->created_by_id);
        }
        // elseif ($this->created_by_type === AdminEmployee::class) {
        //     return AdminEmployee::find($this->created_by_id);
        // }
        return null;
    }

    public function getUpdatorDetailsAttribute()
    {
        if ($this->last_updated_by_type === Admin::class) {
            return Admin::find($this->last_updated_by_id);
        }
        // elseif ($this->last_updated_by_type === AdminEmployee::class) {
        //     return AdminEmployee::find($this->last_updated_by_id);
        // }
        return null;
    }
}
