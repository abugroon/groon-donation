@extends('layouts.admin', ['title' => 'تفاصيل المشروع'])

@section('content')
    <div class="card">
        <h2>{{ $project->name }}</h2>
        <p class="text-muted">{{ $project->description }}</p>
        <p><strong>المستهدف:</strong> {{ number_format($project->target_amount, 2) }} ر.س</p>
        <p><strong>المجموع المعتمد:</strong> {{ number_format($project->collected_amount, 2) }} ر.س ({{ $project->progress }}%)</p>
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
                    <td>{{ number_format($summary->total_amount ?? 0, 2) }} ر.س</td>
                    <td>{{ $summary->donations_count ?? 0 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>أمثلة استعلام SQL</h3>
        <pre>
-- إجمالي المبالغ وعدد التبرعات لكل حساب بنكي مرتبط
SELECT bank_account_id, COUNT(*) AS donations_count, SUM(amount) AS total_amount
FROM donations
WHERE project_id = {{ $project->id }} AND status = 'approved'
GROUP BY bank_account_id;

-- تحديث حالة المشروع عند الوصول للهدف
UPDATE projects
SET collected_amount = (SELECT COALESCE(SUM(amount),0) FROM donations WHERE status='approved' AND project_id = projects.id),
    progress = ROUND(LEAST(100, (collected_amount / target_amount) * 100), 2),
    status = CASE WHEN collected_amount >= target_amount THEN 'completed' WHEN collected_amount > 0 THEN 'in_progress' ELSE 'open' END
WHERE id = {{ $project->id }};
        </pre>
    </div>
@endsection
