// کامنت‌ها: ویرایش درجا و فرم پاسخ. دکمه‌ها: <button data-comment-action="edit|cancel|reply" data-id="..">
function toggleEdit(id, editing) {
    document.getElementById('comment-view-' + id)?.classList.toggle('hidden', editing);
    document.getElementById('comment-actions-' + id)?.classList.toggle('hidden', editing);
    document.getElementById('comment-edit-' + id)?.classList.toggle('hidden', !editing);
}

document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-comment-action]');
    if (!btn) return;

    const id = btn.dataset.id;
    switch (btn.dataset.commentAction) {
        case 'edit':   toggleEdit(id, true); break;
        case 'cancel': toggleEdit(id, false); break;
        case 'reply':  document.getElementById('reply-form-' + id)?.classList.toggle('hidden'); break;
    }
});
