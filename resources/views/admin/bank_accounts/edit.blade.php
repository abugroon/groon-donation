@extends('layouts.admin', ['title' => 'تعديل حساب بنكي'])

@section('content')
    <h2>تعديل حساب بنكي</h2>
    @include('admin.bank_accounts._form', [
        'action' => route('admin.bank-accounts.update', $bankAccount),
        'method' => 'PUT',
        'bankAccount' => $bankAccount,
        'projects' => $projects,
    ])
@endsection
