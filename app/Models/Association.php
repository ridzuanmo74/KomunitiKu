<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Association extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'ros_registration_number',
        'established_date',
        'address',
        'postcode',
        'city',
        'state_id',
        'phone',
        'official_email',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'established_date' => 'date',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(AssociationUser::class)
            ->withPivot([
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
            ])
            ->withTimestamps();
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
