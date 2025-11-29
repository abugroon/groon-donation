<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountRequest extends FormRequest
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
        $bankAccountId = $this->route('bank_account')?->id ?? null;

        return [
            'account_name' => ['required', 'string', 'max:150'],
            'bank_name' => ['required', 'string', 'max:150'],
            'iban' => ['required', 'string', 'max:150', Rule::unique('bank_accounts', 'iban')->ignore($bankAccountId)],
            'account_number' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'projects' => ['nullable', 'array'],
            'projects.*' => ['exists:projects,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_name.required' => 'اسم الحساب مطلوب.',
            'account_name.string' => 'اسم الحساب يجب أن يكون نصاً.',
            'account_name.max' => 'اسم الحساب طويل جداً.',
            'bank_name.required' => 'اسم البنك مطلوب.',
            'bank_name.string' => 'اسم البنك يجب أن يكون نصاً.',
            'bank_name.max' => 'اسم البنك طويل جداً.',
            'iban.required' => 'رقم الآيبان مطلوب.',
            'iban.unique' => 'رقم الآيبان مستخدم من قبل.',
            'iban.max' => 'رقم الآيبان طويل جداً.',
            'account_number.required' => 'رقم الحساب مطلوب.',
            'account_number.string' => 'رقم الحساب يجب أن يكون نصاً.',
            'account_number.max' => 'رقم الحساب طويل جداً.',
            'status.required' => 'حالة الحساب مطلوبة.',
            'status.in' => 'حالة الحساب يجب أن تكون نشط أو غير نشط.',
            'projects.array' => 'قائمة المشاريع غير صالحة.',
            'projects.*.exists' => 'أحد المشاريع المحددة غير موجود.',
        ];
    }
}
