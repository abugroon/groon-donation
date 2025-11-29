@extends('layouts.admin', ['title' => 'مراجعة التبرع'])

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
    <div class="card">
        <h2>مراجعة التبرع رقم {{ $donation->id }}</h2>
        <p><strong>المشروع:</strong> {{ $donation->project->name }}</p>
        <p><strong>اسم المتبرع:</strong> {{ $donation->anonymous ? 'متبرع مجهول' : ($donation->donor_name ?: 'غير متوفر') }}</p>
        <p><strong>المبلغ:</strong> {{ number_format($donation->amount, 2) }} SD</p>
        <p><strong>طريقة التبرع:</strong> {{ $donation->method === 'bank' ? 'بنك' : 'نقد' }}</p>
        @if($donation->method === 'cash')
            <p><strong>وصف التبرع النقدي:</strong> {{ $donation->cash_description }}</p>
        @endif
        <p><strong>إيصال التحويل:</strong> <a href="{{ Storage::url($donation->transfer_receipt) }}" target="_blank">عرض الإيصال</a></p>

        <form method="POST" action="{{ route('admin.donations.updateStatus', $donation) }}">
            @csrf
            @method('PUT')
            <label for="status">نتيجة المراجعة</label>
            <select name="status" id="status" required>
                <option value="approved" @selected($donation->status === 'approved')>موافقة</option>
                <option value="rejected" @selected($donation->status === 'rejected')>رفض</option>
            </select>

            <label for="bank_account_id">الحساب البنكي المستلم</label>
            <select name="bank_account_id" id="bank_account_id">
                <option value="">اختر حساباً</option>
                @foreach($bankAccounts as $account)
                    <option value="{{ $account->id }}" @selected($donation->bank_account_id === $account->id)>
                        {{ $account->account_name }} - {{ $account->bank_name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">حفظ القرار</button>
        </form>
    </div>
@endsection
