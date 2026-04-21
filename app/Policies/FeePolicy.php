<?php

namespace App\Policies;

use App\Models\Fee;
use App\Models\User;

class FeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'jawatankuasa', 'bendahari', 'ahli']);
    }

    public function view(User $user, Fee $fee): bool
    {
        return $user->isSuperAdmin() || $user->belongsToAssociation($fee->association_id);
    }

    public function create(User $user, int $associationId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canAccessCommitteeFees() && $user->belongsToAssociation($associationId);
    }

    public function update(User $user, Fee $fee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canAccessCommitteeFees() && $user->belongsToAssociation($fee->association_id);
    }

    public function delete(User $user, Fee $fee): bool
    {
        return $this->update($user, $fee);
    }
}
