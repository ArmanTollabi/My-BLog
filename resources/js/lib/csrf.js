/** توکن CSRF: اول از <meta name="csrf-token">، وگرنه از اولین فیلد _token صفحه (هر فرم @csrf دارد) */
export function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
}
