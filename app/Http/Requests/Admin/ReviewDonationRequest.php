<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'bank_account_id' => [
                'required_if:status,approved',
                'nullable',
                Rule::exists('bank_accounts', 'id')
                    ->whereNull('deleted_at')
                    ->where('status', 'active'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'حالة التبرع مطلوبة.',
            'status.in' => 'يجب اختيار قبول أو رفض.',
            'bank_account_id.required_if' => 'يجب تحديد الحساب البنكي المستلم عند الموافقة.',
            'bank_account_id.exists' => 'الحساب البنكي المحدد غير موجود.',
        ];
    }
}
