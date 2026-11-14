<footer class="bg-gray-900 text-gray-400 mt-16">
    <div class="max-w-7xl mx-auto px-6 py-8">

        <div class="flex flex-col md:flex-row items-center justify-between gap-4">

            <p class="text-sm">
                © {{ \Morilog\Jalali\CalendarUtils::convertNumbers(\Morilog\Jalali\Jalalian::now()->getYear()) }}
                وبلاگ من. تمامی حقوق محفوظ است.
            </p>

            <div class="flex items-center gap-6 text-sm">
                <a href="#" class="hover:text-white transition">
                    حریم خصوصی
                </a>

                <a href="#" class="hover:text-white transition">
                    قوانین و مقررات
                </a>

                <a href="#" class="hover:text-white transition">
                    تماس با ما
                </a>
            </div>

        </div>

    </div>
</footer>
@stack('scripts')
