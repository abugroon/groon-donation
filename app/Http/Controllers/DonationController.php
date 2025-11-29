<?php

namespace App\Http\Controllers;

use App\Http\Requests\Donation\StoreDonationRequest;
use App\Http\Resources\DonationResource;
use App\Mail\PendingDonationMail;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class DonationController extends Controller
{
    /**
     * Display a listing of donations for a specific project.
     */
    public function index(Project $project): JsonResponse
    {
        $donations = $project->approvedDonations()->latest('created_at')->get();

        return DonationResource::collection($donations)->response();
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(StoreDonationRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('transfer_receipt')) {
            $data['transfer_receipt'] = $request->file('transfer_receipt')->store('receipts', 'public');
        }

        if (($data['method'] ?? null) === 'cash') {
            $data['bank_account_id'] = null;
        }

        $data['status'] = Donation::STATUS_PENDING;

        $donation = Donation::create($data);

        $adminAddress = 'moawiaabugroon@gmail.com';

        if (config('app.env') == 'production') {
            Mail::to($adminAddress)
                ->send(new PendingDonationMail($donation));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('donations.created'),
                'data' => new DonationResource($donation),
            ], 201);
        }

        return redirect()
            ->route('projects.show', $donation->project)
            ->with('success', __('donations.created'));
    }
}
