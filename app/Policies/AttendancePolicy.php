<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'jawatankuasa', 'ahli']);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->isSuperAdmin() || $user->belongsToAssociation($attendance->activity->association_id);
    }

    public function create(User $user, int $associationId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAhli() && $user->belongsToAssociation($associationId);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->id === $attendance->user_id
            && $user->isAhli()
            && $user->belongsToAssociation($attendance->activity->association_id);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $this->update($user, $attendance);
    }
}
