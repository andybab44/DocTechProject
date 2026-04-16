<?php

namespace App\Models;

use App\Enums\Module;
use Database\Factories\LicenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    /** @use HasFactory<LicenseFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expires_at',
        'is_active',
        'modules',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active'  => 'boolean',
            'modules'    => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return $this->is_active
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function hasModule(Module $module): bool
    {
        return in_array($module->value, $this->modules ?? []);
    }
}
