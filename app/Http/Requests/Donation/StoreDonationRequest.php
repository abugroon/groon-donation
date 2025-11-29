<?php

namespace App\Http\Requests\Donation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'donor_name' => ['nullable', 'string', 'max:100', 'required_if:anonymous,true'],
            'amount' => ['required', 'numeric', 'min:1'],
            'anonymous' => ['boolean'],
            'method' => ['required', Rule::in(['bank', 'cash'])],
            'transfer_receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'cash_description' => ['required_if:method,cash', 'nullable', 'string'],
            'bank_account_id' => [
                'required_if:method,bank',
                'nullable',
                Rule::exists('bank_accounts', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'المشروع مطلوب.',
            'project_id.exists' => 'المشروع غير موجود.',
            'donor_name.required_if' => 'اسم المتبرع مطلوب عند اختيار التبرع المجهول.',
            'donor_name.string' => 'اسم المتبرع يجب أن يكون نصاً.',
            'donor_name.max' => 'اسم المتبرع يجب ألا يتجاوز ١٠٠ حرف.',
            'amount.required' => 'المبلغ مطلوب.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً.',
            'amount.min' => 'الحد الأدنى للمبلغ هو ١.',
            'anonymous.boolean' => 'حقل الإخفاء يجب أن يكون صحيحاً أو خطأ.',
            'method.required' => 'طريقة التبرع مطلوبة.',
            'method.in' => 'طريقة التبرع يجب أن تكون بنك أو نقد.',
            'transfer_receipt.required' => 'إيصال التحويل مطلوب.',
            'transfer_receipt.file' => 'الرجاء رفع ملف صالح.',
            'transfer_receipt.mimes' => 'الملف يجب أن يكون من نوع JPG أو PNG أو PDF.',
            'cash_description.required_if' => 'وصف التبرع النقدي مطلوب.',
            'cash_description.string' => 'الوصف يجب أن يكون نصاً.',
            'bank_account_id.required_if' => 'حساب البنك المستلم مطلوب للتبرع البنكي.',
            'bank_account_id.exists' => 'حساب البنك غير موجود.',
        ];
    }
}
