<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'jawatankuasa', 'ahli']);
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->isSuperAdmin() || $user->belongsToAssociation($activity->association_id);
    }

    public function create(User $user, int $associationId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isJawatankuasa() && $user->belongsToAssociation($associationId);
    }

    public function update(User $user, Activity $activity): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isJawatankuasa() && $user->belongsToAssociation($activity->association_id);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }
}
