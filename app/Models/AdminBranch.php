<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminBranch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admin_branches';

    /** -------------------------------
     *  Mass Assignable Attributes
     * ------------------------------- */
    protected $fillable = [
        // Basic Information
        'branch_unique_id',
        'name',
        'slug',
        'description',
        'date_of_start',
        'logo',

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

        // Address & Location
        'address_line1',
        'address_line2',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',

        // Geographic Location
        'latitude',
        'longitude',

        // Tax & Compliance
        'gstin',
        'tax_details',

        // Branch Classification
        'branch_type',

        // Operating Hours
        'operating_hours',

        // SMTP Configuration
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_email',
        'smtp_from_name',
        'use_branch_smtp_credentials',

        // Social Media
        'social_links',

        // Branch Status
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
        'smtp_password', // Security reason
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
        'date_of_start' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'tax_details' => 'array',
        'operating_hours' => 'array',
        'social_links' => 'array',
        'use_branch_smtp_credentials' => 'boolean',
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

    public static function generateUniqueBranchID($countryId, $stateId, $cityId)
    {
        // Fixed Prefix
        $prefix = "BR";

        // Get Country Code (ISO Alpha-2, e.g., IN for India, US for USA)
        $country = Country::find($countryId);
        $countryCode = $country ? strtoupper($country->iso3) : 'XX'; // Default XX if not found

        // Get State Code (First 2 letters, e.g., HR for Haryana, DL for Delhi)
        $state = State::find($stateId);
        $stateCode = $state ? strtoupper(substr($state->iso2, 0, 3)) : 'XX'; // Default XX if not found

        // Get City Code (First 3 letters, e.g., DEL for Delhi, MUM for Mumbai)
        $city = City::find($cityId);
        $cityCode = $city ? strtoupper(substr($city->name, 0, 3)) : 'XXX'; // Default XXX if not found

        // Get Last Branch Number in This City
        $latestBranch = self::where('country_id', $countryId)
            ->where('state_id', $stateId)
            ->where('city_id', $cityId)
            ->latest('id')
            ->first();

        $branchNumber = $latestBranch ? (intval(substr($latestBranch->branch_id, -3)) + 1) : 1;

        // Format: 001 (Incremental Code)
        $branchCode = str_pad($branchNumber, 3, '0', STR_PAD_LEFT);

        // Combine Everything: BR-IN-HR-DEL-001
        return "{$prefix}-{$countryCode}-{$stateCode}-{$cityCode}-{$branchCode}";
    }

    /**
     * Set the last updated details for the branch.
     */
    private static function setUpdater($branch)
    {
        $authUser = self::getAuthenticatedUser();
        if ($authUser) {
            $branch->last_updated_by_id = $authUser['id'];
            $branch->last_updated_by_type = $authUser['class'];
        }
    }

    /**
     * Automatically generate a slug before creating a branch
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($branch) {
            if (empty($branch->slug)) {
                $branch->slug = self::generateUniqueSlug($branch->name);
            }

            // Generate Unique Branch ID
            if (empty($branch->branch_id)) {
                $branch->branch_unique_id = self::generateUniqueBranchID($branch->country_id, $branch->state_id, $branch->city_id);
            }

            $authUser = self::getAuthenticatedUser();
            if ($authUser) {
                $branch->created_by_id = $authUser['id'];
                $branch->created_by_type = $authUser['class'];
            }
        });

        // Updating Event
        static::updating(function ($branch) {
            self::setUpdater($branch);
        });

        // Soft Deleting Event
        static::deleting(function ($branch) {
            if ($branch->isForceDeleting()) {
                return; // Skip if it's a permanent delete
            }
            self::setUpdater($branch);
            $branch->saveQuietly(); // Prevent infinite loop
        });

        // Restoring Event
        static::restoring(function ($branch) {
            self::setUpdater($branch);
            $branch->saveQuietly();
        });
    }

    /** -------------------------------
     *  Relationships
     * ------------------------------- */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function leader()
    {
        return $this->belongsTo(Admin::class, 'leader_id');
    }

    /**
     * Scope to filter branches by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
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
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address_line1,
            $this->address_line2,
            optional($this->city)->name,
            optional($this->state)->name,
            optional($this->country)->name,
            $this->postal_code
        ]));
    }
}
