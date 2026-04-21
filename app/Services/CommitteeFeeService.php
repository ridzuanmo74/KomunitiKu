<?php

namespace App\Services;

use App\Models\Association;
use App\Models\Fee;

class CommitteeFeeService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function createForAssociation(Association $association, array $payload): Fee
    {
        $frequency = (string) $payload['frequency'];

        return Fee::query()->create([
            'association_id' => $association->id,
            'name' => $payload['name'],
            'amount' => $payload['amount'],
            'frequency' => $frequency,
            'due_day' => $frequency === Fee::FREQUENCY_MONTHLY ? ($payload['due_day'] ?? null) : null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function updateForAssociation(Fee $fee, Association $association, array $payload): void
    {
        if ((int) $fee->association_id !== (int) $association->id) {
            abort(403);
        }

        $frequency = (string) $payload['frequency'];

        $fee->update([
            'name' => $payload['name'],
            'amount' => $payload['amount'],
            'frequency' => $frequency,
            'due_day' => $frequency === Fee::FREQUENCY_MONTHLY ? ($payload['due_day'] ?? null) : null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);
    }

    public function deleteForAssociation(Fee $fee, Association $association): void
    {
        if ((int) $fee->association_id !== (int) $association->id) {
            abort(403);
        }

        $fee->delete();
    }
}
