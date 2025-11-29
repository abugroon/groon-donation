<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewDonationRequest;
use App\Models\BankAccount;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DonationReviewController extends Controller
{
    public function index(): Response
    {
        $donations = Donation::with('project')
            ->latest()
//            ->where('status', '!=',Donation::STATUS_APPROVED)
            ->paginate(100)
            ->through(fn (Donation $donation) => [
                'id' => $donation->id,
                'project' => [
                    'id' => $donation->project->id,
                    'name' => $donation->project->name,
                ],
                'donor_name' => $donation->donor_name,
                'anonymous' => (bool) $donation->anonymous,
                'amount' => (float) $donation->amount,
                'status' => $donation->status,
                'created_at' => $donation->created_at?->toIso8601String(),
                'transfer_receipt_url' => $donation->transfer_receipt
                    ? Storage::disk('public')->url($donation->transfer_receipt)
                    : null,
            ]);

        $bankAccounts = BankAccount::where('status', 'active')
            ->orderBy('account_name')
            ->get(['id', 'account_name', 'bank_name']);

        return Inertia::render('Admin/Donations/Index', [
            'donations' => $donations,
            'bankAccounts' => $bankAccounts,
            'translations' => [
                'title' => __('donations.requests_title'),
                'empty' => __('donations.requests_empty'),
                'approve' => __('donations.approve'),
                'reject' => __('donations.reject'),
                'fields' => [
                    'donor_name' => __('donations.donor_name'),
                    'amount' => __('donations.amount'),
                    'project' => __('donations.project_name'),
                    'date' => __('donations.date'),
                    'receipt' => __('donations.receipt'),
                    'status' => __('donations.status_label'),
                    'bank_account' => __('donations.bank_account'),
                ],
                'status_labels' => [
                    'pending' => __('donations.status_pending'),
                    'approved' => __('donations.status_approved'),
                    'rejected' => __('donations.status_rejected'),
                ],
                'view_full' => __('donations.view_receipt'),
                'select_bank' => __('donations.select_bank_account'),
                'anonymous' => __('donations.anonymous'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function show(Request $request, Donation $donation): Response|JsonResponse
    {
        $bankAccounts = BankAccount::where('status', 'active')
            ->orderBy('account_name')
            ->get(['id', 'account_name', 'bank_name']);

        $donationData = [
            'id' => $donation->id,
            'project' => [
                'id' => $donation->project->id,
                'name' => $donation->project->name,
            ],
            'donor_name' => $donation->donor_name,
            'amount' => (float) $donation->amount,
            'anonymous' => (bool) $donation->anonymous,
            'method' => $donation->method,
            'status' => $donation->status,
            'bank_account_id' => $donation->bank_account_id,
            'cash_description' => $donation->cash_description,
            'transfer_receipt_url' => $donation->transfer_receipt
                ? Storage::disk('public')->url($donation->transfer_receipt)
                : null,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'donation' => $donationData,
                'bank_accounts' => $bankAccounts,
            ]);
        }

        return Inertia::render('Admin/Donations/Review', [
            'donation' => $donationData,
            'bankAccounts' => $bankAccounts,
            'translations' => [
                'title' => __('donations.review_title'),
                'approve' => __('donations.approve'),
                'reject' => __('donations.reject'),
                'back_to_project' => __('donations.back_to_project'),
                'fields' => [
                    'amount' => __('donations.amount'),
                    'donor_name' => __('donations.donor_name'),
                    'method' => __('donations.payment_method'),
                    'cash_description' => __('donations.cash_description'),
                    'receipt' => __('donations.receipt'),
                    'view_receipt' => __('donations.view_receipt'),
                    'status' => __('donations.status_label'),
                    'bank_account' => __('donations.bank_account'),
                    'anonymous' => __('donations.anonymous'),
                ],
                'status_labels' => [
                    'pending' => __('donations.status_pending'),
                    'approved' => __('donations.status_approved'),
                    'rejected' => __('donations.status_rejected'),
                ],
                'methods' => [
                    'bank' => __('donations.method_bank'),
                    'cash' => __('donations.method_cash'),
                ],
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function update(ReviewDonationRequest $request, Donation $donation): RedirectResponse
    {
        DB::transaction(function () use ($request, $donation) {
            $bankAccountId = $request->input('status') === Donation::STATUS_APPROVED
                ? $request->input('bank_account_id')
                : null;

            $donation->status = $request->input('status');
            $donation->bank_account_id = $bankAccountId;
            $donation->save();

            if ($bankAccountId !== null) {
                $donation->project->bankAccounts()->syncWithoutDetaching([$bankAccountId]);
            }

            $donation->project->refreshProgressFromDonations();
        });

        if ($request->wantsJson()) {
            return response()->json(['message' => __('donations.status_updated')]);
        }

        return redirect()
            ->route('admin.projects.show', $donation->project)
            ->with('success', __('donations.status_updated'));
    }
}
