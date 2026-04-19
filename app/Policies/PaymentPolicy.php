<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'jawatankuasa', 'ahli']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->isSuperAdmin() || $user->id === $payment->user_id;
    }

    public function create(User $user, int $associationId): bool
    {
        return $user->isAhli() && $user->belongsToAssociation($associationId);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->isSuperAdmin();
    }
}
