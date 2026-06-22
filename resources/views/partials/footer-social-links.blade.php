@php
    $socialPlatforms = [
        ['label' => 'Facebook', 'icon' => 'icon-FacebookLogo', 'config' => 'kiosk.social.facebook'], ['label' => 'Instagram', 'icon' => 'icon-InstagramLogo', 'config' => 'kiosk.social.instagram'], ['label' => 'X', 'icon' => 'icon-XLogo', 'config' => 'kiosk.social.x'], ['label' => 'TikTok', 'icon' => 'icon-TiktokLogo', 'config' => 'kiosk.social.tiktok'], ['label' => 'LinkedIn', 'icon' => 'bi bi-linkedin', 'config' => 'kiosk.social.linkedin'], ['label' => 'YouTube', 'icon' => 'bi bi-youtube', 'config' => 'kiosk.social.youtube'], ['label' => 'WhatsApp', 'icon' => 'bi bi-whatsapp', 'config' => 'kiosk.social.whatsapp'], ['label' => 'Pinterest', 'icon' => 'bi bi-pinterest', 'config' => 'kiosk.social.pinterest'],
    ];
@endphp

<ul class="footer-social-list" aria-label="Social media accounts">
    @foreach ($socialPlatforms as $platform)
        @php
            $url = config($platform['config']);
        @endphp
        @if (filled($url))
            <li>
                <a class="footer-social-link" href="{{ $url }}"
                    target="_blank" rel="noopener noreferrer"
                    aria-label="Kiosk on {{ $platform['label'] }}">
                    <i class="icon {{ $platform['icon'] }}" aria-hidden="true"></i>
                </a>
            </li>
        @endif
    @endforeach
</ul>
