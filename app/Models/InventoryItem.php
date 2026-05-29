<?php

namespace App\Models;

use Database\Factories\InventoryItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    /** @use HasFactory<InventoryItemFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'unit',
        'category',
        'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'quantity'            => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(InventoryUsage::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }
}
