<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AssociationUser extends Pivot
{
    protected $table = 'association_user';

    public $incrementing = true;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'association_id',
        'user_id',
        'membership_no',
        'joined_at',
        'is_active',
        'address',
        'postcode',
        'city',
        'state_id',
        'latitude',
        'longitude',
        'property_relationship',
        'is_voting_eligible',
        'phone',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'is_active' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_voting_eligible' => 'boolean',
        ];
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
