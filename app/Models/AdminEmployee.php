<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admin_employees';

    protected $fillable = [
        'branch_id',
        'department_id',
        'team_id',
        'employee_unique_id',
        'first_name',
        'last_name',
        'designation',
        'date_of_birth',
        'gender',
        'marital_status',
        'profile_picture',
        'resume',
        'govt_id',
        'education_certificates',
        'nationality_id',
        'blood_group',
        'joining_date',
        'probation_period',
        'employment_type',
        'salary',
        'bank_account',
        'bank_name',
        'email',
        'alternative_email',
        'email_verified_at',
        'mobile',
        'alternate_mobile',
        'mobile_verified_at',
        'email_change_otp',
        'email_change_otp_expires_at',
        'email_verification_otp',
        'email_verification_otp_expires_at',
        'mobile_change_otp',
        'mobile_change_otp_expires_at',
        'mobile_verification_otp',
        'mobile_verification_otp_expires_at',
        'current_address_line1',
        'current_address_line2',
        'current_city_id',
        'current_state_id',
        'current_country_id',
        'current_postal_code',
        'same_as_current_address',
        'permanent_address_line1',
        'permanent_address_line2',
        'permanent_city_id',
        'permanent_state_id',
        'permanent_country_id',
        'permanent_postal_code',
        'account_status',
        'account_locked_until',
        'password_updated_at',
        'notification_preferences',
        'ifsc_swift_code',
        'pan_tax_id',
        'salary_frequency',
        'allowances',
        'deductions',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_number'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'email_change_otp_expires_at' => 'datetime',
        'email_verification_otp_expires_at' => 'datetime',
        'mobile_change_otp_expires_at' => 'datetime',
        'mobile_verification_otp_expires_at' => 'datetime',
        'account_locked_until' => 'datetime',
        'password_updated_at' => 'datetime',
        'notification_preferences' => 'array',
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function currentCity()
    {
        return $this->belongsTo(City::class, 'current_city_id');
    }

    public function currentState()
    {
        return $this->belongsTo(State::class, 'current_state_id');
    }

    public function currentCountry()
    {
        return $this->belongsTo(Country::class, 'current_country_id');
    }

    public function permanentCity()
    {
        return $this->belongsTo(City::class, 'permanent_city_id');
    }

    public function permanentState()
    {
        return $this->belongsTo(State::class, 'permanent_state_id');
    }

    public function permanentCountry()
    {
        return $this->belongsTo(Country::class, 'permanent_country_id');
    }

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
    public static function generateUniqueUsername($firstName, $lastName)
    {
        $name = trim($firstName . ' ' . $lastName);
        $baseUsername = Str::slug($name);
        $username = $baseUsername;
        $count = 1;

        while (self::where('username', $username)->exists()) {
            $username = "{$baseUsername}-{$count}";
            $count++;
        }

        return $username;
    }

    public static function generateUniqueEmployeeID($branch_id, $department_id, $team_id)
    {
        // Fetch Branch Details
        $team = AdminTeam::find($team_id);
        if (!$team) {
            return null; // Handle invalid branch ID
        }

        // Get Branch Unique ID
        $teamUniqueId = $team->team_unique_id;

        // Get Last Department Number for the Same Branch
        $latestEmployee = self::where('team_id', $team_id)
            ->latest('id')
            ->first();

        // Determine Next Department Number
        $employeeNumber = $latestEmployee
            ? (intval(substr($latestEmployee->employee_unique_id, strrpos($latestEmployee->employee_unique_id, '-') + 1)) + 1)
            : 1;

        // Construct Unique Department ID
        $generatedTeamID = "{$teamUniqueId}-{$employeeNumber}";

        return $generatedTeamID;
    }

    /**
     * Set the last updated details for the department.
     */
    private static function setUpdater($employee)
    {
        $authUser = self::getAuthenticatedUser();
        if ($authUser) {
            $employee->last_updated_by_id = $authUser['id'];
            $employee->last_updated_by_type = $authUser['class'];
        }
    }

    /**
     * Automatically generate a slug before creating a department
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->username)) {
                $employee->username = self::generateUniqueUsername($employee->first_name, $employee->last_name);
            }

            // Generate Unique department ID
            if (empty($employee->employee_unique_id)) {
                $employee->employee_unique_id = self::generateUniqueEmployeeID($employee->branch_id, $employee->department_id, $employee->team_id);
            }

            $authUser = self::getAuthenticatedUser();
            if ($authUser) {
                $employee->created_by_id = $authUser['id'];
                $employee->created_by_type = $authUser['class'];
            }
        });

        // Updating Event
        static::updating(function ($employee) {
            self::setUpdater($employee);
        });

        // Soft Deleting Event
        static::deleting(function ($employee) {
            if ($employee->isForceDeleting()) {
                return; // Skip if it's a permanent delete
            }
            self::setUpdater($employee);
            $employee->saveQuietly(); // Prevent infinite loop
        });

        // Restoring Event
        static::restoring(function ($employee) {
            self::setUpdater($employee);
            $employee->saveQuietly();
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
    public function scopeByAccountStatus($query, $status)
    {
        return $query->where('account_status', $status);
    }

    /**
     * Scope to filter teams by status.
     */
    public function scopeByTeamID($query, $teamID)
    {
        return $query->where('team_id', $teamID);
    }

    /**
     * Scope to filter departments by status.
     */
    public function scopeByBranchID($query, $branchID)
    {
        return $query->where('branch_id', $branchID);
    }

    /**
     * Scope to filter departments by status.
     */
    public function scopeByDepartmentID($query, $departmentID)
    {
        return $query->where('department_id', $departmentID);
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

    /** -------------------------------
     *  Custom Attributes
     * ------------------------------- */
    public function getFullNameAttribute(): string
    {
        $firstName = trim($this->first_name ?? '');
        $lastName = trim($this->last_name ?? '');

        if ($firstName && $lastName) {
            return "{$firstName} {$lastName}";
        }

        return $firstName ?: $lastName ?: 'N/A';
    }

    public function getFullCurrentAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->current_address_line1,
            $this->current_address_line2,
            optional($this->current_city)->name,
            optional($this->current_state)->name,
            optional($this->current_country)->name,
            $this->current_postal_code
        ]));
    }

    public function getFullPermanentAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->permanent_address_line1,
            $this->permanent_address_line2,
            optional($this->permanent_city)->name,
            optional($this->permanent_state)->name,
            optional($this->permanent_country)->name,
            $this->permanent_postal_code
        ]));
    }
}
