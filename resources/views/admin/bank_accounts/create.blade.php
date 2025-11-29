@extends('layouts.admin', ['title' => 'إضافة حساب بنكي'])

@section('content')
    <h2>إضافة حساب بنكي جديد</h2>
    @include('admin.bank_accounts._form', [
        'action' => route('admin.bank-accounts.store'),
        'method' => 'POST',
        'bankAccount' => new \App\Models\BankAccount(),
        'projects' => $projects,
    ])
@endsection
