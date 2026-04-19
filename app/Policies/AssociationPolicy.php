<?php

namespace App\Policies;

use App\Models\Association;
use App\Models\User;

class AssociationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Association $association): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isJawatankuasa() && $user->belongsToAssociation($association->id);
    }

    public function manageMembers(User $user, Association $association): bool
    {
        return $this->view($user, $association);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Association $association): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Association $association): bool
    {
        return $user->isSuperAdmin();
    }
}
