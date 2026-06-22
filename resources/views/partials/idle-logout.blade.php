@auth
    @php($idleTimeoutMinutes = (int) config('kiosk.security.idle_timeout_minutes', 30))
    @php($idleTimeoutMs = max(1, $idleTimeoutMinutes) * 60 * 1000)

    <form method="POST" action="{{ route('logout') }}" id="idleLogoutForm" class="d-none">
        @csrf
        <input type="hidden" name="idle_logout" id="idleLogoutFlag" value="1">
    </form>

    <script>
    (() => {
        const timeoutMs = {{ $idleTimeoutMs }};
        const heartbeatMs = Math.min(timeoutMs / 3, 5 * 60 * 1000);
        const activityKey = 'kiosk:last-activity';
        const logoutKey = 'kiosk:idle-logout';
        const sessionKey = @json(session()->getId());
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const activityUrl = @json(route('session.activity'));
        const loginUrl = @json(route('login'));
        const form = document.getElementById('idleLogoutForm');
        const parse = (value) => {
            try {
                return JSON.parse(value);
            } catch (error) {
                return null;
            }
        };

        let logoutSubmitted = false;
        let timerId = null;
        let lastHeartbeatAt = 0;
        let activityCooldown = false;

        const remainingIdleTime = (timestamp) => Math.max(timeoutMs - (Date.now() - timestamp), 0);

        const submitLogout = (reason = 'idle-timeout') => {
            if (logoutSubmitted || !form) {
                return;
            }

            logoutSubmitted = true;

            try {
                localStorage.setItem(logoutKey, JSON.stringify({
                    at: Date.now(),
                    reason,
                    session: sessionKey,
                }));
            } catch (error) {
                // Ignore localStorage write failures and continue logging out.
            }

            form.submit();
        };

        const scheduleLogout = (timestamp) => {
            window.clearTimeout(timerId);
            timerId = window.setTimeout(() => submitLogout(), remainingIdleTime(timestamp));
        };

        const sendHeartbeat = () => {
            if (!csrfToken || logoutSubmitted || (Date.now() - lastHeartbeatAt) < heartbeatMs) {
                return;
            }

            lastHeartbeatAt = Date.now();

            fetch(activityUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: '{}',
            }).catch(() => {
                // Silent by design. The next full request will still enforce timeout.
            });
        };

        const syncActivity = (timestamp = Date.now(), broadcast = true) => {
            if (logoutSubmitted) {
                return;
            }

            if (broadcast) {
                try {
                    localStorage.setItem(activityKey, JSON.stringify({
                        at: timestamp,
                        session: sessionKey,
                    }));
                } catch (error) {
                    // Ignore localStorage write failures.
                }
            }

            scheduleLogout(timestamp);
            sendHeartbeat();
        };

        const bootstrapActivity = () => {
            const stored = parse(localStorage.getItem(activityKey));

            if (stored?.session === sessionKey && Number.isFinite(stored.at)) {
                if (remainingIdleTime(stored.at) === 0) {
                    submitLogout();
                    return;
                }

                scheduleLogout(stored.at);
                sendHeartbeat();
                return;
            }

            syncActivity(Date.now());
        };

        const markActivity = () => {
            if (activityCooldown || logoutSubmitted) {
                return;
            }

            activityCooldown = true;
            syncActivity(Date.now());

            window.setTimeout(() => {
                activityCooldown = false;
            }, 1000);
        };

        ['click', 'keydown', 'mousemove', 'scroll', 'touchstart', 'focus'].forEach((eventName) => {
            document.addEventListener(eventName, markActivity, { passive: true });
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                markActivity();
            }
        });

        window.addEventListener('storage', (event) => {
            if (!event.newValue) {
                return;
            }

            if (event.key === activityKey) {
                const payload = parse(event.newValue);

                if (payload?.session === sessionKey && Number.isFinite(payload.at)) {
                    if (remainingIdleTime(payload.at) === 0) {
                        submitLogout();
                        return;
                    }

                    scheduleLogout(payload.at);
                }

                return;
            }

            if (event.key === logoutKey) {
                const payload = parse(event.newValue);

                if (payload?.session === sessionKey) {
                    submitLogout(payload.reason ?? 'idle-timeout');
                    window.setTimeout(() => {
                        window.location.assign(loginUrl);
                    }, 150);
                }
            }
        });

        bootstrapActivity();
    })();
    </script>
@endauth
