<?php

return [
    'assets' => [
        'meta_image' => 'assets/images/meta/kiosk-meta-default.png',
        'product_placeholder' => 'assets/images/meta/kiosk-product-placeholder.png',
        'favicon' => 'favicon.ico',
    ],

    'social' => [
        'facebook' => env('KIOSK_SOCIAL_FACEBOOK', 'https://www.facebook.com/profile.php?id=61590489010576'),
        'instagram' => env('KIOSK_SOCIAL_INSTAGRAM', 'https://www.instagram.com/mirrorageconcepts'),
        'x' => env('KIOSK_SOCIAL_X', 'https://x.com/mirrorageconcepts'),
        'tiktok' => env('KIOSK_SOCIAL_TIKTOK', 'https://www.tiktok.com/@mirrorageconcepts'),
        'linkedin' => env('KIOSK_SOCIAL_LINKEDIN', ''),
        'youtube' => env('KIOSK_SOCIAL_YOUTUBE', ''),
        'whatsapp' => env('KIOSK_SOCIAL_WHATSAPP', ''),
        'pinterest' => env('KIOSK_SOCIAL_PINTEREST', ''),
        'twitter' => env('KIOSK_SOCIAL_TWITTER', ''),
    ],

    'security' => [
        'idle_timeout_minutes' => env('KIOSK_IDLE_TIMEOUT_MINUTES', 30),
    ],

    'payments' => [
        'currency' => env('KIOSK_PAYMENT_CURRENCY', 'NGN'),

        'manual' => [
            'bank_name' => env('KIOSK_BANK_NAME', 'Kiosk Collections Bank'),
            'account_name' => env('KIOSK_BANK_ACCOUNT_NAME', 'Kiosk Services Ltd'),
            'account_number' => env('KIOSK_BANK_ACCOUNT_NUMBER', '0001234567'),
            'support_phone' => env('KIOSK_PAYMENT_SUPPORT_PHONE', '+234 800 000 0000'),
            'support_email' => env('KIOSK_PAYMENT_SUPPORT_EMAIL', 'payments@kiosk.test'),
            'note' => env('KIOSK_PAYMENT_NOTE', 'Use your payment reference as the transfer narration so the finance desk can match your payment quickly.'),
        ],

        'paystack' => [
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
            'public_app_url' => env('PAYSTACK_PUBLIC_APP_URL', env('APP_URL')),
            'callback_route' => env('PAYSTACK_CALLBACK_ROUTE', 'payments.paystack.callback'),
            'webhook_path' => env('PAYSTACK_WEBHOOK_PATH', '/webhooks/paystack'),
            'timeout_seconds' => env('PAYSTACK_TIMEOUT_SECONDS', 30),
            'connect_timeout_seconds' => env('PAYSTACK_CONNECT_TIMEOUT_SECONDS', 10),
            'force_ipv4' => env('PAYSTACK_FORCE_IPV4', true),
            'verify_ssl' => env('PAYSTACK_VERIFY_SSL', true),
        ],
    ],

    'kyc' => [
        'dojah' => [
            'app_id' => env('DOJAH_APP_ID'),
            'secret_key' => env('DOJAH_SECRET_KEY'),
            'base_url' => env('DOJAH_BASE_URL', 'https://sandbox.dojah.io'),
            'timeout_seconds' => env('DOJAH_TIMEOUT_SECONDS', 20),
            'connect_timeout_seconds' => env('DOJAH_CONNECT_TIMEOUT_SECONDS', 10),
        ],
    ],

    'geo' => [
        'reverse_geocode_url' => env('KIOSK_REVERSE_GEOCODE_URL', 'https://nominatim.openstreetmap.org/reverse'),
        'reverse_geocode_timeout_seconds' => env('KIOSK_REVERSE_GEOCODE_TIMEOUT_SECONDS', 8),
        'reverse_geocode_connect_timeout_seconds' => env('KIOSK_REVERSE_GEOCODE_CONNECT_TIMEOUT_SECONDS', 5),
    ],

    'marketplaces' => [
        'sync' => [
            'enabled' => env('MARKETPLACE_SYNC_ENABLED', true),
            'prune_missing' => env('MARKETPLACE_SYNC_PRUNE_MISSING', true),
            'timeout_seconds' => env('MARKETPLACE_SYNC_TIMEOUT_SECONDS', 20),
            'schedule' => env('MARKETPLACE_SYNC_SCHEDULE', 'hourly'),
        ],

        'providers' => [
            'jumia' => [
                'label' => 'Jumia',
                'enabled' => env('MARKETPLACE_JUMIA_ENABLED', true),
                'feed_url' => env('MARKETPLACE_JUMIA_FEED_URL'),
                'seed_path' => resource_path('data/marketplaces/jumia.json'),
            ],
            'temu' => [
                'label' => 'Temu',
                'enabled' => env('MARKETPLACE_TEMU_ENABLED', true),
                'feed_url' => env('MARKETPLACE_TEMU_FEED_URL'),
                'seed_path' => resource_path('data/marketplaces/temu.json'),
            ],
            'alibaba' => [
                'label' => 'Alibaba',
                'enabled' => env('MARKETPLACE_ALIBABA_ENABLED', true),
                'feed_url' => env('MARKETPLACE_ALIBABA_FEED_URL'),
                'seed_path' => resource_path('data/marketplaces/alibaba.json'),
            ],
            'aliexpress' => [
                'label' => 'AliExpress',
                'enabled' => env('MARKETPLACE_ALIEXPRESS_ENABLED', true),
                'feed_url' => env('MARKETPLACE_ALIEXPRESS_FEED_URL'),
                'seed_path' => resource_path('data/marketplaces/aliexpress.json'),
            ],
            'jiji' => [
                'label' => 'Jiji',
                'enabled' => env('MARKETPLACE_JIJI_ENABLED', true),
                'feed_url' => env('MARKETPLACE_JIJI_FEED_URL'),
                'seed_path' => resource_path('data/marketplaces/jiji.json'),
            ],
        ],
    ],

    'orders' => [
        'tracking_statuses' => [
            'pending',
            'procurement_review',
            'supplier_confirmed',
            'procurement_in_progress',
            'processing',
            'sourced',
            'quality_check',
            'packed',
            'ready_for_dispatch',
            'dispatched',
            'in_transit',
            'out_for_delivery',
            'delivered',
            'failed_delivery',
            'returned',
            'cancelled',
        ],
    ],

    'services' => [
        'tracking_statuses' => [
            'request_received',
            'payment_confirmed',
            'under_review',
            'team_assigned',
            'visit_scheduled',
            'en_route',
            'on_site',
            'in_progress',
            'awaiting_parts',
            'quality_check',
            'completed',
            'closed',
        ],
    ],

    'emergency' => [
        'types' => [
            'fire',
            'accident',
            'medical',
            'police',
            'road_safety',
            'disaster',
            'domestic',
            'security',
            'other',
        ],

        'statuses' => [
            'pending',
            'received',
            'contacted',
            'responding',
            'on_scene',
            'resolved',
            'closed',
        ],

        'tracking_statuses' => [
            'received',
            'contacted',
            'responding',
            'unit_dispatched',
            'en_route',
            'approaching_destination',
            'on_scene',
            'resolved',
            'closed',
        ],

        'default_country_code' => 'NG',
        'default_country_name' => 'Nigeria',
        'geo_data_path' => resource_path('data/nigeria-states-lgas.json'),
        'directory_data_path' => resource_path('data/nigeria-emergency-units.json'),
    ],
];
