<?php

namespace App\Services;

use App\Models\Association;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssociationRegistryService
{
    public function create(array $data): Association
    {
        return DB::transaction(function () use ($data): Association {
            return Association::query()->create($data);
        });
    }

    public function update(Association $association, array $data): void
    {
        DB::transaction(function () use ($association, $data): void {
            $association->update($data);
        });
    }

    public function delete(Association $association): void
    {
        if ($association->users()->exists()) {
            throw ValidationException::withMessages([
                'association' => [__('Persatuan mempunyai ahli berdaftar dan tidak boleh dipadam.')],
            ]);
        }

        if ($association->fees()->exists()
            || $association->activities()->exists()
            || $association->announcements()->exists()
            || $association->payments()->exists()) {
            throw ValidationException::withMessages([
                'association' => [__('Persatuan mempunyai rekod yuran, aktiviti, pengumuman atau bayaran dan tidak boleh dipadam.')],
            ]);
        }

        DB::transaction(function () use ($association): void {
            $association->delete();
        });
    }
}
