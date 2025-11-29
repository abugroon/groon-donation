@extends('layouts.admin', ['title' => 'تفاصيل المشروع'])

@section('content')
    <div class="card">
        <h2>{{ $project->name }}</h2>
        <p class="text-muted">{{ $project->description }}</p>
        <p><strong>المستهدف:</strong> {{ number_format($project->target_amount, 2) }} SD</p>
        <p><strong>المجموع المعتمد:</strong> {{ number_format($project->collected_amount, 2) }} SD ({{ $project->progress }}%)</p>
        <p><strong>الحالة:</strong> {{ $project->status === 'completed' ? 'مكتمل' : ($project->status === 'in_progress' ? 'قيد التنفيذ' : 'مفتوح') }}</p>
    </div>

    <div class="card">
        <h3>ملخص حسابات المشروع</h3>
        <table>
            <thead>
            <tr>
                <th>الحساب البنكي</th>
                <th>إجمالي المبالغ</th>
                <th>عدد التبرعات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($project->bankAccounts as $account)
                @php
                    $summary = $accountSummaries->firstWhere('bank_account_id', $account->id);
                @endphp
                <tr>
                    <td>{{ $account->account_name }} ({{ $account->bank_name }})</td>
                    <td>{{ number_format($summary->total_amount ?? 0, 2) }} SD</td>
                    <td>{{ $summary->donations_count ?? 0 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
