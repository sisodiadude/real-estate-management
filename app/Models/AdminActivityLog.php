<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class AdminActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_activity_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module',
        'action',
        'description',
        'data',
        'model_class',
        'route',
        'route_method',
        'admin_id',
        'ipv6',
        'ipv4',
        'latitude',
        'longitude',
        'internet_service_provider',
        'client_information',
        'user_agent',
        'session_id',
        'status',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the admin who performed the action.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get all logs for an admin.
     */
    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminActivityLog::class, 'admin_id');
    }

    public static function storeLog(
        Admin $admin,
        string $module,
        string $action,
        bool $status = false,
        ?string $message = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $description = null,
        ?array $data = null,
        ?string $modelClass = null
    ): ?self {
        try {
            $request = request();
            $clientDetails = getClientLocation($latitude, $longitude);

            return self::create([
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'data' => json_encode($data ?? []),
                'model_class' => $modelClass,
                'route' => $request->path(),
                'route_method' => $request->method(),
                'admin_id' => $admin->id,
                'ipv6' => $clientDetails['ip_address']['ipv6'] ?? null,
                'ipv4' => $clientDetails['ip_address']['ipv4'] ?? null,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'internet_service_provider' => $clientDetails['isp_info']['isp'] ?? null,
                'client_information' => json_encode($clientDetails),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'status' => $status,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store admin activity log: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }
}
