if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
    backToTop.classList.remove('hidden');
    backToTop.classList.add('flex');
} else {
    backToTop.classList.add('hidden');
    backToTop.classList.remove('flex');
}
