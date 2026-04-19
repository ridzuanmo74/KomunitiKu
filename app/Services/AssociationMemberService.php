<?php

namespace App\Services;

use App\Models\Association;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssociationMemberService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function updateMemberPivot(Association $association, User $member, array $data): void
    {
        if (! $member->associations()->where('associations.id', $association->id)->exists()) {
            abort(404);
        }

        DB::transaction(function () use ($association, $member, $data): void {
            $association->users()->updateExistingPivot($member->id, $data);
        });
    }
}
