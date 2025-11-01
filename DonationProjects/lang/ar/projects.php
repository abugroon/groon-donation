<?php

return [
    'title' => 'المشروعات',
    'create' => 'إضافة مشروع',
    'edit' => 'تعديل مشروع',
    'details' => 'تفاصيل المشروع',
    'status' => [
        'open' => 'مفتوح',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
    ],
    'fields' => [
        'name' => 'اسم المشروع',
        'description' => 'الوصف',
        'target_amount' => 'المبلغ المطلوب',
        'collected_amount' => 'المبلغ المحصل',
        'progress' => 'نسبة الإنجاز',
        'status' => 'الحالة',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'image' => 'الصورة',
    ],
    'messages' => [
        'created' => 'تم إنشاء المشروع بنجاح.',
        'updated' => 'تم تحديث المشروع بنجاح.',
        'deleted' => 'تم حذف المشروع بنجاح.',
    ],
];
