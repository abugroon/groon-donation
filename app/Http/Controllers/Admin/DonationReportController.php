<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DonationReportController extends Controller
{
    public function index(Request $request): Response
    {
        $projectId = $request->integer('project_id');
        $bankAccountId = $request->integer('bank_account_id');

        $query = Donation::query()
            ->with(['bankAccount', 'project'])
            ->where('status', Donation::STATUS_APPROVED);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        $donations = $query
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Donation $donation) => [
                'id' => $donation->id,
                'donor_name' => $donation->donor_name,
                'anonymous' => (bool) $donation->anonymous,
                'amount' => (float) $donation->amount,
                'created_at' => $donation->created_at?->toDateTimeString(),
                'transfer_receipt_url' => $donation->transfer_receipt
                    ? Storage::disk('public')->url($donation->transfer_receipt)
                    : null,
                'bank_account' => $donation->bankAccount
                    ? [
                        'id' => $donation->bankAccount->id,
                        'account_name' => $donation->bankAccount->account_name,
                        'bank_name' => $donation->bankAccount->bank_name,
                    ]
                    : null,
                'project' => $donation->project
                    ? [
                        'id' => $donation->project->id,
                        'name' => $donation->project->name,
                    ]
                    : null,
            ]);

        $totalsQuery = Donation::query()
            ->selectRaw('bank_account_id, SUM(amount) as total_amount, COUNT(*) as total_count')
            ->where('status', Donation::STATUS_APPROVED)
            ->groupBy('bank_account_id');

        if ($projectId) {
            $totalsQuery->where('project_id', $projectId);
        }

        if ($bankAccountId) {
            $totalsQuery->where('bank_account_id', $bankAccountId);
        }

        $totals = $totalsQuery->get()->map(fn ($row) => [
            'bank_account_id' => $row->bank_account_id,
            'total_amount' => (float) $row->total_amount,
            'total_count' => (int) $row->total_count,
        ]);

        $bankAccounts = BankAccount::query()
            ->orderBy('account_name')
            ->get()
            ->map(fn (BankAccount $account) => [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'bank_name' => $account->bank_name,
            ]);

        $projects = Project::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Donations/Report', [
            'donations' => $donations,
            'totals' => $totals,
            'bankAccounts' => $bankAccounts,
            'projects' => $projects,
            'filters' => [
                'project_id' => $projectId,
                'bank_account_id' => $bankAccountId,
            ],
            'translations' => [
                'title' => 'تقرير التبرعات',
                'project_label' => 'المشروع',
                'bank_account_label' => 'الحساب البنكي',
                'all_projects' => 'كل المشاريع',
                'all_accounts' => 'كل الحسابات',
                'donor_label' => 'المتبرع',
                'amount_label' => 'المبلغ',
                'date_label' => 'التاريخ',
                'receipt_label' => 'الإيصال',
                'total_label' => 'الإجمالي',
                'count_label' => 'عدد التبرعات',
                'no_receipt' => 'لا يوجد',
                'empty' => 'لا توجد تبرعات مطابقة لهذا الفلتر',
                'unassigned_label' => 'غير محدد',
            ],
        ]);
    }
}
