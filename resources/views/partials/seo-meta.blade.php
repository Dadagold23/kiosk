@php
    $stringifyMeta = function ($value, string $fallback = '') {
        if (is_array($value)) {
            $flattened = collect($value)
                ->flatten()
                ->filter(fn ($item) => is_scalar($item) || $item === null)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->implode(', ');
            return $flattened !== '' ? $flattened : $fallback;
        }
        if (is_object($value) && ! method_exists($value, '__toString')) {
            return $fallback;
        }
        $string = trim((string) ($value ?? ''));
        return $string !== '' ? $string : $fallback;
    };

    $sectionValue = function (string $section, $fallback = null) use ($__env, $stringifyMeta) {
        $content = $__env->yieldContent($section);
        if (is_string($content) && trim($content) !== '') {
            return $content;
        }
        if (! is_string($content) && ! empty($content)) {
            return $content;
        }
        return $fallback;
    };

    $appName             = config('app.name', 'Kiosk');
    $metaTitle           = $stringifyMeta($sectionValue('meta_title', $title ?? $appName), $appName);
    $metaDescription     = $stringifyMeta($sectionValue('meta_description', $description ?? 'Kiosk platform for commerce, services, consultancy, booking, and emergency support.'), 'Kiosk platform for commerce, services, consultancy, booking, and emergency support.');
    $metaKeywords        = $stringifyMeta($sectionValue('meta_keywords', $keywords ?? 'Kiosk, e-commerce, services, consultancy, booking, emergency'), 'Kiosk, e-commerce, services, consultancy, booking, emergency');
    $metaAuthor          = $stringifyMeta($sectionValue('meta_author', $author ?? $appName), $appName);
    $metaRobots          = $stringifyMeta($sectionValue('meta_robots', $robots ?? 'index, follow'), 'index, follow');
    $canonicalUrl        = $stringifyMeta($sectionValue('canonical_url', $canonical ?? url()->current()), url()->current());
    $metaImage           = $stringifyMeta($sectionValue('og_image', $image ?? asset(config('kiosk.assets.meta_image'))), asset(config('kiosk.assets.meta_image')));
    $themeColorValue     = $stringifyMeta($themeColor ?? '#f97316', '#f97316');
    $ogType              = $stringifyMeta($sectionValue('og_type', 'website'), 'website');
    $ogTitle             = $stringifyMeta($sectionValue('og_title', $metaTitle), $metaTitle);
    $ogDescription       = $stringifyMeta($sectionValue('og_description', $metaDescription), $metaDescription);
    $ogUrl               = $stringifyMeta($sectionValue('og_url', $canonicalUrl), $canonicalUrl);
    $ogImageAlt          = $stringifyMeta($sectionValue('og_image_alt', $metaTitle), $metaTitle);
    $twitterCard         = $stringifyMeta($sectionValue('twitter_card', 'summary_large_image'), 'summary_large_image');
    $twitterTitle        = $stringifyMeta($sectionValue('twitter_title', $metaTitle), $metaTitle);
    $twitterDescription  = $stringifyMeta($sectionValue('twitter_description', $metaDescription), $metaDescription);
    $twitterImage        = $stringifyMeta($sectionValue('twitter_image', $metaImage), $metaImage);
    $twitterImageAlt     = $stringifyMeta($sectionValue('twitter_image_alt', $metaTitle), $metaTitle);
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="author" content="{{ $metaAuthor }}">
<meta name="robots" content="{{ $metaRobots }}">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="theme-color" content="{{ $themeColorValue }}">

<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:locale" content="en_US">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $ogImageAlt }}">

<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
<meta name="twitter:image" content="{{ $twitterImage }}">
<meta name="twitter:image:alt" content="{{ $twitterImageAlt }}">
@yield('meta_extra')
