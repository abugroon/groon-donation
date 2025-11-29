<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BankAccountRequest;
use App\Models\BankAccount;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(): View
    {
        $bankAccounts = BankAccount::with('projects')->latest()->paginate(10);

        return view('admin.bank_accounts.index', compact('bankAccounts'));
    }

    public function create(): View
    {
        $projects = Project::orderBy('name')->get();

        return view('admin.bank_accounts.create', compact('projects'));
    }

    public function store(BankAccountRequest $request): RedirectResponse
    {
        $bankAccount = BankAccount::create($request->validated());
        $bankAccount->projects()->sync($request->input('projects', []));

        return redirect()->route('admin.bank-accounts.index')->with('success', 'تم إنشاء الحساب البنكي بنجاح.');
    }

    public function edit(BankAccount $bank_account): View
    {
        $projects = Project::orderBy('name')->get();

        return view('admin.bank_accounts.edit', [
            'bankAccount' => $bank_account,
            'projects' => $projects,
        ]);
    }

    public function update(BankAccountRequest $request, BankAccount $bank_account): RedirectResponse
    {
        $bank_account->update($request->validated());
        $bank_account->projects()->sync($request->input('projects', []));

        return redirect()->route('admin.bank-accounts.index')->with('success', 'تم تحديث الحساب البنكي بنجاح.');
    }

    public function destroy(BankAccount $bank_account): RedirectResponse
    {
        $bank_account->delete();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'تم حذف الحساب البنكي (حذف ناعم).');
    }
}
