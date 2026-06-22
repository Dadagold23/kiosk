<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InfoPageController extends Controller
{
    public function about(): View
    {
        $sectionImages = $this->assetImages('section');

        $stats = [
            ['value' => '5', 'suffix' => '', 'title' => 'Connected modules', 'copy' => 'Commerce, services, consultancy, bookings, and emergency support share one customer flow.'],
            ['value' => '24', 'suffix' => '/7', 'title' => 'Support visibility', 'copy' => 'Customers can follow requests, payments, and fulfillment records from one account space.'],
            ['value' => '3', 'suffix' => '', 'title' => 'Operational hubs', 'copy' => 'Kiosk coordinates storefront and response activity through key branch and partner points.'],
            ['value' => '1', 'suffix' => '', 'title' => 'Unified experience', 'copy' => 'One platform reduces context switching between product, service, and support actions.'],
        ];

        $accordions = [
            ['title' => 'What Kiosk is', 'body' => 'Kiosk is a multi-module customer platform that combines product shopping, service requests, consultancy intake, reservations, and emergency support into one coordinated storefront.'],
            ['title' => 'How the platform works', 'body' => 'Customers browse, request, pay, and track from a single account, while operations teams manage fulfillment, updates, and support through the relevant Kiosk module.'],
            ['title' => 'What sets Kiosk apart', 'body' => 'The platform is designed to keep customer movement simpler: fewer handoffs, clearer visibility, stronger request history, and better continuity between everyday purchases and higher-touch requests.'],
            ['title' => 'Our commitment', 'body' => 'Kiosk is built around responsive support, reliable transaction handling, clearer tracking, and a storefront experience that can scale across local and global sourcing workflows.'],
        ];

        $brands = [
            ['name' => 'Local Commerce Partners', 'meta' => 'Stocked inventory and merchant supply', 'image' => $this->assetPath('brand', 'anthro.png')],
            ['name' => 'Global Sourcing Desk', 'meta' => 'Assisted imports and specialty requests', 'image' => $this->assetPath('brand', 'west-elm.png')],
            ['name' => 'Service Network', 'meta' => 'Field teams and skilled providers', 'image' => $this->assetPath('brand', 'stanza.png')],
            ['name' => 'Advisory Circle', 'meta' => 'Consultants and subject specialists', 'image' => $this->assetPath('brand', 'urban.png')],
            ['name' => 'Reservation Partners', 'meta' => 'Travel, venue, and hospitality support', 'image' => $this->assetPath('brand', 'crate.png')],
            ['name' => 'Response Coordination', 'meta' => 'Urgent workflow and escalation support', 'image' => $this->assetPath('brand', 'bohome.png')],
        ];

        $testimonials = [
            ['name' => 'Adaeze N.', 'role' => 'Customer', 'quote' => 'Kiosk made it easier to shop, ask for help, and track what was happening without leaving my account flow.', 'image' => $this->assetPath('testimonial', 'tes-1.jpg')],
            ['name' => 'Tunde A.', 'role' => 'Operations Client', 'quote' => 'The strongest part is the continuity. Orders, payments, and support requests all feel connected instead of scattered.', 'image' => $this->assetPath('testimonial', 'tes-2.jpg')],
        ];

        $team = [
            ['name' => 'Commerce Lead', 'role' => 'Catalog and sourcing coordination', 'image' => $this->assetPath('member', 'member-1.jpg')],
            ['name' => 'Support Desk', 'role' => 'Customer guidance and escalation', 'image' => $this->assetPath('member', 'member-2.jpg')],
            ['name' => 'Advisory Desk', 'role' => 'Consultancy and booking intake', 'image' => $this->assetPath('member', 'member-3.jpg')],
            ['name' => 'Field Response', 'role' => 'Service and emergency workflow support', 'image' => $this->assetPath('member', 'member-4.jpg')],
        ];

        $heroImage = $sectionImages[12]['url'] ?? $sectionImages[0]['url'] ?? null;
        $bannerImage = $sectionImages[5]['url'] ?? $heroImage;

        return view('frontend.about-kiosk', compact('stats', 'accordions', 'brands', 'testimonials', 'team', 'heroImage', 'bannerImage'));
    }

    public function branches(): View
    {
        $branches = [
            ['code' => 'LG', 'name' => 'Lagos Flagship Hub', 'summary' => 'Commerce operations, product sourcing, storefront management, and payment monitoring.', 'address' => 'Lekki Phase 1, Lagos', 'image' => $this->assetPath('slider', 'slider-1.jpg')],
            ['code' => 'AB', 'name' => 'Abuja Service Desk', 'summary' => 'Consultancy intake, reservation follow-up, customer care escalation, and account coordination.', 'address' => 'Central Business District, Abuja', 'image' => $this->assetPath('slider', 'slider-2.jpg')],
            ['code' => 'PH', 'name' => 'Port Harcourt Response Point', 'summary' => 'Field support liaison, logistics coordination, and request handoff for urgent workflows.', 'address' => 'GRA Phase 2, Port Harcourt', 'image' => $this->assetPath('slider', 'slider-3.jpg')],
            ['code' => 'PF', 'name' => 'Partner Fulfillment Link', 'summary' => 'Distribution and supplier-side communication for sourced or specialty orders.', 'address' => 'Hybrid partner network', 'image' => $this->assetPath('slider', 'slider-4.jpg')],
            ['code' => 'DO', 'name' => 'Digital Operations Layer', 'summary' => 'Online tracking, support continuity, and request visibility across all Kiosk modules.', 'address' => 'Served through the Kiosk platform', 'image' => $this->assetPath('slider', 'slider-5.jpg')],
        ];

        return view('frontend.kiosk-branches', compact('branches'));
    }

    public function contact(): View
    {
        return $this->page('Contact Kiosk', 'Reach the Kiosk team through office, phone, or email support.', [
            [
                'heading' => 'Head office',
                'body' => 'Mirrorage Concepts / Kiosk, 24 Admiralty Way service corridor, Lekki Phase 1, Lagos, Nigeria.',
            ],
            [
                'heading' => 'Support channels',
                'body' => 'General support email: info@mirrorageconcepts.com. Primary support lines: +234 703 562 7734 and +234 803 000 1542 for customer follow-up, account questions, and order assistance.',
            ],
            [
                'heading' => 'Working contact rhythm',
                'body' => 'Commerce, booking, and advisory questions are handled during core support hours, while urgent incidents submitted through the emergency path are escalated according to availability and response priority.',
            ],
        ], [
            'Email: info@mirrorageconcepts.com',
            'Phone: +234 703 562 7734',
            'Alt line: +234 803 009 5591',
            'Office: Lekki Phase 1, Lagos, Nigeria',
        ], [
            'eyebrow' => 'Support channels',
            'cta_label' => 'Email Kiosk',
            'cta_url' => 'mailto:info@mirrorageconcepts.com',
            'hero_image' => $this->assetPath('section', 'banner-21.jpg'),
            'spotlight_images' => [
                $this->assetPath('gallery', 'gallery-1.jpg'),
                $this->assetPath('slider', 'slider-6.jpg'),
            ],
        ]);
    }

    public function shippingServices(): View
    {
        return $this->page('Shipping, Delivery, and Service Terms', 'What customers should expect once an order, service request, or booking moves into fulfillment.', [
            [
                'heading' => 'Product delivery timing',
                'body' => 'In-stock products are prepared after payment confirmation and then moved into pickup, dispatch, or arranged delivery. Sourced or assisted-import items may take longer because supplier confirmation, customs handling, or third-party logistics can affect the final timeline.',
            ],
            [
                'heading' => 'Service and request fulfillment',
                'body' => 'Service requests are reviewed before scheduling or dispatch. Kiosk may confirm scope, location, contact details, pricing, or timing before a request is accepted and moved into progress.',
            ],
            [
                'heading' => 'Customer communication',
                'body' => 'Kiosk shares progress through your dashboard, order pages, receipts, payment records, and direct follow-up where needed. Delivery or service timing may change if verification, stock availability, or location details need review.',
            ],
        ], [
            'Delivery windows can differ between stocked items and sourced items',
            'Service requests may need review before scheduling is confirmed',
            'Order and service timing is communicated per transaction',
        ], [
            'eyebrow' => 'Customer terms',
            'cta_label' => 'Open services',
            'cta_route' => 'services.index',
            'hero_image' => $this->assetPath('section', 'banner-22.jpg'),
            'spotlight_images' => [
                $this->assetPath('gallery', 'gallery-2.jpg'),
                $this->assetPath('slider', 'slider-7.jpg'),
            ],
        ]);
    }

    public function returnsAdvisory(): View
    {
        return $this->page('Returns, Refund Review, and Support Terms', 'What happens when a customer reports a delivery issue, requests a review, or needs after-purchase support.', [
            [
                'heading' => 'Returns and issue review',
                'body' => 'Requests are reviewed against item condition, delivery status, proof of payment, and the nature of the complaint. Kiosk may ask for photos, delivery notes, or other supporting details before confirming the next step.',
            ],
            [
                'heading' => 'Customer support guidance',
                'body' => 'If you are unsure whether a problem qualifies for return, replacement, or service correction, Kiosk support can review the issue first and explain the available path before escalation.',
            ],
            [
                'heading' => 'Possible resolutions',
                'body' => 'Depending on the case, Kiosk may recommend replacement, refund review, account credit consideration, or direct correction where the issue relates to handling, fulfillment, or support rather than product misuse.',
            ],
        ], [
            'Keep receipts and delivery evidence for faster review',
            'Report order issues promptly after receipt',
            'Some sourced or special-order items may have separate review conditions',
        ], [
            'eyebrow' => 'After-purchase terms',
            'cta_label' => 'Contact support',
            'cta_route' => 'info.contact',
            'hero_image' => $this->assetPath('section', 'banner-23.jpg'),
            'spotlight_images' => [
                $this->assetPath('gallery', 'gallery-3.jpg'),
                $this->assetPath('slider', 'slider-8.jpg'),
            ],
        ]);
    }

    public function privacyBooking(): View
    {
        return $this->page('Customer Privacy and Transaction Records', 'How Kiosk handles the customer details, payment-linked information, and transaction records used to complete orders and requests.', [
            [
                'heading' => 'Information used for transactions',
                'body' => 'Kiosk stores the customer information needed for account access, order handling, bookings, payments, delivery coordination, and support history. This can include contact details, profile fields, addresses, and transaction-linked activity required to complete your request.',
            ],
            [
                'heading' => 'Payment and request records',
                'body' => 'When you place an order, submit a booking, or pay for a service, Kiosk keeps the related payment references, request notes, order details, and operational records needed for confirmation, follow-up, and customer support.',
            ],
            [
                'heading' => 'Access and protection',
                'body' => 'Access is limited to the parts of the system needed for support, fulfillment, verification, and reporting. Customers are expected to keep account credentials private and make sure delivery, billing, and contact details are accurate before checkout.',
            ],
        ], [
            'Only share details that are relevant to the order, booking, or service request',
            'Transaction and fulfillment records may stay attached to your account for support and compliance',
            'Account access and payment follow-up should always be handled through your secured Kiosk session',
        ], [
            'eyebrow' => 'Privacy notice',
            'cta_label' => 'View reservations',
            'cta_route' => 'booking.index',
            'hero_image' => $this->assetPath('section', 'banner-24.jpg'),
            'spotlight_images' => [
                $this->assetPath('gallery', 'gallery-4.jpg'),
                $this->assetPath('slider', 'slider-9.jpg'),
            ],
        ]);
    }

    public function ordersFaqs(): View
    {
        return $this->page('Checkout Terms and Customer Agreement', 'The basic terms customers agree to when placing an order or moving into payment with Kiosk.', [
            [
                'heading' => 'When your order begins',
                'body' => 'An order is created when checkout is completed and payment is started or confirmed. From that point, the order may move through review, payment, fulfillment, sourcing, dispatch, and delivery states depending on the product or service type.',
            ],
            [
                'heading' => 'Your agreement as a customer',
                'body' => 'By continuing with checkout, you confirm that your contact details, delivery information, item selection, and payment intent are correct. You also agree that Kiosk may contact you when clarification, stock review, sourcing confirmation, or fulfillment changes are needed.',
            ],
            [
                'heading' => 'Tracking and support after payment',
                'body' => 'Customers can return to order pages, receipts, and notifications to confirm payment state, fulfillment progress, and any follow-up action needed from their account.',
            ],
        ], [
            'How do I know if my payment went through? Use your order page, receipt, and payment status after checkout.',
            'Can I retry payment on an unpaid order? Yes, eligible unpaid records can be reopened from the customer order area.',
            'Why is my order taking longer? Sourced items, manual verification, or fulfillment dependencies can extend timelines.',
            'Where do I ask for help? Use your account records first, then contact Kiosk support if the order needs attention.',
            'Will every order have the same delivery window? No, timelines vary by product type, location, sourcing path, and confirmation workflow.',
        ], [
            'eyebrow' => 'Transaction agreement',
            'cta_label' => 'Open account',
            'cta_route' => 'login',
            'hero_image' => $this->assetPath('section', 'banner-25.jpg'),
            'spotlight_images' => [
                $this->assetPath('gallery', 'gallery-5.jpg'),
                $this->assetPath('slider', 'slider-10.jpg'),
            ],
        ]);
    }

    private function page(string $title, string $intro, array $sections, array $highlights, array $meta = []): View
    {
        $meta = array_merge([
            'hero_image' => $this->assetPath('section', 'banner-10.jpg'),
            'spotlight_images' => [
                $this->assetPath('gallery', 'gallery-6.jpg'),
                $this->assetPath('slider', 'slider-11.jpg'),
            ],
        ], $meta);

        return view('frontend.info-page', compact('title', 'intro', 'sections', 'highlights', 'meta'));
    }

    private function assetImages(string $folder): array
    {
        $directory = public_path("assets/images/{$folder}");

        if (! is_dir($directory)) {
            return [];
        }

        return collect(scandir($directory))
            ->filter(fn ($file) => ! in_array($file, ['.', '..'], true))
            ->sort(SORT_NATURAL)
            ->values()
            ->map(fn ($file) => [
                'file' => $file,
                'path' => "assets/images/{$folder}/{$file}",
                'url' => asset("assets/images/{$folder}/{$file}"),
            ])
            ->all();
    }

    private function assetPath(string $folder, string $file): ?string
    {
        $path = public_path("assets/images/{$folder}/{$file}");

        return is_file($path) ? asset("assets/images/{$folder}/{$file}") : null;
    }
}