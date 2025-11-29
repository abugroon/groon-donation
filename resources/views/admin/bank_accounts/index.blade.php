@extends('layouts.admin', ['title' => 'الحسابات البنكية'])

@section('content')
    <div class="card">
        <div style="display:flex; justify-content: space-between; align-items:center;">
            <h2>الحسابات البنكية</h2>
            <a href="{{ route('admin.bank-accounts.create') }}">إضافة حساب جديد</a>
        </div>
        <table>
            <thead>
            <tr>
                <th>اسم الحساب</th>
                <th>البنك</th>
                <th>الآيبان</th>
                <th>الحالة</th>
                <th>المشاريع</th>
                <th>إجراءات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bankAccounts as $account)
                <tr>
                    <td>{{ $account->account_name }}</td>
                    <td>{{ $account->bank_name }}</td>
                    <td>{{ $account->iban }}</td>
                    <td>{{ $account->status === 'active' ? 'نشط' : 'غير نشط' }}</td>
                    <td>
                        <span class="text-muted">{{ $account->projects->pluck('name')->join('، ') ?: 'لا يوجد' }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.bank-accounts.edit', $account) }}">تعديل</a>
                        <form action="{{ route('admin.bank-accounts.destroy', $account) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('تأكيد الحذف الناعم؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $bankAccounts->links() }}
    </div>
@endsection
