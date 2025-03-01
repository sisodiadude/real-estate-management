<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLoginLog extends Model
{
    use HasFactory;
    protected $table = 'admin_login_logs';

    protected $fillable = [
        'module',
        'action',
        'admin_id',
        'username',
        'status',
        'message',
        'ipv6',
        'ipv4',
        'latitude',
        'longitude',
        'internet_service_provider',
        'client_information',
        'user_agent',
        'session_id',
        'login_method',
    ];

    protected $casts = [
        'status' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function logAttempt(
        Admin $admin,
        string $module,
        string $action,
        string $username,
        bool $status = false,
        ?string $message = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $loginMethod = null
    ): self {
        try {
            $clientDetails = getClientLocation($latitude, $longitude);
            // prArr($clientDetails, 1);
            $ipv6 = $clientDetails['ip_address']['ipv6'] ?? null;
            $ipv4 = $clientDetails['ip_address']['ipv4'] ?? null;
            $isp = $clientDetails['isp_info']['isp'] ?? null;

            return self::create([
                'module' => $module,
                'action' => $action,
                'admin_id' => $admin->id,
                'username' => $username,
                'status' => $status,
                'message' => is_array($message) ? json_encode($message) : (json_decode($message) !== null ? $message : json_encode($message)),
                'ipv6' => $ipv6,
                'ipv4' => $ipv4,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'internet_service_provider' => $isp,
                'client_information' => json_encode($clientDetails),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'login_method' => $loginMethod ?? 'email_password',
            ]);
        } catch (\Exception $e) {
            report($e); // Proper error logging instead of returning an exception
            return new self();
        }
    }
}
