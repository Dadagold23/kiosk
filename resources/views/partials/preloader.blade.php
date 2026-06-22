<style>
    .kiosk-preloader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 24px;
        background:
            radial-gradient(circle at top left, rgba(220, 70, 70, 0.16), transparent 34%),
            radial-gradient(circle at bottom right, rgba(17, 17, 17, 0.24), transparent 28%),
            linear-gradient(135deg, rgba(24, 18, 13, 0.9) 0%, rgba(20, 20, 22, 0.85) 50%, rgba(17, 17, 17, 0.9) 100%);
        opacity: 1;
        visibility: visible;
        transition: opacity 0.4s ease, visibility 0.4s ease;
        backdrop-filter: blur(10px);
    }

    .kiosk-preloader.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .kiosk-preloader__stage {
        position: relative;
        display: grid;
        place-items: center;
        width: min(100%, 210px);
        aspect-ratio: 1;
        transition: transform 0.5s cubic-bezier(0.77, 0, 0.175, 1), opacity 0.5s cubic-bezier(0.77, 0, 0.175, 1);
    }

    .kiosk-preloader.is-hidden .kiosk-preloader__stage {
        transform: scale(0.85);
        opacity: 0;
    }

    .kiosk-preloader__glow {
        position: absolute;
        inset: 18%;
        border-radius: 50%;
        background:
            radial-gradient(circle, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.4) 42%, rgba(255, 255, 255, 0) 72%),
            conic-gradient(from 180deg, rgba(220, 70, 70, 0.18), rgba(17, 17, 17, 0.14), rgba(220, 70, 70, 0.18));
        filter: blur(10px);
        opacity: 0.82;
    }

    .kiosk-preloader__ring,
    .kiosk-preloader__ring::before,
    .kiosk-preloader__ring::after {
        position: absolute;
        inset: 0;
        border-radius: 50%;
    }

    .kiosk-preloader__ring {
        display: grid;
        place-items: center;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.94) 30%, rgba(255, 255, 255, 0.86) 54%, rgba(255, 255, 255, 0.18) 74%, transparent 75%);
        box-shadow:
            0 30px 70px rgba(15, 23, 42, 0.12),
            inset 0 0 0 1px rgba(255, 255, 255, 0.75);
        overflow: hidden;
    }

    .kiosk-preloader__ring::before,
    .kiosk-preloader__ring::after {
        content: "";
        border: 2px solid transparent;
        mix-blend-mode: multiply;
    }

    .kiosk-preloader__ring::before {
        inset: 10%;
        border-top-color: rgba(220, 70, 70, 0.88);
        border-right-color: rgba(220, 70, 70, 0.24);
        animation: kiosk-preloader-orbit 3.1s linear infinite;
    }

    .kiosk-preloader__ring::after {
        inset: 2%;
        border-bottom-color: rgba(17, 17, 17, 0.82);
        border-left-color: rgba(17, 17, 17, 0.2);
        animation: kiosk-preloader-orbit 3.9s linear infinite reverse;
    }

    .kiosk-preloader__wave {
        position: absolute;
        border-radius: 50%;
        border: 2px solid rgba(220, 70, 70, 0.2);
        transform: scale(0.7);
        opacity: 0;
        animation: kiosk-preloader-wave 3.5s ease-out infinite;
    }

    .kiosk-preloader__wave--one {
        inset: 8%;
    }

    .kiosk-preloader__wave--two {
        inset: 16%;
        animation-delay: 0.9s;
        border-color: rgba(17, 17, 17, 0.18);
    }

    .kiosk-preloader__wave--three {
        inset: 24%;
        animation-delay: 1.8s;
        border-color: rgba(220, 70, 70, 0.14);
    }

    .kiosk-preloader__center {
        position: relative;
        z-index: 2;
        width: 29%;
        aspect-ratio: 1;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        box-shadow:
            0 14px 30px rgba(15, 23, 42, 0.12),
            inset 0 0 0 1px rgba(148, 163, 184, 0.18);
    }

    .kiosk-preloader__center::after {
        content: "";
        position: absolute;
        inset: 10%;
        border-radius: 50%;
        box-shadow: inset 0 0 24px rgba(249, 115, 22, 0.08);
        pointer-events: none;
    }

    .kiosk-preloader__logo {
        width: 58%;
        height: auto;
        display: block;
    }

    .kiosk-preloader__label {
        position: absolute;
        left: 50%;
        top: -2px;
        transform: translateX(-50%);
        z-index: 3;
        align-items: center;
        border: 1px solid rgba(249, 115, 22, 0.14);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        display: inline-flex;
        justify-content: center;
        min-height: 38px;
        min-width: 118px;
        padding: 6px 14px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        white-space: nowrap;
    }

    .kiosk-preloader__label img {
        display: block;
        height: 16px;
        max-width: 94px;
        width: auto;
    }

    .kiosk-preloader__copy {
        position: absolute;
        left: 50%;
        bottom: -8px;
        transform: translateX(-50%);
        width: max-content;
        max-width: min(90vw, 250px);
        padding: 7px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
        color: #334155;
        font-size: 0.76rem;
        line-height: 1.4;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        text-align: center;
    }

    body.kiosk-loading {
        overflow: hidden;
    }

    @keyframes kiosk-preloader-orbit {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes kiosk-preloader-wave {
        0% {
            transform: scale(0.74);
            opacity: 0;
        }

        25% {
            opacity: 0.48;
        }

        70% {
            opacity: 0.18;
        }

        100% {
            transform: scale(1.12);
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .kiosk-preloader,
        .kiosk-preloader * {
            animation: none !important;
            transition: none !important;
        }

        .kiosk-preloader__wave {
            opacity: 0.14;
            transform: none;
        }
    }

    @media (max-width: 575.98px) {
        .kiosk-preloader__stage {
            width: min(100%, 176px);
        }

        .kiosk-preloader__label {
            top: 2px;
            min-height: 32px;
            min-width: 102px;
            padding: 5px 10px;
        }

        .kiosk-preloader__label img {
            height: 13px;
            max-width: 80px;
        }

        .kiosk-preloader__copy {
            font-size: 0.7rem;
            max-width: min(88vw, 212px);
        }
    }
</style>

<div class="kiosk-preloader" data-kiosk-preloader aria-live="polite" aria-label="Page loading">
    <div class="kiosk-preloader__stage" role="status">
        <span class="kiosk-preloader__glow" aria-hidden="true"></span>
        <span class="kiosk-preloader__label">
            <img src="{{ asset('assets/images/logo/logo.svg') }}" alt="Kiosk">
        </span>
        <span class="kiosk-preloader__wave kiosk-preloader__wave--one" aria-hidden="true"></span>
        <span class="kiosk-preloader__wave kiosk-preloader__wave--two" aria-hidden="true"></span>
        <span class="kiosk-preloader__wave kiosk-preloader__wave--three" aria-hidden="true"></span>
        <div class="kiosk-preloader__ring" aria-hidden="true">
            <div class="kiosk-preloader__center">
                <img src="{{ asset('assets/images/logo/favicon.svg') }}" alt="Kiosk" class="kiosk-preloader__logo">
            </div>
        </div>
        <p class="kiosk-preloader__copy">Getting things ready for you.</p>
    </div>
</div>
