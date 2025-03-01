<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module',
        'subject',
        'action',
        'template',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_email',
        'from_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'smtp_port' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessors
     */
    public function getSmtpConfigAttribute()
    {
        return [
            'host' => $this->smtp_host,
            'port' => $this->smtp_port,
            'username' => $this->smtp_username,
            'password' => $this->smtp_password,
            'encryption' => $this->smtp_encryption,
        ];
    }

    public function getFromAttribute()
    {
        return [
            'email' => $this->from_email,
            'name' => $this->from_name,
        ];
    }

    /**
     * Professional Scopes
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModuleAndAction($query, string $module, string $action)
    {
        return $query->where('module', $module)->where('action', $action);
    }

    public function scopeBySmtpHost($query, string $smtpHost)
    {
        return $query->where('smtp_host', $smtpHost);
    }

    public function scopeByEncryption($query, ?string $encryption)
    {
        return $query->where('smtp_encryption', $encryption);
    }

    public function scopeByFromEmail($query, string $fromEmail)
    {
        return $query->where('from_email', $fromEmail);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('module', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhere('smtp_host', 'like', "%{$search}%")
                ->orWhere('smtp_username', 'like', "%{$search}%")
                ->orWhere('from_email', 'like', "%{$search}%")
                ->orWhere('from_name', 'like', "%{$search}%");
        });
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeUpdatedWithin($query, $startDate, $endDate)
    {
        return $query->whereBetween('updated_at', [$startDate, $endDate]);
    }

    /**
     * Custom Methods
     */
    public static function createConfig(
        string $module,
        string $subject,
        string $action,
        string $template,
        string $smtpHost,
        int $smtpPort,
        string $smtpUsername,
        string $smtpPassword,
        ?string $smtpEncryption,
        string $fromEmail,
        string $fromName
    ): self {
        return self::create([
            'module' => $module,
            'subject' => $subject,
            'action' => $action,
            'template' => $template,
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
            'smtp_username' => $smtpUsername,
            'smtp_password' => $smtpPassword, // Consider encrypting this in production
            'smtp_encryption' => $smtpEncryption,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
        ]);
    }

    public function updateConfig(array $attributes): bool
    {
        return $this->update($attributes);
    }

    public function getTemplateVariables(): array
    {
        // Extract placeholders from the template (e.g., {{variable}})
        preg_match_all('/\{\{(.+?)\}\}/', $this->template, $matches);
        return array_unique($matches[1] ?? []);
    }

    public function renderTemplate(array $variables): string
    {
        $template = $this->template;
        foreach ($variables as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        return $template;
    }

    public function isValidSmtpConfig(): bool
    {
        return !empty($this->smtp_host) &&
            !empty($this->smtp_port) &&
            !empty($this->smtp_username) &&
            !empty($this->smtp_password) &&
            !empty($this->from_email) &&
            filter_var($this->from_email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
