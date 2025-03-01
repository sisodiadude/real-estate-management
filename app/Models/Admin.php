<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        // Basic Information
        'username',
        'first_name',
        'last_name',
        'designation',
        'date_of_birth',
        'gender',
        'profile_picture',

        // Contact Information
        'email',
        'email_verified_at',
        'mobile',
        'mobile_verified_at',

        // OTP Verification Fields
        'email_change_otp',
        'email_change_otp_expires_at',
        'email_verification_otp',
        'email_verification_otp_expires_at',
        'mobile_change_otp',
        'mobile_change_otp_expires_at',
        'mobile_verification_otp',
        'mobile_verification_otp_expires_at',

        // Address Information
        'address_line1',
        'address_line2',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',

        // Authentication & Security
        'password',
        'is_verified',
        'is_two_factor_enabled',
        'login_otp',
        'login_otp_expires_at',
        'password_reset_otp',
        'password_reset_otp_expires_at',
        'token',
        'token_expires_at',

        // Account Management
        'account_status',
        'account_locked_until',
        'password_updated_at',
        'notification_preferences',

        // Change Tracking
        'deleted_at',
        'created_by_id',
        'updated_by_id',

        // Social Links
        'social_links',
    ];

    protected $hidden = [
        // Sensitive Information
        'password',
        'token',

        // OTP & Security Fields
        'login_otp',
        'password_reset_otp',
        'email_change_otp',
        'mobile_change_otp',
        'email_verification_otp',
        'mobile_verification_otp',
    ];

    protected $casts = [
        // Timestamp Fields
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'login_otp_expires_at' => 'datetime',
        'password_reset_otp_expires_at' => 'datetime',
        'email_change_otp_expires_at' => 'datetime',
        'mobile_change_otp_expires_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'password_updated_at' => 'datetime',
        'account_locked_until' => 'datetime',

        // JSON Fields
        'notification_preferences' => 'json',
        'social_links' => 'json',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by_id');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by_id');
    }

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

    public function scopeFindByUsername(Builder $query, string $username): Builder
    {
        return $query->where(function ($q) use ($username) {
            $q->where('email', $username)
                ->orWhere('mobile', $username)
                ->orWhere('username', $username);
        });
    }

    public function generateTwoFactorLoginCode(): void
    {
        $this->login_otp = random_int(100000, 999999);
        $this->login_otp_expires_at = now()->addMinutes(15);
        $this->save();
    }

    public function verifyTwoFactorLoginCode(string $code): bool
    {
        return $this->login_otp === $code &&
            $this->login_otp_expires_at > now();
    }

    public function resetTwoFactorLoginCode(): void
    {
        $this->login_otp = null;
        $this->login_otp_expires_at = null;
        $this->save();
    }

    public function getVerifiedDetails(): array
    {
        $verifiedDetails = [];

        if ($this->email_verified_at) {
            $verifiedDetails['email'] = $this->email;
        }

        if ($this->mobile_verified_at) {
            $verifiedDetails['mobile'] = $this->mobile;
        }

        return $verifiedDetails;
    }

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_has_permissions', 'admin_id', 'permission_id')
            ->where('is_active', true);
    }

    public function canPerform($group, $action)
    {
        return $this->permissions->contains(function ($permission) use ($group, $action) {
            return trim(strtolower($permission->group)) === trim(strtolower($group)) &&
                trim(strtolower($permission->action)) === trim(strtolower($action));
        });
    }
}
