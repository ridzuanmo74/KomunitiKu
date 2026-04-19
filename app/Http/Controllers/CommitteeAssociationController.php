<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssociationRequest;
use App\Http\Requests\UpdateAssociationRequest;
use App\Models\Association;
use App\Models\State;
use App\Services\AssociationRegistryService;
use App\Services\CommitteePortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CommitteeAssociationController extends Controller
{
    public function __construct(
        private readonly AssociationRegistryService $registry
    ) {}

    public function create(): View
    {
        $this->authorize('create', Association::class);

        $states = State::query()->orderBy('name')->get();

        return view('committee.associations.create', compact('states'));
    }

    public function store(StoreAssociationRequest $request): RedirectResponse
    {
        $association = $this->registry->create($request->validated());

        return redirect()->route('committee.associations.info', ['association' => $association->id])
            ->with('status', __('Persatuan telah didaftarkan.'));
    }

    public function edit(Association $association): View
    {
        $this->authorize('update', $association);

        $states = State::query()->orderBy('name')->get();

        return view('committee.associations.edit', compact('association', 'states'));
    }

    public function update(UpdateAssociationRequest $request, Association $association): RedirectResponse
    {
        $this->registry->update($association, $request->validated());

        return redirect()->route('committee.associations.info', ['association' => $association->id])
            ->with('status', __('Maklumat persatuan telah dikemaskini.'));
    }

    public function destroy(Association $association): RedirectResponse
    {
        $this->authorize('delete', $association);

        try {
            $this->registry->delete($association);
        } catch (ValidationException $e) {
            return redirect()->route('committee.associations.info', ['association' => $association->id])
                ->withErrors($e->errors());
        }

        session()->forget(CommitteePortalService::ACTIVE_ASSOCIATION_SESSION_KEY);

        return redirect()->route('committee.associations.info')
            ->with('status', __('Persatuan telah dipadam.'));
    }
}
