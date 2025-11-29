<div class="card">
    <form method="POST" action="{{ $action }}">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <label for="account_name">اسم الحساب</label>
        <input id="account_name" name="account_name" value="{{ old('account_name', $bankAccount->account_name ?? '') }}" required>

        <label for="bank_name">اسم البنك</label>
        <input id="bank_name" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}" required>

        <label for="iban">الآيبان</label>
        <input id="iban" name="iban" value="{{ old('iban', $bankAccount->iban ?? '') }}" required>

        <label for="account_number">رقم الحساب</label>
        <input id="account_number" name="account_number" value="{{ old('account_number', $bankAccount->account_number ?? '') }}" required>

        <label for="status">الحالة</label>
        <select id="status" name="status" required>
            <option value="active" @selected(old('status', $bankAccount->status ?? '') === 'active')>نشط</option>
            <option value="inactive" @selected(old('status', $bankAccount->status ?? '') === 'inactive')>غير نشط</option>
        </select>

        <label for="projects">المشاريع المرتبطة</label>
        <select name="projects[]" id="projects" multiple>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected(collect(old('projects', optional($bankAccount->projects)->pluck('id') ?? []))->contains($project->id))>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">حفظ</button>
    </form>
</div>
