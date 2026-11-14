{{--
    پاپ‌آپ تایید شیشه‌ای؛ جایگزین confirm() خام مرورگر.

    نصب: resources/views/components/confirm-modal.blade.php و یک‌بار قبل از </body> در layout:  <x-confirm-modal />
    منطق: resources/js/confirm-modal.js

    بدون تغییر در فرم‌های فعلی کار می‌کند: هر فرمی که onsubmit="return confirm('...')" داشته باشد
    خودکار به پاپ‌آپ تبدیل می‌شود. برای کنترل بیشتر می‌توانید این attribute ها را روی <form> بگذارید:
      data-confirm="متن پیام"          (به‌جای onsubmit)
      data-confirm-title="عنوان"
      data-confirm-ok="متن دکمه تایید"
      data-confirm-cancel="متن دکمه انصراف"
      data-confirm-variant="danger | logout | default"
--}}
<dialog id="confirm-dialog" class="confirm-dialog" aria-labelledby="confirm-title" aria-describedby="confirm-text">
    <div class="confirm-box">

        <div id="confirm-icon" class="confirm-icon" aria-hidden="true"></div>

        <h2 id="confirm-title" class="confirm-title"></h2>
        <p id="confirm-text" class="confirm-text"></p>

        <div class="confirm-actions">
            <button type="button" id="confirm-cancel" class="btn-cancel" autofocus></button>
            <button type="button" id="confirm-ok" class="btn-create"></button>
        </div>

    </div>
</dialog>

