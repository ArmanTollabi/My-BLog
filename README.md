<div align="center">

# MyBlog

**وبلاگ فارسی با طراحی Glassmorphism، راست‌چین و تیره‌ی بنفش**
ساخته‌شده با Laravel، Tailwind CSS و Vite

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind-v4-06B6D4?logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white)

</div>

---

## معرفی

مای بلاگ یک سیستم وبلاگ کامل با رابط فارسی است. نویسنده ها با ادیتور متن غنی مقاله مینویسند و منتشر یا زمان بندی میکنند. خواننده ها دیدگاه میگذارند و پست ها را نشان میکنند، و مدیر همه چیز را از پنل ادمین کنترل میکند.
## امکانات

### مقاله‌ها
- ادیتور متن غنی (Quill) با آپلود تصویر و ویرایش **متن جایگزین (alt)** برای هر تصویر
- پاک‌سازی HTML ورودی (HtmlSanitizer) برای جلوگیری از XSS
- **چرخه‌ی وضعیت پست:** پیش‌نویس، منتشرشده، زمان‌بندی‌شده و بایگانی‌شده
- **انتشار خودکار** پست‌های زمان‌بندی‌شده با تقویم جلالی (انتخاب روز، ساعت و دقیقه)
- slug فارسی یکتا که با ویرایش عنوان به‌روز می‌شود
- دسته‌بندی و تگ، تصویر شاخص و شمارنده‌ی بازدید
- **فهرست مطالب خودکار** (h1 تا h3) در سایدبار، با اسکرول نرم و هایلایت بخش جاری
- نوار پیشرفت مطالعه، لینک دسته‌بندی‌ها در سایدبار و breadcrumb
- SEO: عنوان و توضیحات meta، Open Graph، sitemap

### کاربران و تعامل
- ثبت‌نام، ورود و بازیابی رمز عبور
- دیدگاه‌گذاری و پاسخ تودرتو
- نشان‌کردن (Bookmark) پست‌ها
- اعلان‌ها (Notification) با تاریخ و ساعت شمسی
- پنل کاربری با پست‌های من و وضعیت هر پست

### پنل ادمین
- مدیریت پست‌ها با فیلتر وضعیت (همه، بایگانی، پیش‌نویس، منتشرشده، زمان‌بندی‌شده)
- مدیریت دیدگاه‌ها: در انتظار تایید، تایید شده، سطل زباله و **خالی‌کردن سطل زباله**
- مدیریت کاربران
- سایدبار کشویی و جمع‌شونده

### طراحی
- یک دیزاین شیشه ای (glassmorphism) و ریسپانسیو
- بدون استفاده از ردیس یا کش داده؛ کوئری‌ها مستقیم و با اسکوپ‌های الکوئنت بهینه شده‌اند

## پیش‌نیازها

| ابزار | نسخه |
|---|---|
| PHP | ‎8.5 به بالا |
| Composer | ‎2.x |
| Node.js و npm | ‎20 به بالا |
| MySQL | ‎8 به بالا (یا MariaDB معادل) |

## نصب و راه‌اندازی

```bash
# ۱) دریافت پروژه
git clone https://github.com/ArmanTollabi/My-BLog.git
cd REPO

# ۲) وابستگی‌ها
composer install
npm install

# ۳) تنظیمات محیطی
cp .env.example .env
php artisan key:generate
```

در فایل `.env` اطلاعات دیتابیس را وارد کن:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myblog
DB_USERNAME=root
DB_PASSWORD=
```

و در `config/app.php` مقدار زمان را روی تهران بگذار تا زمان‌بندی انتشار درست کار کند:

```php
'timezone' => 'Asia/Tehran',
```

سپس:

```bash
# ۴) دیتابیس و لینک storage
php artisan migrate
php artisan storage:link

# ۵) اجرا (دو ترمینال)
php artisan serve
npm run dev
```

برای ساخت نسخه‌ی نهایی فرانت‌اند از `npm run build` استفاده کن.

## انتشار خودکار پست‌های زمان‌بندی‌شده

دستور `posts:publish-scheduled` هر دقیقه اجرا می‌شود و پست‌هایی که زمانشان رسیده را منتشر می‌کند. پست زمان‌بندی‌شده حتی اگر scheduler اجرا نشود، به محض رسیدن زمانش برای بازدیدکنندگان قابل مشاهده است، ولی وضعیتش در دیتابیس فقط با اجرای scheduler به «منتشرشده» تغییر می‌کند.

**محیط توسعه (XAMPP یا لوکال):**
```bash
php artisan schedule:work
```

**سرور لینوکسی:** این خط را به cron اضافه کن:
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**ویندوز:** یک تسک در Task Scheduler بساز که هر دقیقه `php artisan schedule:run` را اجرا کند.

## ساختار مهم پروژه

```
app/
├── Console/Commands/PublishScheduledPosts.php   # انتشار خودکار
├── Enums/PostStatus.php                         # وضعیت‌های پست
├── Models/Concerns/                             # HasSlug و HasJalaliDates
├── Services/PostService.php
├── Support/                                     # JalaliDate، HtmlSanitizer
resources/
├── css/                                         # استایل هر صفحه (بدون CSS داخل blade)
├── js/                                          # post-form، post-toc، panel و ...
└── views/                                       # blade ها (بدون JS و کوئری داخل view)
```

قرارداد پروژه: هیچ CSS، JavaScript یا کوئری مستقیمی داخل فایل‌های blade نوشته نمی‌شود. استایل‌ها در `resources/css`، اسکریپت‌ها در `resources/js` و کوئری‌ها در کنترلرها، سرویس‌ها و اسکوپ‌های مدل هستند.

## ساخت مدیر (Admin)

بعد از ثبت‌نام یک کاربر، نقش او را با Tinker تغییر بده. نام ستون و مقدار نقش را مطابق ساختار جدول `users` خودت تنظیم کن:

```bash
php artisan tinker
>>> \App\Models\User::where('email', 'you@example.com')->update(['role' => 'admin']);
```

## تست و کیفیت کد

```bash
php artisan test
./vendor/bin/pint        # فرمت‌دهی کد (در صورت نصب بودن)
```

## نقشه‌ی راه

- [ ] ریدایرکت ۳۰۱ از slug های قدیمی بعد از تغییر عنوان
- [ ] جستجوی پیشرفته
