<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwitchAssociationRequest;
use App\Services\MemberPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberPortalController extends Controller
{
    public function __construct(private readonly MemberPortalService $portalService) {}

    public function associations(Request $request): View
    {
        $associations = $this->portalService->associationsFor($request->user());
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());

        return view('member.associations.index', compact('associations', 'activeAssociation'));
    }

    public function switchAssociation(SwitchAssociationRequest $request): RedirectResponse
    {
        $this->portalService->switchActiveAssociation(
            $request->user(),
            (int) $request->validated('association_id')
        );

        return back()->with('status', 'Persatuan aktif telah dikemaskini.');
    }

    public function membershipProfile(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $membership = $this->portalService->membershipForActiveAssociation($request->user());

        return view('member.membership.profile', compact('activeAssociation', 'membership'));
    }

    public function membershipCard(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $membership = $this->portalService->membershipForActiveAssociation($request->user());

        return view('member.membership.card', compact('activeAssociation', 'membership'));
    }

    public function membershipApplications(): View
    {
        return view('member.membership.applications');
    }

    public function fees(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $fees = $this->portalService->activeFeesFor($request->user());

        return view('member.fees.index', compact('activeAssociation', 'fees'));
    }

    public function invoices(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $invoices = $this->portalService->invoicesFor($request->user());

        return view('member.invoices.index', compact('activeAssociation', 'invoices'));
    }

    public function payments(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $payments = $this->portalService->paymentsFor($request->user());

        return view('member.payments.index', compact('activeAssociation', 'payments'));
    }

    public function receipts(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $receipts = $this->portalService->receiptsFor($request->user());

        return view('member.receipts.index', compact('activeAssociation', 'receipts'));
    }

    public function activities(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $activities = $this->portalService->upcomingActivitiesFor($request->user());

        return view('member.activities.index', compact('activeAssociation', 'activities'));
    }

    public function attendances(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $attendances = $this->portalService->attendancesFor($request->user());

        return view('member.attendances.index', compact('activeAssociation', 'attendances'));
    }

    public function announcements(Request $request): View
    {
        $activeAssociation = $this->portalService->activeAssociationFor($request->user());
        $announcements = $this->portalService->announcementsFor($request->user());

        return view('member.announcements.index', compact('activeAssociation', 'announcements'));
    }
}
