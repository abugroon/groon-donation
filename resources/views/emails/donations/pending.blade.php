<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تبرع جديد بانتظار المراجعة</title>
</head>
<body style="font-family: 'Tajawal', Arial, sans-serif; direction: rtl; text-align: right;">
    <h2>تبرع جديد بانتظار المراجعة</h2>
    <p>تم استلام تبرع جديد ويحتاج إلى موافقتكم.</p>
    <p><strong>اسم المتبرع:</strong> {{ $donation->anonymous ? 'متبرع مجهول' : ($donation->donor_name ?: 'لم يتم إدخال الاسم') }}</p>
    <p><strong>المبلغ:</strong> {{ number_format($donation->amount, 2) }} ر.س</p>
    <p><strong>اسم المشروع:</strong> {{ $project->name }}</p>
    <p>لمراجعة التبرع يرجى زيارة الرابط التالي:</p>
    <p><a href="{{ $adminUrl }}" style="color:#2563eb">فتح لوحة الإدارة</a></p>
</body>
</html>
