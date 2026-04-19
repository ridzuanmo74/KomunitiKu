<?php

namespace App\Http\Controllers;

use App\Services\CommitteePortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommitteePortalController extends Controller
{
    public function __construct(private readonly CommitteePortalService $portalService) {}

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
        $association = $this->portalService->committeeContextAssociation($request->user());
        $members = $association ? $this->portalService->membersForAssociation($association) : collect();

        return view('committee.associations.members', compact('association', 'members'));
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
