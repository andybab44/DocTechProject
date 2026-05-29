<?php

namespace App\Models;

use Database\Factories\InventoryUsageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryUsage extends Model
{
    /** @use HasFactory<InventoryUsageFactory> */
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'work_job_id',
        'used_by',
        'quantity_used',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'integer',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function workJob(): BelongsTo
    {
        return $this->belongsTo(WorkJob::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
