<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'jawatankuasa', 'pengerusi', 'setiausaha', 'ahli']);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->isSuperAdmin() || $user->belongsToAssociation($announcement->association_id);
    }

    public function create(User $user, int $associationId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canManageCommitteeAnnouncements() && $user->belongsToAssociation($associationId);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canManageCommitteeAnnouncements() && $user->belongsToAssociation($announcement->association_id);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->update($user, $announcement);
    }
}
