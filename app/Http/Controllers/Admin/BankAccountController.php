<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BankAccountController extends Controller
{
    public function index(Request $request): Response|JsonResponse
    {
        $bankAccounts = BankAccount::query()
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (BankAccount $account) => [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'bank_name' => $account->bank_name,
                'iban' => $account->iban,
                'account_number' => $account->account_number,
                'status' => $account->status,
            ]);

        if ($request->wantsJson()) {
            return response()->json($bankAccounts);
        }

        return Inertia::render('Admin/BankAccounts/Index', [
            'bankAccounts' => $bankAccounts,
            'translations' => [
                'title' => __('bank_accounts.title'),
                'create' => __('bank_accounts.create'),
                'empty' => __('bank_accounts.empty'),
                'fields' => __('bank_accounts.fields'),
                'actions' => __('bank_accounts.actions'),
                'status_labels' => __('bank_accounts.status_labels'),
                'confirm_delete' => __('bank_accounts.confirm_delete'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/BankAccounts/Create', [
            'translations' => [
                'title' => __('bank_accounts.create'),
                'save' => __('bank_accounts.actions.save'),
                'cancel' => __('bank_accounts.actions.cancel'),
                'fields' => __('bank_accounts.fields'),
                'listTitle' => __('bank_accounts.title'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function store(BankAccountRequest $request): RedirectResponse
    {
        BankAccount::create($request->validated());

        return redirect()
            ->route('admin.bank-accounts.index')
            ->with('success', __('bank_accounts.created'));
    }

    public function edit(BankAccount $bank_account): Response
    {
        return Inertia::render('Admin/BankAccounts/Edit', [
            'bankAccount' => [
                'id' => $bank_account->id,
                'account_name' => $bank_account->account_name,
                'bank_name' => $bank_account->bank_name,
                'iban' => $bank_account->iban,
                'account_number' => $bank_account->account_number,
                'status' => $bank_account->status,
            ],
            'translations' => [
                'title' => __('bank_accounts.edit'),
                'save' => __('bank_accounts.actions.save'),
                'cancel' => __('bank_accounts.actions.cancel'),
                'fields' => __('bank_accounts.fields'),
                'listTitle' => __('bank_accounts.title'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function update(BankAccountRequest $request, BankAccount $bank_account): RedirectResponse
    {
        $bank_account->update($request->validated());

        return redirect()
            ->route('admin.bank-accounts.index')
            ->with('success', __('bank_accounts.updated'));
    }

    public function destroy(BankAccount $bank_account): RedirectResponse
    {
        $bank_account->delete();

        return redirect()
            ->route('admin.bank-accounts.index')
            ->with('success', __('bank_accounts.deleted'));
    }
}
