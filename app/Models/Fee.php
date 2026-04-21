<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    public const FREQUENCY_ONE_TIME = 'one_time';

    public const FREQUENCY_MONTHLY = 'monthly';

    public const FREQUENCY_YEARLY = 'yearly';

    protected $fillable = [
        'association_id',
        'name',
        'amount',
        'frequency',
        'due_day',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function frequencyLabel(): string
    {
        return match ($this->frequency) {
            self::FREQUENCY_ONE_TIME => 'Sekali Bayar',
            self::FREQUENCY_MONTHLY => 'Bulanan',
            default => 'Tahunan',
        };
    }

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
