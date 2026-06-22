<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\ConsultancyRequest;
use App\Models\EmergencyRequest;
use App\Models\EmergencyServiceUnit;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperationalDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
    }

    private function seedUsers(): array
    {
        $password = Hash::make('password');

        $definitions = [
            'super_admin' => [
                'name' => 'Abdullateef Olalekan Dada',
                'email' => 'mirrorageconcepts@gmail.com',
                'phone' => '07035627734',
                'address' => 'Kiosk HQ, Offa, Kwara State',
                'role' => 'Super Admin',
            ],
        ];

        $users = [];

        foreach ($definitions as $key => $definition) {
            $role = $definition['role'];
            unset($definition['role']);

            $user = User::updateOrCreate(
                ['email' => $definition['email']],
                array_merge($definition, [
                    'password' => $password,
                    'email_verified_at' => now(),
                ])
            );

            $user->syncRoles([$role]);
            $users[$key] = $user;
        }

        return $users;
    }

    private function seedOrders(array $users): void
    {
        $products = Product::query()
            ->whereIn('sku', [
                'LOC-COMP-001',
                'LOC-BABY-001',
                'LOC-BACC-002',
                'LOC-HAP-007',
                'LOC-FOOD-001',
                'JUM-PHN-001',
                'TEM-BABY-001',
                'ALI-COMP-001',
                'AEX-HOME-001',
                'JIJ-PHN-001',
            ])
            ->get()
            ->keyBy('sku');

        if ($products->count() < 10) {
            return;
        }

        $definitions = [
            [
                'order_no' => 'KSK-OPS-1001',
                'user' => $users['customer_primary'],
                'order_type' => 'global_shop',
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'delivery_fee' => 4500,
                'service_charge' => 1500,
                'payment_reference' => 'PAY-OPS-1001',
                'notes' => 'Mixed procurement order with one global-marketplace phone and one locally fulfilled business laptop.',
                'payment' => [
                    'payment_method' => 'card',
                    'gateway' => 'paystack',
                    'status' => 'paid',
                    'paid_at' => now()->subDays(2),
                    'gateway_transaction_id' => 'PSTK-OPS-1001',
                    'gateway_response' => 'Approved',
                    'gateway_verified_at' => now()->subDays(2)->addMinutes(3),
                ],
                'items' => [
                    [
                        'product' => $products['JUM-PHN-001'],
                        'qty' => 1,
                        'fulfillment_status' => 'supplier_confirmed',
                        'logistics_partner' => 'Kiosk Global Procurement Desk',
                        'tracking_number' => 'KGP-1001-01',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/KGP-1001-01',
                        'last_tracked_at' => now()->subHours(11),
                        'meta' => [
                            'sourcing_team' => 'Global Retail Partnerships',
                            'provider' => 'Jumia',
                        ],
                        'events' => [
                            [
                                'status' => 'procurement_review',
                                'location' => 'Global marketplace sourcing desk',
                                'note' => 'Marketplace listing validated and price locked for procurement.',
                                'event_time' => now()->subDays(2)->addHours(1),
                            ],
                            [
                                'status' => 'supplier_confirmed',
                                'location' => 'Jumia fulfillment partner',
                                'note' => 'Supplier confirmed stock reservation and dispatch handover window.',
                                'event_time' => now()->subDays(1)->addHours(3),
                            ],
                        ],
                    ],
                    [
                        'product' => $products['LOC-COMP-001'],
                        'qty' => 1,
                        'fulfillment_status' => 'quality_check',
                        'logistics_partner' => 'Kiosk Warehouse QC',
                        'tracking_number' => 'LAG-QC-1001-02',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/LAG-QC-1001-02',
                        'last_tracked_at' => now()->subHours(5),
                        'meta' => [
                            'warehouse' => 'Ikeja retail warehouse',
                            'picked_by' => $users['shop_manager']->id,
                        ],
                        'events' => [
                            [
                                'status' => 'processing',
                                'location' => 'Ikeja retail warehouse',
                                'note' => 'Laptop picked from shelf and allocated to confirmed order.',
                                'event_time' => now()->subDays(1)->subHours(8),
                            ],
                            [
                                'status' => 'quality_check',
                                'location' => 'Outbound QA bay',
                                'note' => 'Battery cycle count, keyboard test, and packaging inspection passed.',
                                'event_time' => now()->subHours(5),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'order_no' => 'KSK-OPS-1002',
                'user' => $users['customer_family'],
                'order_type' => 'local_shop',
                'payment_status' => 'paid',
                'order_status' => 'delivered',
                'delivery_fee' => 3500,
                'service_charge' => 800,
                'payment_reference' => 'PAY-OPS-1002',
                'notes' => 'Family essentials order delivered successfully to Port Harcourt residence.',
                'payment' => [
                    'payment_method' => 'transfer',
                    'gateway' => 'manual',
                    'status' => 'paid',
                    'paid_at' => now()->subDays(6),
                    'gateway_response' => 'Bank transfer matched and cleared',
                    'gateway_verified_at' => now()->subDays(6)->addMinutes(22),
                ],
                'items' => [
                    [
                        'product' => $products['LOC-BABY-001'],
                        'qty' => 2,
                        'fulfillment_status' => 'delivered',
                        'logistics_partner' => 'Kiosk Express',
                        'tracking_number' => 'KEX-1002-01',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/KEX-1002-01',
                        'last_tracked_at' => now()->subDays(4)->addHours(6),
                        'shipped_at' => now()->subDays(5),
                        'delivered_at' => now()->subDays(4)->addHours(6),
                        'events' => [
                            [
                                'status' => 'packed',
                                'location' => 'Retail packing station',
                                'note' => 'Diapers packed with tamper-proof wrap and invoice insert.',
                                'event_time' => now()->subDays(5)->subHours(4),
                            ],
                            [
                                'status' => 'dispatched',
                                'location' => 'Lagos outbound route',
                                'note' => 'Shipment moved with regional courier route to Port Harcourt.',
                                'event_time' => now()->subDays(5),
                            ],
                            [
                                'status' => 'delivered',
                                'location' => 'GRA Phase 2, Port Harcourt',
                                'note' => 'Consignee received package and signed on doorstep.',
                                'event_time' => now()->subDays(4)->addHours(6),
                            ],
                        ],
                    ],
                    [
                        'product' => $products['LOC-BACC-002'],
                        'qty' => 1,
                        'fulfillment_status' => 'delivered',
                        'logistics_partner' => 'Kiosk Express',
                        'tracking_number' => 'KEX-1002-02',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/KEX-1002-02',
                        'last_tracked_at' => now()->subDays(4)->addHours(6),
                        'shipped_at' => now()->subDays(5),
                        'delivered_at' => now()->subDays(4)->addHours(6),
                        'events' => [
                            [
                                'status' => 'quality_check',
                                'location' => 'Fulfillment bay',
                                'note' => 'Stroller wheel lock and folding joints inspected before dispatch.',
                                'event_time' => now()->subDays(5)->subHours(2),
                            ],
                            [
                                'status' => 'out_for_delivery',
                                'location' => 'Port Harcourt city route',
                                'note' => 'Courier left the local hub for final delivery window.',
                                'event_time' => now()->subDays(4)->subHours(3),
                            ],
                            [
                                'status' => 'delivered',
                                'location' => 'GRA Phase 2, Port Harcourt',
                                'note' => 'Item delivered together with diaper pack.',
                                'event_time' => now()->subDays(4)->addHours(6),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'order_no' => 'KSK-OPS-1003',
                'user' => $users['customer_business'],
                'order_type' => 'global_shop',
                'payment_status' => 'under_review',
                'order_status' => 'reviewing',
                'delivery_fee' => 9000,
                'service_charge' => 2500,
                'payment_reference' => 'PAY-OPS-1003',
                'notes' => 'Business procurement basket awaiting finance confirmation and supplier release.',
                'payment' => [
                    'payment_method' => 'bank_transfer',
                    'gateway' => 'manual',
                    'status' => 'under_review',
                    'gateway_response' => 'Payment receipt submitted and pending finance confirmation',
                ],
                'items' => [
                    [
                        'product' => $products['ALI-COMP-001'],
                        'qty' => 1,
                        'fulfillment_status' => 'procurement_review',
                        'logistics_partner' => 'Kiosk B2B Imports',
                        'tracking_number' => 'KBI-1003-01',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/KBI-1003-01',
                        'last_tracked_at' => now()->subHours(20),
                        'events' => [
                            [
                                'status' => 'pending',
                                'location' => 'Finance verification queue',
                                'note' => 'Awaiting proof-of-payment confirmation before supplier engagement.',
                                'event_time' => now()->subDay()->subHours(7),
                            ],
                            [
                                'status' => 'procurement_review',
                                'location' => 'B2B import desk',
                                'note' => 'Commercial invoice and MOQ terms reviewed for the touchscreen terminal.',
                                'event_time' => now()->subHours(20),
                            ],
                        ],
                    ],
                    [
                        'product' => $products['JIJ-PHN-001'],
                        'qty' => 2,
                        'fulfillment_status' => 'pending',
                        'logistics_partner' => 'Kiosk Device Sourcing Unit',
                        'tracking_number' => 'KDS-1003-02',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/KDS-1003-02',
                        'last_tracked_at' => now()->subHours(20),
                        'events' => [
                            [
                                'status' => 'pending',
                                'location' => 'Device sourcing queue',
                                'note' => 'Vendor shortlist created and device grading checks pending payment clearance.',
                                'event_time' => now()->subHours(20),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'order_no' => 'KSK-OPS-1004',
                'user' => $users['customer_primary'],
                'order_type' => 'global_shop',
                'payment_status' => 'paid',
                'order_status' => 'dispatched',
                'delivery_fee' => 5000,
                'service_charge' => 1200,
                'payment_reference' => 'PAY-OPS-1004',
                'notes' => 'Home appliance order cleared by Paystack and now on final-mile delivery route.',
                'payment' => [
                    'payment_method' => 'card',
                    'gateway' => 'paystack',
                    'status' => 'paid',
                    'paid_at' => now()->subDays(3),
                    'gateway_transaction_id' => 'PSTK-OPS-1004',
                    'gateway_response' => 'Approved',
                    'gateway_verified_at' => now()->subDays(3)->addMinutes(2),
                ],
                'items' => [
                    [
                        'product' => $products['AEX-HOME-001'],
                        'qty' => 1,
                        'fulfillment_status' => 'out_for_delivery',
                        'logistics_partner' => 'Swift Haul Logistics',
                        'tracking_number' => 'SWI-1004-01',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/SWI-1004-01',
                        'last_tracked_at' => now()->subMinutes(40),
                        'shipped_at' => now()->subDay()->subHours(5),
                        'events' => [
                            [
                                'status' => 'supplier_confirmed',
                                'location' => 'AliExpress consolidation desk',
                                'note' => 'Seller released parcel into export consolidation lane.',
                                'event_time' => now()->subDays(2)->addHours(5),
                            ],
                            [
                                'status' => 'in_transit',
                                'location' => 'Lagos bonded warehouse',
                                'note' => 'Shipment cleared into domestic routing after customs release.',
                                'event_time' => now()->subDay()->subHours(5),
                            ],
                            [
                                'status' => 'out_for_delivery',
                                'location' => 'Lekki final-mile route',
                                'note' => 'Dispatch rider is on the final stop window for this parcel.',
                                'event_time' => now()->subMinutes(40),
                            ],
                        ],
                    ],
                    [
                        'product' => $products['LOC-HAP-007'],
                        'qty' => 1,
                        'fulfillment_status' => 'ready_for_dispatch',
                        'logistics_partner' => 'Swift Haul Logistics',
                        'tracking_number' => 'SWI-1004-02',
                        'tracking_url' => 'https://tracking.kiosk.test/orders/SWI-1004-02',
                        'last_tracked_at' => now()->subHours(3),
                        'events' => [
                            [
                                'status' => 'packed',
                                'location' => 'Lagos appliance dispatch hub',
                                'note' => 'Air fryer packed with protective foam and invoice copy.',
                                'event_time' => now()->subHours(6),
                            ],
                            [
                                'status' => 'ready_for_dispatch',
                                'location' => 'Dispatch manifest desk',
                                'note' => 'Local appliance merged into the same delivery run as the robot vacuum.',
                                'event_time' => now()->subHours(3),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $lineItems = collect($definition['items'])->map(function (array $item): array {
                $unitPrice = $item['product']->current_price;

                return array_merge($item, [
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $item['qty'],
                ]);
            });

            $subtotal = $lineItems->sum('subtotal');
            $order = Order::updateOrCreate(
                ['order_no' => $definition['order_no']],
                [
                    'user_id' => $definition['user']->id,
                    'order_type' => $definition['order_type'],
                    'subtotal' => $subtotal,
                    'delivery_fee' => $definition['delivery_fee'],
                    'service_charge' => $definition['service_charge'],
                    'total' => $subtotal + $definition['delivery_fee'] + $definition['service_charge'],
                    'payment_status' => $definition['payment_status'],
                    'order_status' => $definition['order_status'],
                    'payment_reference' => $definition['payment_reference'],
                    'delivery_address' => $definition['user']->deliveryAddress() ?: $definition['user']->address,
                    'notes' => $definition['notes'],
                ]
            );

            $this->syncOrderLineItems($order, $lineItems->all());
            $this->upsertPayment($order, $definition['user'], $definition['payment_reference'], array_merge(
                $definition['payment'],
                [
                    'amount' => $order->total,
                    'meta' => [
                        'type' => 'order_payment',
                        'seeded_by' => self::class,
                        'order_no' => $order->order_no,
                    ],
                ]
            ));
        }
    }

    private function seedServiceRequests(array $users): void
    {
        $categories = Category::query()
            ->where('type', 'service')
            ->get()
            ->keyBy('slug');

        $definitions = [
            [
                'lookup' => 'Three-bedroom duplex electrical audit and inverter conversion',
                'category_slug' => 'electrical-installation',
                'user' => $users['customer_primary'],
                'title' => 'Three-bedroom duplex electrical audit and inverter conversion',
                'description' => 'Client needs electrical load balancing, inverter sizing, and changeover panel installation for a Lekki duplex.',
                'location' => 'Lekki Phase 1, Lagos',
                'preferred_date' => now()->addDays(2)->toDateString(),
                'budget' => 450000,
                'assigned_to' => $users['service_manager']->id,
                'assigned_team' => 'Power Systems Team A',
                'status' => 'approved',
                'progress_status' => 'visit_scheduled',
                'tracking_updated_at' => now()->subHours(4),
                'service_window_start' => now()->addDays(2)->setTime(9, 0),
                'service_window_end' => now()->addDays(2)->setTime(13, 0),
                'payment_status' => 'paid',
                'fee' => 25000,
                'payment_reference' => 'SRV-OPS-1001',
                'tracking_events' => [
                    ['status' => 'request_received', 'location' => 'Customer portal', 'next_step' => 'Technical review', 'note' => 'Service request logged with site photos and power requirement summary.', 'event_time' => now()->subDay()->subHours(6)],
                    ['status' => 'payment_confirmed', 'location' => 'Finance desk', 'next_step' => 'Assign field lead', 'note' => 'Inspection fee confirmed and request released to operations.', 'event_time' => now()->subDay()->subHours(3)],
                    ['status' => 'team_assigned', 'location' => 'Lagos service coordination desk', 'next_step' => 'Schedule site visit', 'note' => 'Power Systems Team A assigned with site engineer and installer.', 'event_time' => now()->subHours(8)],
                    ['status' => 'visit_scheduled', 'location' => 'Lekki route desk', 'next_step' => 'Arrive on site within scheduled window', 'note' => 'Technician window booked with the customer for 9am to 1pm.', 'event_time' => now()->subHours(4)],
                ],
            ],
            [
                'lookup' => 'Commercial plumbing leak isolation for restaurant kitchen',
                'category_slug' => 'plumbing',
                'user' => $users['customer_family'],
                'title' => 'Commercial plumbing leak isolation for restaurant kitchen',
                'description' => 'Urgent leak tracing, valve replacement, and drainage correction required in a busy kitchen line.',
                'location' => 'Old GRA, Port Harcourt',
                'preferred_date' => now()->toDateString(),
                'budget' => 185000,
                'assigned_to' => $users['service_attendant']->id,
                'assigned_team' => 'Rapid Plumbing Crew',
                'status' => 'in_progress',
                'progress_status' => 'in_progress',
                'tracking_updated_at' => now()->subMinutes(35),
                'service_window_start' => now()->subHours(2),
                'service_window_end' => now()->addHours(2),
                'payment_status' => 'paid',
                'fee' => 12000,
                'payment_reference' => 'SRV-OPS-1002',
                'tracking_events' => [
                    ['status' => 'request_received', 'location' => 'Customer hotline', 'next_step' => 'Leak assessment', 'note' => 'Restaurant manager reported active leakage behind wash area.', 'event_time' => now()->subDay()],
                    ['status' => 'under_review', 'location' => 'Port Harcourt service desk', 'next_step' => 'Deploy crew', 'note' => 'Job risk and materials checklist completed by operations team.', 'event_time' => now()->subHours(12)],
                    ['status' => 'team_assigned', 'location' => 'Field dispatch board', 'next_step' => 'Move to site', 'note' => 'Rapid Plumbing Crew assigned with replacement valves and seal kit.', 'event_time' => now()->subHours(5)],
                    ['status' => 'on_site', 'location' => 'Old GRA, Port Harcourt', 'next_step' => 'Repair line', 'note' => 'Crew checked in on site and isolated the damaged section.', 'event_time' => now()->subHours(2)],
                    ['status' => 'in_progress', 'location' => 'Old GRA, Port Harcourt', 'next_step' => 'Pressure test and clean-up', 'note' => 'Pipe replacement ongoing with kitchen line temporarily rerouted.', 'event_time' => now()->subMinutes(35)],
                ],
            ],
            [
                'lookup' => 'Warehouse CCTV and network stabilisation',
                'category_slug' => 'technical-engineering',
                'user' => $users['customer_business'],
                'title' => 'Warehouse CCTV and network stabilisation',
                'description' => 'Business requested structured cable testing, DVR replacement, and access point balancing across a warehouse floor.',
                'location' => 'Kano Municipal, Kano',
                'preferred_date' => now()->subDays(3)->toDateString(),
                'budget' => 620000,
                'assigned_to' => $users['service_manager']->id,
                'assigned_team' => 'Network Operations Squad',
                'status' => 'completed',
                'progress_status' => 'completed',
                'tracking_updated_at' => now()->subDay(),
                'service_window_start' => now()->subDays(2)->setTime(10, 0),
                'service_window_end' => now()->subDays(2)->setTime(16, 30),
                'completed_at' => now()->subDay(),
                'payment_status' => 'paid',
                'fee' => 30000,
                'payment_reference' => 'SRV-OPS-1003',
                'tracking_events' => [
                    ['status' => 'request_received', 'location' => 'Customer dashboard', 'next_step' => 'Technical scoping', 'note' => 'Warehouse job logged with CCTV blind-spot and network downtime notes.', 'event_time' => now()->subDays(4)],
                    ['status' => 'payment_confirmed', 'location' => 'Finance desk', 'next_step' => 'Assign team lead', 'note' => 'Diagnostic fee settled and cleared into operations.', 'event_time' => now()->subDays(4)->addHours(2)],
                    ['status' => 'team_assigned', 'location' => 'Network operations desk', 'next_step' => 'Dispatch field engineers', 'note' => 'Two engineers and one cable technician allocated to the assignment.', 'event_time' => now()->subDays(3)->subHours(3)],
                    ['status' => 'on_site', 'location' => 'Kano warehouse floor', 'next_step' => 'Stabilise backbone', 'note' => 'Field team signed in and began segmented diagnostics.', 'event_time' => now()->subDays(2)->addHours(1)],
                    ['status' => 'quality_check', 'location' => 'Kano warehouse floor', 'next_step' => 'Close out report', 'note' => 'Camera feeds and Wi-Fi zones tested successfully after repairs.', 'event_time' => now()->subDay()->subHours(3)],
                    ['status' => 'completed', 'location' => 'Kano warehouse floor', 'next_step' => 'Share service report', 'note' => 'Client handover completed with final checklist and media confirmation.', 'event_time' => now()->subDay()],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $category = $categories->get($definition['category_slug']);

            if (! $category) {
                continue;
            }

            $serviceRequest = ServiceRequest::updateOrCreate(
                ['user_id' => $definition['user']->id, 'title' => $definition['lookup']],
                [
                    'category_id' => $category->id,
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'location' => $definition['location'],
                    'preferred_date' => $definition['preferred_date'],
                    'budget' => $definition['budget'],
                    'images' => [],
                    'assigned_to' => $definition['assigned_to'],
                    'assigned_team' => $definition['assigned_team'],
                    'status' => $definition['status'],
                    'progress_status' => $definition['progress_status'],
                    'tracking_updated_at' => $definition['tracking_updated_at'],
                    'service_window_start' => $definition['service_window_start'],
                    'service_window_end' => $definition['service_window_end'],
                    'completed_at' => $definition['completed_at'] ?? null,
                    'payment_status' => $definition['payment_status'],
                    'fee' => $definition['fee'],
                ]
            );

            $serviceRequest->trackingEvents()->delete();
            $serviceRequest->trackingEvents()->createMany(
                collect($definition['tracking_events'])
                    ->map(fn (array $event) => array_merge($event, [
                        'meta' => [
                            'updated_by' => $definition['assigned_to'],
                            'assigned_team' => $definition['assigned_team'],
                            'seeded_by' => self::class,
                        ],
                    ]))
                    ->all()
            );

            $this->upsertPayment($serviceRequest, $definition['user'], $definition['payment_reference'], [
                'amount' => $definition['fee'],
                'payment_method' => 'paystack',
                'gateway' => 'paystack',
                'status' => $definition['payment_status'],
                'paid_at' => $definition['payment_status'] === 'paid' ? now()->subDays(2) : null,
                'gateway_response' => $definition['payment_status'] === 'paid' ? 'Verification successful' : 'Awaiting gateway confirmation',
                'meta' => [
                    'type' => 'service_request_fee',
                    'seeded_by' => self::class,
                    'service_title' => $serviceRequest->title,
                ],
            ]);
        }
    }

    private function seedConsultancyRequests(array $users): void
    {
        $categories = Category::query()
            ->where('type', 'consultancy')
            ->get()
            ->keyBy('slug');

        $definitions = [
            [
                'user' => $users['customer_business'],
                'category_slug' => 'business-consultancy',
                'subject' => 'CAC incorporation, tax onboarding, and compliance roadmap',
                'description' => 'Client needs company incorporation support, post-registration tax setup, and a six-month compliance checklist.',
                'preferred_date' => now()->addDays(4)->toDateString(),
                'assigned_consultant_id' => $users['consultant_manager']->id,
                'status' => 'processing',
                'payment_status' => 'paid',
                'fee' => 45000,
                'admin_note' => 'KYC documents received. Draft incorporation checklist shared with client.',
                'reference' => 'CON-OPS-1001',
            ],
            [
                'user' => $users['customer_primary'],
                'category_slug' => 'legal-consultancy',
                'subject' => 'Employment contract review for retail workforce',
                'description' => 'Review required for retail staffing contracts, disciplinary clauses, and payroll-compliance wording.',
                'preferred_date' => now()->addDays(6)->toDateString(),
                'assigned_consultant_id' => $users['consultant']->id,
                'status' => 'reviewing',
                'payment_status' => 'paid',
                'fee' => 30000,
                'admin_note' => 'Counsel is reviewing draft documents and scheduled a clarification call.',
                'reference' => 'CON-OPS-1002',
            ],
            [
                'user' => $users['customer_family'],
                'category_slug' => 'travel-guidance',
                'subject' => 'Schengen travel documentation readiness audit',
                'description' => 'Applicant wants a document-readiness review before paying for full visa advisory support.',
                'preferred_date' => now()->addDays(8)->toDateString(),
                'assigned_consultant_id' => $users['consultant_manager']->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'fee' => 18000,
                'admin_note' => 'Awaiting initial consultation fee and passport bio-data page upload.',
                'reference' => 'CON-OPS-1003',
            ],
        ];

        foreach ($definitions as $definition) {
            $category = $categories->get($definition['category_slug']);

            if (! $category) {
                continue;
            }

            $request = ConsultancyRequest::updateOrCreate(
                ['user_id' => $definition['user']->id, 'subject' => $definition['subject']],
                [
                    'category_id' => $category->id,
                    'description' => $definition['description'],
                    'preferred_date' => $definition['preferred_date'],
                    'assigned_consultant_id' => $definition['assigned_consultant_id'],
                    'status' => $definition['status'],
                    'payment_status' => $definition['payment_status'],
                    'fee' => $definition['fee'],
                    'admin_note' => $definition['admin_note'],
                ]
            );

            $this->upsertPayment($request, $definition['user'], $definition['reference'], [
                'amount' => $definition['fee'],
                'payment_method' => 'transfer',
                'gateway' => 'manual',
                'status' => $definition['payment_status'],
                'paid_at' => $definition['payment_status'] === 'paid' ? now()->subDays(3) : null,
                'meta' => [
                    'type' => 'consultancy_fee',
                    'seeded_by' => self::class,
                    'subject' => $request->subject,
                ],
            ]);
        }
    }

    private function seedBookings(array $users): void
    {
        $definitions = [
            [
                'user' => $users['customer_primary'],
                'booking_type' => 'hotel',
                'title' => 'Lagos business stay at Radisson Blu',
                'location' => 'Victoria Island, Lagos',
                'check_in_date' => now()->addDays(10)->toDateString(),
                'check_out_date' => now()->addDays(13)->toDateString(),
                'persons' => 2,
                'details' => 'Airport pickup, executive room, and late checkout requested for business travel.',
                'status' => 'processing',
                'payment_status' => 'paid',
                'amount' => 85000,
                'confirmation_code' => 'HTL-OPS-1001',
                'admin_note' => 'Partner hotel acknowledged booking window and room hold is active.',
                'reference' => 'BKG-OPS-1001',
            ],
            [
                'user' => $users['customer_family'],
                'booking_type' => 'flight',
                'title' => 'Abuja to Kigali return flight support',
                'location' => 'Nnamdi Azikiwe International Airport',
                'travel_date' => now()->addDays(18)->toDateString(),
                'persons' => 1,
                'details' => 'Customer needs best fare comparison with baggage and change-flexibility options.',
                'status' => 'reviewing',
                'payment_status' => 'under_review',
                'amount' => 25000,
                'confirmation_code' => 'FLT-OPS-1002',
                'admin_note' => 'Reservation desk is holding quoted fare while finance reviews transfer proof.',
                'reference' => 'BKG-OPS-1002',
            ],
            [
                'user' => $users['customer_business'],
                'booking_type' => 'resort',
                'title' => 'Executive team retreat at Ibom Icon Resort',
                'location' => 'Uyo, Akwa Ibom',
                'check_in_date' => now()->addDays(25)->toDateString(),
                'check_out_date' => now()->addDays(27)->toDateString(),
                'persons' => 6,
                'details' => 'Boardroom access, airport transfer, and buffet package required for team retreat.',
                'status' => 'pending',
                'payment_status' => 'pending',
                'amount' => 40000,
                'confirmation_code' => 'RST-OPS-1003',
                'admin_note' => 'Awaiting deposit confirmation before releasing partner availability request.',
                'reference' => 'BKG-OPS-1003',
            ],
        ];

        foreach ($definitions as $definition) {
            $booking = Booking::updateOrCreate(
                ['user_id' => $definition['user']->id, 'title' => $definition['title']],
                [
                    'booking_type' => $definition['booking_type'],
                    'location' => $definition['location'],
                    'check_in_date' => $definition['check_in_date'] ?? null,
                    'check_out_date' => $definition['check_out_date'] ?? null,
                    'travel_date' => $definition['travel_date'] ?? null,
                    'persons' => $definition['persons'],
                    'details' => $definition['details'],
                    'status' => $definition['status'],
                    'payment_status' => $definition['payment_status'],
                    'amount' => $definition['amount'],
                    'confirmation_code' => $definition['confirmation_code'],
                    'admin_note' => $definition['admin_note'],
                ]
            );

            $this->upsertPayment($booking, $definition['user'], $definition['reference'], [
                'amount' => $definition['amount'],
                'payment_method' => 'card',
                'gateway' => $definition['payment_status'] === 'paid' ? 'paystack' : 'manual',
                'status' => $definition['payment_status'],
                'paid_at' => $definition['payment_status'] === 'paid' ? now()->subDays(2) : null,
                'meta' => [
                    'type' => 'booking_service_fee',
                    'seeded_by' => self::class,
                    'booking_type' => $booking->booking_type,
                ],
            ]);
        }
    }

    private function seedEmergencyRequests(array $users): void
    {
        $lagosUnit = EmergencyServiceUnit::query()
            ->where('state_name', 'Lagos')
            ->where('is_national', false)
            ->first();

        $abujaUnit = EmergencyServiceUnit::query()
            ->where(function ($query) {
                $query->where('state_name', 'FCT')
                    ->orWhere('state_name', 'Federal Capital Territory');
            })
            ->where('is_national', false)
            ->first();

        $definitions = [
            [
                'user' => $users['customer_primary'],
                'emergency_type' => 'medical',
                'full_name' => $users['customer_primary']->name,
                'phone' => $users['customer_primary']->phone,
                'alternate_phone' => $users['customer_primary']->alternate_phone,
                'location_text' => 'Lekki Phase 1, Admiralty Way, Lagos',
                'state_code' => 'LAGOS',
                'state_name' => 'Lagos',
                'local_government_area' => 'Eti Osa',
                'latitude' => 6.4474,
                'longitude' => 3.4698,
                'description' => 'Patient collapsed at a family home; relatives requested urgent responder support and nearest medical contact.',
                'status' => 'responding',
                'assigned_unit' => $lagosUnit?->unit_name ?? 'FRSC Lagos Sector Command',
                'assigned_unit_id' => $lagosUnit?->id,
                'assigned_unit_contact' => $lagosUnit?->contact_phone ?? '08077690201',
                'assigned_unit_toll_free' => $lagosUnit?->toll_free_line ?? '122',
                'dispatch_reference' => 'EMG-OPS-1001',
                'assigned_at' => now()->subMinutes(18),
                'last_tracked_at' => now()->subMinutes(4),
                'response_note' => 'Nearest responding unit is approaching the Lekki corridor and caller remains on standby.',
                'tracking_events' => [
                    ['status' => 'received', 'location_label' => 'Emergency desk intake', 'note' => 'Call received, patient condition logged, and contact verification completed.', 'event_time' => now()->subMinutes(20)],
                    ['status' => 'unit_dispatched', 'location_label' => 'Lagos sector command dispatch lane', 'latitude' => 6.6018, 'longitude' => 3.3515, 'eta_minutes' => 22, 'note' => 'Responder unit acknowledged dispatch and departed toward Lekki.', 'event_time' => now()->subMinutes(14)],
                    ['status' => 'en_route', 'location_label' => 'Lekki-Epe express corridor', 'latitude' => 6.4542, 'longitude' => 3.4916, 'eta_minutes' => 7, 'note' => 'Unit is closing in on the destination with live line open to the caller.', 'event_time' => now()->subMinutes(4)],
                ],
            ],
            [
                'user' => $users['customer_family'],
                'emergency_type' => 'accident',
                'full_name' => $users['customer_family']->name,
                'phone' => $users['customer_family']->phone,
                'alternate_phone' => $users['customer_family']->alternate_phone,
                'location_text' => 'Ahmadu Bello Way, Central Area, Abuja',
                'state_code' => 'FCT',
                'state_name' => 'Federal Capital Territory',
                'local_government_area' => 'Municipal Area Council',
                'latitude' => 9.0579,
                'longitude' => 7.4951,
                'description' => 'Minor road traffic collision reported with injured passengers already stabilized and evacuated.',
                'status' => 'resolved',
                'assigned_unit' => $abujaUnit?->unit_name ?? 'FRSC FCT Sector Command',
                'assigned_unit_id' => $abujaUnit?->id,
                'assigned_unit_contact' => $abujaUnit?->contact_phone ?? '122',
                'assigned_unit_toll_free' => $abujaUnit?->toll_free_line ?? '122',
                'dispatch_reference' => 'EMG-OPS-1002',
                'assigned_at' => now()->subHours(6),
                'last_tracked_at' => now()->subHours(4),
                'resolved_at' => now()->subHours(4),
                'response_note' => 'Incident resolved and vehicles cleared from the carriageway.',
                'tracking_events' => [
                    ['status' => 'received', 'location_label' => 'Emergency dispatch line', 'note' => 'Crash details confirmed and road-safety unit alerted.', 'event_time' => now()->subHours(6)],
                    ['status' => 'unit_dispatched', 'location_label' => 'FCT command route desk', 'latitude' => 9.0765, 'longitude' => 7.3986, 'eta_minutes' => 18, 'note' => 'Road-safety response team dispatched to the collision scene.', 'event_time' => now()->subHours(5)->subMinutes(45)],
                    ['status' => 'on_scene', 'location_label' => 'Central Area, Abuja', 'latitude' => 9.0579, 'longitude' => 7.4951, 'eta_minutes' => 0, 'note' => 'Responders arrived, secured the scene, and coordinated with traffic control.', 'event_time' => now()->subHours(5)->subMinutes(20)],
                    ['status' => 'resolved', 'location_label' => 'Central Area, Abuja', 'latitude' => 9.0579, 'longitude' => 7.4951, 'eta_minutes' => 0, 'note' => 'Passengers moved for treatment and roadway restored to normal flow.', 'event_time' => now()->subHours(4)],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $request = EmergencyRequest::updateOrCreate(
                [
                    'user_id' => $definition['user']->id,
                    'dispatch_reference' => $definition['dispatch_reference'],
                ],
                [
                    'country_code' => 'NG',
                    'country_name' => 'Nigeria',
                    'emergency_type' => $definition['emergency_type'],
                    'full_name' => $definition['full_name'],
                    'phone' => $definition['phone'],
                    'alternate_phone' => $definition['alternate_phone'],
                    'location_text' => $definition['location_text'],
                    'state_code' => $definition['state_code'],
                    'state_name' => $definition['state_name'],
                    'local_government_area' => $definition['local_government_area'],
                    'latitude' => $definition['latitude'],
                    'longitude' => $definition['longitude'],
                    'description' => $definition['description'],
                    'status' => $definition['status'],
                    'assigned_unit' => $definition['assigned_unit'],
                    'assigned_unit_id' => $definition['assigned_unit_id'],
                    'assigned_unit_contact' => $definition['assigned_unit_contact'],
                    'assigned_unit_toll_free' => $definition['assigned_unit_toll_free'],
                    'dispatch_reference' => $definition['dispatch_reference'],
                    'assigned_at' => $definition['assigned_at'],
                    'last_tracked_at' => $definition['last_tracked_at'],
                    'resolved_at' => $definition['resolved_at'] ?? null,
                    'response_note' => $definition['response_note'],
                ]
            );

            $request->trackingEvents()->delete();
            $request->trackingEvents()->createMany(
                collect($definition['tracking_events'])
                    ->map(fn (array $event) => array_merge($event, [
                        'emergency_service_unit_id' => $definition['assigned_unit_id'],
                        'meta' => [
                            'updated_by' => $users['emergency_desk']->id,
                            'seeded_by' => self::class,
                        ],
                    ]))
                    ->all()
            );
        }
    }

    private function syncOrderLineItems(Order $order, array $items): void
    {
        $order->items()->delete();

        foreach ($items as $item) {
            $orderItem = $order->items()->create([
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
                'fulfillment_status' => $item['fulfillment_status'],
                'logistics_partner' => $item['logistics_partner'] ?? null,
                'tracking_number' => $item['tracking_number'] ?? null,
                'tracking_url' => $item['tracking_url'] ?? null,
                'last_tracked_at' => $item['last_tracked_at'] ?? null,
                'shipped_at' => $item['shipped_at'] ?? null,
                'delivered_at' => $item['delivered_at'] ?? null,
                'meta' => array_merge($item['meta'] ?? [], [
                    'seeded_by' => self::class,
                ]),
            ]);

            $orderItem->trackingEvents()->createMany(
                collect($item['events'] ?? [])
                    ->map(fn (array $event) => array_merge($event, [
                        'meta' => array_merge($event['meta'] ?? [], [
                            'seeded_by' => self::class,
                        ]),
                    ]))
                    ->all()
            );
        }
    }

    private function upsertPayment(Model $payable, User $user, string $reference, array $attributes): void
    {
        $payable->payments()->updateOrCreate(
            ['reference' => $reference],
            array_merge([
                'user_id' => $user->id,
                'currency' => config('kiosk.payments.currency', 'NGN'),
                'payer_name' => $user->billing_name ?: $user->name,
                'payer_email' => $user->billing_email ?: $user->email,
                'payer_phone' => $user->billing_phone ?: $user->phone,
                'billing_address' => $user->billingAddressForPayment(),
                'delivery_address_snapshot' => $user->deliveryAddress(),
                'customer_profile_snapshot' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'kyc_status' => $user->kyc_status,
                    'identity_type' => $user->identity_type,
                ],
            ], $attributes)
        );
    }
}
