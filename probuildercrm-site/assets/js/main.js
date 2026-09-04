document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('.nav-toggle');
    var links = document.querySelector('.nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.classList.toggle('open');
        });
        links.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { links.classList.remove('open'); });
        });
    }

    document.querySelectorAll('.faq-item').forEach(function (item) {
        var q = item.querySelector('.faq-q');
        if (!q) return;
        q.addEventListener('click', function () {
            var wasOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(function (o) {
                if (o !== item) o.classList.remove('open');
            });
            item.classList.toggle('open', !wasOpen);
        });
    });

    var toggleButtons = document.querySelectorAll('.plan-toggle button');
    if (toggleButtons.length) {
        toggleButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                toggleButtons.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                var cycle = btn.getAttribute('data-cycle');
                document.querySelectorAll('[data-price-monthly]').forEach(function (el) {
                    el.textContent = cycle === 'yearly' ? el.getAttribute('data-price-yearly') : el.getAttribute('data-price-monthly');
                });
                document.querySelectorAll('[data-cycle-label]').forEach(function (el) {
                    el.textContent = cycle === 'yearly' ? '/mo, billed yearly' : '/month';
                });
            });
        });
    }
});
