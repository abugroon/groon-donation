<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'لوحة الإدارة' }}</title>
    <style>
        body { font-family: 'Tajawal', Arial, sans-serif; direction: rtl; text-align: right; margin: 2rem; background: #f7f7f7; }
        .card { background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #d1d5db; border-radius: 8px; }
        button { background: #2563eb; color: #fff; border: none; padding: 0.75rem 1.25rem; border-radius: 8px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; border-bottom: 1px solid #e5e7eb; }
        a { color: #2563eb; text-decoration: none; }
        .text-muted { color: #6b7280; }
        .rtl { direction: rtl; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'لوحة الإدارة' }}</h1>
    @if(session('success'))
        <div style="padding:1rem; background:#dcfce7; color:#166534; border-radius:8px; margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="padding:1rem; background:#fee2e2; color:#991b1b; border-radius:8px; margin-bottom:1rem;">
            <strong>حدثت أخطاء:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="content">
        @yield('content')
    </div>
</body>
</html>
