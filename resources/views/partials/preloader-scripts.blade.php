<script>
    (function () {
        var root = document.documentElement;
        var body = document.body;
        var overlay = document.querySelector('[data-kiosk-preloader]');
        var startedAt = Date.now();
        var minimumVisibleMs = 400;

        if (!overlay) {
            return;
        }

        var settled = false;

        function finish() {
            if (settled) {
                return;
            }

            var elapsed = Date.now() - startedAt;

            if (elapsed < minimumVisibleMs) {
                window.setTimeout(finish, minimumVisibleMs - elapsed);
                return;
            }

            settled = true;
            overlay.classList.add('is-hidden');

            window.setTimeout(function () {
                overlay.remove();
                body.classList.remove('kiosk-loading');
                root.classList.remove('kiosk-loading');
            }, 420);
        }

        root.classList.add('kiosk-loading');
        body.classList.add('kiosk-loading');

        window.addEventListener('load', function () {
            window.setTimeout(finish, 120);
        }, { once: true });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                finish();
            }
        });

        window.setTimeout(finish, 8000);
    })();
</script>
