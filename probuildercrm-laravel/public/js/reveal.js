// Fades + slides each major section up into place the moment it scrolls
// into view — the hero is excluded since it's already on screen at load,
// so there's nothing to "reveal" there (and no flash-of-invisible-content
// risk if this script is ever slow to run).
document.addEventListener('DOMContentLoaded', function () {
    var targets = document.querySelectorAll('.section, .section-tight');

    if (!('IntersectionObserver' in window)) {
        targets.forEach(function (el) { el.classList.add('reveal-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

    targets.forEach(function (el) {
        el.classList.add('reveal-init');
        observer.observe(el);
    });
});
