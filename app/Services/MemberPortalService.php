<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Association;
use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberPortalService
{
    private const ACTIVE_ASSOCIATION_SESSION_KEY = 'member.active_association_id';

    /**
     * @return Collection<int, Association>
     */
    public function associationsFor(User $user): Collection
    {
        return $user->associations()
            ->orderBy('name')
            ->get();
    }

    public function activeAssociationFor(User $user): ?Association
    {
        $associations = $this->associationsFor($user);
        if ($associations->isEmpty()) {
            session()->forget(self::ACTIVE_ASSOCIATION_SESSION_KEY);

            return null;
        }

        $requestedAssociationId = (int) session(self::ACTIVE_ASSOCIATION_SESSION_KEY, 0);
        $activeAssociation = $associations->firstWhere('id', $requestedAssociationId);

        if ($activeAssociation instanceof Association) {
            return $activeAssociation;
        }

        $defaultAssociation = $associations->first();
        if ($defaultAssociation instanceof Association) {
            session([self::ACTIVE_ASSOCIATION_SESSION_KEY => $defaultAssociation->id]);
        }

        return $defaultAssociation;
    }

    public function switchActiveAssociation(User $user, int $associationId): void
    {
        $belongsToAssociation = $user->associations()
            ->where('associations.id', $associationId)
            ->exists();

        if (! $belongsToAssociation) {
            abort(403);
        }

        session([self::ACTIVE_ASSOCIATION_SESSION_KEY => $associationId]);
    }

    /**
     * @return Collection<int, Fee>
     */
    public function activeFeesFor(User $user): Collection
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return collect();
        }

        return Fee::query()
            ->where('association_id', $activeAssociation->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function membershipForActiveAssociation(User $user): ?Pivot
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return null;
        }

        return $activeAssociation->users()
            ->where('users.id', $user->id)
            ->first()?->pivot;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function invoicesFor(User $user): array
    {
        $fees = $this->activeFeesFor($user);
        if ($fees->isEmpty()) {
            return [];
        }

        return $fees
            ->map(function (Fee $fee) use ($user): array {
                $latestPayment = Payment::query()
                    ->where('user_id', $user->id)
                    ->where('association_id', $fee->association_id)
                    ->where('fee_id', $fee->id)
                    ->latest('paid_at')
                    ->latest('id')
                    ->first();

                return [
                    'fee_name' => $fee->name,
                    'amount' => $fee->amount,
                    'frequency_label' => $fee->due_day ? 'Bulanan' : 'Tahunan',
                    'period_label' => $fee->due_day ? now()->translatedFormat('F Y') : (string) now()->year,
                    'status' => $latestPayment?->status === 'paid' ? 'selesai' : 'belum bayar',
                    'reference' => $latestPayment?->reference,
                    'paid_at' => $latestPayment?->paid_at,
                ];
            })
            ->all();
    }

    public function paymentsFor(User $user): LengthAwarePaginator
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return Payment::query()->whereRaw('1 = 0')->paginate(10);
        }

        return Payment::query()
            ->with('fee')
            ->where('association_id', $activeAssociation->id)
            ->where('user_id', $user->id)
            ->latest('paid_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function receiptsFor(User $user): LengthAwarePaginator
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return Payment::query()->whereRaw('1 = 0')->paginate(10);
        }

        return Payment::query()
            ->with('fee')
            ->where('association_id', $activeAssociation->id)
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->latest('paid_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function upcomingActivitiesFor(User $user): LengthAwarePaginator
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return Activity::query()->whereRaw('1 = 0')->paginate(10);
        }

        return Activity::query()
            ->where('association_id', $activeAssociation->id)
            ->where('activity_date', '>=', now()->startOfDay())
            ->orderBy('activity_date')
            ->paginate(10)
            ->withQueryString();
    }

    public function attendancesFor(User $user): LengthAwarePaginator
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return Attendance::query()->whereRaw('1 = 0')->paginate(10);
        }

        return Attendance::query()
            ->with('activity')
            ->where('user_id', $user->id)
            ->whereHas('activity', function ($query) use ($activeAssociation): void {
                $query->where('association_id', $activeAssociation->id);
            })
            ->latest('checked_in_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    public function announcementsFor(User $user): LengthAwarePaginator
    {
        $activeAssociation = $this->activeAssociationFor($user);
        if (! $activeAssociation instanceof Association) {
            return Announcement::query()->whereRaw('1 = 0')->paginate(10);
        }

        return Announcement::query()
            ->where('association_id', $activeAssociation->id)
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }
}
