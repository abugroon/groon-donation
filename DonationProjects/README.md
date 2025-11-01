# DonationProjects

DonationProjects هو قالب جاهز مبني باستخدام Laravel 11 وVue 3 (Inertia.js) لإدارة المشروعات الخيرية والتبرعات.

## المتطلبات

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL

## خطوات التشغيل

1. تثبيت الاعتماديات الخلفية:
   ```bash
   composer install
   ```
2. تثبيت حزم الواجهة:
   ```bash
   npm install
   ```
3. نسخ ملف البيئة:
   ```bash
   cp .env.example .env
   ```
4. إنشاء مفتاح التطبيق:
   ```bash
   php artisan key:generate
   ```
5. إعداد قاعدة البيانات وتعديل بيانات الاتصال في ملف `.env`.
6. تنفيذ الترحيلات والسييدرز:
   ```bash
   php artisan migrate --seed
   ```
7. تشغيل خادم Laravel وVite:
   ```bash
   php artisan serve
   npm run dev
   ```

## الميزات

- إدارة المشروعات (إنشاء، عرض، تحديث، حذف)
- تسجيل التبرعات مع تحديث تلقائي لنسبة التقدم
- لوحة تحكم تعرض إحصائيات عامة
- دعم لغتين (العربية والإنجليزية)
- مصادقة بسيطة (تسجيل دخول/تسجيل جديد)
- تكامل كامل مع Inertia.js وTailwindCSS

## بيانات تسجيل الدخول الافتراضية

- البريد: `admin@example.com`
- كلمة المرور: `password`
