{{--
    Keeps the logo's on-screen size here in proportion to the "Logo size"
    choice on the marketing site's Admin > Branding page - each
    .brand-logo-img element declares its own default height via the
    --brand-logo-base-h custom property (see application-logo.blade.php),
    and this scales all of them together from one --brand-logo-scale
    value on <html>.

    Applies a cached scale immediately (no flash), then refreshes it in
    the background with a short timeout - if probuildercrm.com is slow
    or unreachable, the page just keeps using the last known (or
    default 1x) scale rather than waiting on it.
--}}
<style>
    .brand-logo-img {
        height: calc(var(--brand-logo-base-h, 2.75rem) * var(--brand-logo-scale, 1));
        width: auto;
        display: block;
    }
</style>
<script>
    (function () {
        var BASELINE_PX = 44; // matches probuildercrm.com's default "md" logo size
        var STORAGE_KEY = 'brandLogoScale';
        var SIZE_URL = @json(config('app.brand_logo_size_url'));

        function applyScale(scale) {
            if (scale > 0) {
                document.documentElement.style.setProperty('--brand-logo-scale', scale);
            }
        }

        var cached = parseFloat(localStorage.getItem(STORAGE_KEY));
        if (cached) applyScale(cached);

        if (window.fetch && SIZE_URL) {
            var controller = window.AbortController ? new AbortController() : null;
            var timeout = setTimeout(function () { controller && controller.abort(); }, 1500);

            fetch(SIZE_URL, controller ? { signal: controller.signal } : {})
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    clearTimeout(timeout);
                    var scale = Number(data.height) / BASELINE_PX;
                    applyScale(scale);
                    localStorage.setItem(STORAGE_KEY, scale);
                })
                .catch(function () { clearTimeout(timeout); });
        }
    })();
</script>
