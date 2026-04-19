<?php

namespace App\Services;

use App\Models\Association;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\State;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as BaseCollection;

class CommitteePortalService
{
    public const ACTIVE_ASSOCIATION_SESSION_KEY = 'committee.active_association_id';

    public function primaryAssociationFor(User $user): ?Association
    {
        $fromMembership = $user->associations()
            ->orderBy('name')
            ->first();

        if ($fromMembership instanceof Association) {
            return $fromMembership;
        }

        if ($user->isSuperAdmin()) {
            return Association::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->first();
        }

        return null;
    }

    /**
     * Persatuan semasa untuk halaman jawatankuasa (super admin ikut pilihan session).
     */
    public function committeeContextAssociation(User $user): ?Association
    {
        if ($user->isSuperAdmin()) {
            $id = (int) session(self::ACTIVE_ASSOCIATION_SESSION_KEY, 0);
            if ($id > 0 && Association::query()->whereKey($id)->exists()) {
                return Association::query()->find($id);
            }

            return $this->primaryAssociationFor($user);
        }

        return $this->primaryAssociationFor($user);
    }

    /**
     * @return array{
     *     association: ?Association,
     *     associationList: LengthAwarePaginator<int, Association>,
     *     states: BaseCollection<int, State>,
     *     canManageRegistry: bool,
     *     searchQuery: string,
     *     perPage: int
     * }
     */
    public function associationInfoContext(User $user, ?int $associationQueryId, ?string $searchQuery, mixed $perPageRaw): array
    {
        $states = State::query()->orderBy('name')->get();
        $q = $searchQuery !== null ? trim($searchQuery) : '';
        $perPage = $this->resolveAssociationListPerPage($perPageRaw);

        if ($user->isSuperAdmin()) {
            $listQuery = Association::query()
                ->with('state')
                ->orderBy('associations.name');

            $this->applyAssociationListSearch($listQuery, $q);

            /** @var LengthAwarePaginator<int, Association> $associationList */
            $associationList = $listQuery->paginate($perPage)->withQueryString();

            $selectedId = $this->resolveSuperAdminAssociationSelectionId($associationQueryId);

            $association = $selectedId !== null
                ? Association::query()->with('state')->find($selectedId)
                : null;

            return [
                'association' => $association,
                'associationList' => $associationList,
                'states' => $states,
                'canManageRegistry' => true,
                'searchQuery' => $q,
                'perPage' => $perPage,
            ];
        }

        $listQuery = $user->associations()
            ->with('state')
            ->orderBy('associations.name');

        $this->applyAssociationListSearch($listQuery, $q);

        /** @var LengthAwarePaginator<int, Association> $associationList */
        $associationList = $listQuery->paginate($perPage)->withQueryString();

        $association = $this->resolveCommitteeAssociationDetail($user, $associationQueryId);

        return [
            'association' => $association,
            'associationList' => $associationList,
            'states' => $states,
            'canManageRegistry' => false,
            'searchQuery' => $q,
            'perPage' => $perPage,
        ];
    }

    private function resolveAssociationListPerPage(mixed $perPageRaw): int
    {
        $n = is_numeric($perPageRaw) ? (int) $perPageRaw : 10;

        return in_array($n, [10, 25, 50, 100], true) ? $n : 10;
    }

    private function applyAssociationListSearch(Builder|BelongsToMany $query, string $q): void
    {
        if ($q === '') {
            return;
        }

        $term = '%'.$q.'%';

        $query->where(function (Builder $w) use ($term): void {
            $w->where('associations.name', 'like', $term)
                ->orWhere('associations.code', 'like', $term)
                ->orWhere('associations.city', 'like', $term)
                ->orWhere('associations.ros_registration_number', 'like', $term);
        });
    }

    private function resolveSuperAdminAssociationSelectionId(?int $associationQueryId): ?int
    {
        if ($associationQueryId !== null && $associationQueryId > 0
            && Association::query()->whereKey($associationQueryId)->exists()) {
            session([self::ACTIVE_ASSOCIATION_SESSION_KEY => $associationQueryId]);

            return $associationQueryId;
        }

        $sessionId = (int) session(self::ACTIVE_ASSOCIATION_SESSION_KEY, 0);
        if ($sessionId > 0 && Association::query()->whereKey($sessionId)->exists()) {
            return $sessionId;
        }

        $firstId = Association::query()->orderBy('name')->value('id');
        if ($firstId !== null) {
            session([self::ACTIVE_ASSOCIATION_SESSION_KEY => (int) $firstId]);

            return (int) $firstId;
        }

        session()->forget(self::ACTIVE_ASSOCIATION_SESSION_KEY);

        return null;
    }

    private function resolveCommitteeAssociationDetail(User $user, ?int $associationQueryId): ?Association
    {
        if ($associationQueryId !== null && $associationQueryId > 0
            && $user->belongsToAssociation($associationQueryId)) {
            return Association::query()->with('state')->find($associationQueryId);
        }

        return $this->primaryAssociationFor($user)?->load(['state']);
    }

    /**
     * @return Collection<int, User>
     */
    public function membersForAssociation(Association $association): Collection
    {
        return $association->users()
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Fee>
     */
    public function feesForAssociation(Association $association): Collection
    {
        return $association->fees()
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Payment>
     */
    public function recentPaymentsForAssociation(Association $association): Collection
    {
        return Payment::query()
            ->with(['user', 'fee'])
            ->where('association_id', $association->id)
            ->latest('paid_at')
            ->latest('id')
            ->limit(20)
            ->get();
    }

    /**
     * @return Collection<int, Payment>
     */
    public function arrearsForAssociation(Association $association): Collection
    {
        return Payment::query()
            ->with(['user', 'fee'])
            ->where('association_id', $association->id)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit(20)
            ->get();
    }
}
