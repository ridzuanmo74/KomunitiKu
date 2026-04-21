<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommitteeStoreFeeRequest;
use App\Http\Requests\CommitteeUpdateFeeRequest;
use App\Http\Requests\UpdateAssociationMemberRequest;
use App\Models\Association;
use App\Models\Fee;
use App\Models\User;
use App\Services\AssociationMemberService;
use App\Services\CommitteeFeeService;
use App\Services\CommitteePortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommitteePortalController extends Controller
{
    public function __construct(
        private readonly CommitteePortalService $portalService,
        private readonly AssociationMemberService $associationMemberService,
        private readonly CommitteeFeeService $committeeFeeService,
    ) {}

    public function associationInfo(Request $request): View
    {
        $queryId = $request->filled('association') ? (int) $request->query('association') : null;
        $searchQuery = $request->query('q');
        $searchQuery = is_string($searchQuery) ? $searchQuery : null;
        $context = $this->portalService->associationInfoContext(
            $request->user(),
            $queryId,
            $searchQuery,
            $request->query('per_page'),
        );

        return view('committee.associations.info', $context);
    }

    public function associationMembers(Request $request): View
    {
        $queryId = $request->filled('association') ? (int) $request->query('association') : null;
        $context = $this->portalService->associationMembersPageContext($request->user(), $queryId);

        return view('committee.associations.members', $context);
    }

    public function updateAssociationMember(UpdateAssociationMemberRequest $request, User $user): RedirectResponse
    {
        $queryId = $request->filled('association') ? (int) $request->query('association') : null;
        $association = $this->portalService->associationForMembersPage($request->user(), $queryId);
        abort_unless($association instanceof Association, 404);

        $this->authorize('manageMembers', $association);

        $this->associationMemberService->updateMemberPivot($association, $user, $request->validated());

        return redirect()
            ->route('committee.associations.members', ['association' => $association->id])
            ->with('status', __('Maklumat ahli dikemas kini.'));
    }

    public function associationApprovals(Request $request): View
    {
        $association = $this->portalService->committeeContextAssociation($request->user());

        return view('committee.associations.approvals', compact('association'));
    }

    public function feeSettings(Request $request): View
    {
        $association = $this->portalService->committeeContextAssociation($request->user());
        $fees = $association ? $this->portalService->feesForAssociation($association) : collect();

        return view('committee.fees.settings', compact('association', 'fees'));
    }

    public function storeFee(CommitteeStoreFeeRequest $request): RedirectResponse
    {
        $association = $this->portalService->committeeContextAssociation($request->user());
        abort_unless($association instanceof Association, 404);

        $this->authorize('create', [Fee::class, $association->id]);
        $this->committeeFeeService->createForAssociation($association, $request->validated());

        return redirect()
            ->route('committee.fees.settings')
            ->with('status', __('Yuran berjaya didaftarkan.'));
    }

    public function updateFee(CommitteeUpdateFeeRequest $request, Fee $fee): RedirectResponse
    {
        $association = $this->portalService->committeeContextAssociation($request->user());
        abort_unless($association instanceof Association, 404);

        $this->authorize('update', $fee);
        $this->committeeFeeService->updateForAssociation($fee, $association, $request->validated());

        return redirect()
            ->route('committee.fees.settings')
            ->with('status', __('Yuran berjaya dikemaskini.'));
    }

    public function destroyFee(Request $request, Fee $fee): RedirectResponse
    {
        $association = $this->portalService->committeeContextAssociation($request->user());
        abort_unless($association instanceof Association, 404);

        $this->authorize('delete', $fee);
        $this->committeeFeeService->deleteForAssociation($fee, $association);

        return redirect()
            ->route('committee.fees.settings')
            ->with('status', __('Yuran berjaya dipadam.'));
    }

    public function generateInvoices(Request $request): View
    {
        $association = $this->portalService->committeeContextAssociation($request->user());

        return view('committee.fees.generate-invoices', compact('association'));
    }

    public function reviewPayments(Request $request): View
    {
        $association = $this->portalService->committeeContextAssociation($request->user());
        $payments = $association ? $this->portalService->recentPaymentsForAssociation($association) : collect();

        return view('committee.fees.review-payments', compact('association', 'payments'));
    }

    public function arrears(Request $request): View
    {
        $association = $this->portalService->committeeContextAssociation($request->user());
        $arrears = $association ? $this->portalService->arrearsForAssociation($association) : collect();

        return view('committee.fees.arrears', compact('association', 'arrears'));
    }
}
