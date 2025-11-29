<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewDonationRequest;
use App\Models\BankAccount;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DonationReviewController extends Controller
{
    public function show(Donation $donation): View
    {
        $bankAccounts = BankAccount::where('status', 'active')
            ->orderBy('account_name')
            ->get();

        return view('admin.donations.review', [
            'donation' => $donation,
            'bankAccounts' => $bankAccounts,
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

        return redirect()
            ->route('admin.projects.show', $donation->project)
            ->with('success', 'تم تحديث حالة التبرع بنجاح.');
    }
}
