@extends('layouts.admin')

@section('meta_title', 'Admin Dashboard | Kiosk')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Sales, operations, and support activity overview')

@section('content')
@php
$summaryCards = [
    [
        'label' => 'Total Sales',
        'value' => '₦' . number_format($stats['total_paid'], 2),
        'copy'  => 'Paid revenue recorded across all platform payments.',
        'icon'  => 'bx bx-dollar',
        'color' => 'bg-light-success text-success',
    ],
    [
        'label' => 'Total Orders',
        'value' => number_format($stats['orders']),
        'copy'  => $stats['pending_orders'] . ' order(s) still awaiting fulfillment.',
        'icon'  => 'bx bx-cart-check',
        'color' => 'bg-light-info text-info',
    ],
    [
        'label' => 'Total Products',
        'value' => number_format($stats['products']),
        'copy'  => 'Active catalog items across storefront listings.',
        'icon'  => 'bx bx-box',
        'color' => 'bg-light-warning text-warning',
    ],
    [
        'label' => 'Open Emergencies',
        'value' => number_format($stats['open_emergencies']),
        'copy'  => 'Active cases currently at the emergency desk.',
        'icon'  => 'bx bx-error-circle',
        'color' => 'bg-light-danger text-danger',
    ],
];
@endphp

{{-- ============================
     ROW 1: KPI Summary Cards
     ============================ --}}
<div class="row row-cols-1 row-cols-md-2 row-cols-xxl-4 g-4 mb-4">
    @foreach($summaryCards as $card)
    <div class="col">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-0 text-secondary">{{ $card['label'] }}</p>
                        <h4 class="my-1 fw-bold">{{ $card['value'] }}</h4>
                        <p class="mb-0 font-13 text-muted">{{ $card['copy'] }}</p>
                    </div>
                    <div class="widgets-icons {{ $card['color'] }} ms-auto rounded-3">
                        <i class="{{ $card['icon'] }} fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ============================
     ROW 2: Chart + Payment Status
     ============================ --}}
<div class="row g-4 mb-4">
    {{-- Performance Line Chart --}}
    <div class="col-12 col-xl-8">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 fw-bold">Sales & Orders Trend</h6>
                        <small class="text-muted">Six-month performance across order volume and paid revenue.</small>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary radius-30">
                            Full Report <i class="bx bx-right-arrow-alt ms-1"></i>
                        </a>
                    </div>
                </div>
                <div style="position:relative;height:280px;">
                    <canvas id="adminPerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Status Doughnut --}}
    <div class="col-12 col-xl-4">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="mb-0 fw-bold">Payment Status Mix</h6>
                    <small class="text-muted">Current distribution of payment states.</small>
                </div>
                <div style="position:relative;height:240px;">
                    <canvas id="adminPaymentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================
     ROW 3: Module Activity + KPI Cards
     ============================ --}}
<div class="row g-4 mb-4">
    {{-- Module Bar Chart --}}
    <div class="col-12 col-xl-5">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="mb-0 fw-bold">Module Activity</h6>
                    <small class="text-muted">Cross-module workload snapshot for operations planning.</small>
                </div>
                <div style="position:relative;height:240px;">
                    <canvas id="adminModuleActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Quick Stats --}}
    <div class="col-12 col-xl-7">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-4 fw-bold">Business Summary</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block mb-1">Pending Payments</small>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['pending_payments']) }}</h5>
                            <small class="text-muted">Transactions needing confirmation.</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block mb-1">Total Users</small>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['users']) }}</h5>
                            <small class="text-muted">Registered customer accounts.</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block mb-1">Total Payments</small>
                            <h5 class="mb-0 fw-bold">{{ $stats['payments'] }}</h5>
                            <small class="text-muted">All recorded payment entries.</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block mb-1">Services + Consultancy</small>
                            <h5 class="mb-0 fw-bold">{{ $stats['services'] + $stats['consultancies'] }}</h5>
                            <small class="text-muted">Combined service-side demand.</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block mb-1">Bookings</small>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['bookings']) }}</h5>
                            <small class="text-muted">Reservation volume managed.</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 radius-10 bg-light">
                            <small class="text-muted d-block mb-1">Emergency Cases</small>
                            <h5 class="mb-0 fw-bold">{{ $stats['emergencies'] }}</h5>
                            <small class="text-muted">Requests logged for response.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================
     ROW 4: Assistant Insights
     ============================ --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <h6 class="mb-1 fw-bold">Admin Assistant Desk</h6>
                        <p class="mb-0 text-muted">{{ $assistantInsights['headline'] }}</p>
                    </div>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                        <i class="bx bx-radar me-1"></i>ETA + Risk Monitor
                    </span>
                </div>

                <div class="row g-4">
                    {{-- Orders Needing Attention --}}
                    <div class="col-12 col-lg-6">
                        <h6 class="mb-3 text-muted fw-semibold" style="font-size:.8rem;letter-spacing:.07em;text-transform:uppercase;">Orders Needing Attention</h6>
                        @forelse($assistantInsights['critical_orders'] as $insight)
                        <div class="card radius-10 border mb-3">
                            <div class="card-body py-3 px-3">
                                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $insight['reference'] }}</div>
                                        <div class="small text-muted">{{ $insight['customer_name'] ?: 'Customer pending' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill {{ $insight['risk_level'] === 'high' ? 'bg-danger' : ($insight['risk_level'] === 'medium' ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ ucfirst($insight['risk_level']) }} risk
                                        </span>
                                        <div class="small text-muted mt-1">ETA: {{ $insight['eta_label'] }}</div>
                                    </div>
                                </div>
                                <p class="mb-1 small">{{ $insight['summary'] }}</p>
                                <p class="mb-0 small text-muted"><strong>Next:</strong> {{ $insight['next_action'] }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-success border-0 radius-10 py-2">
                            <i class="bx bx-check-circle me-2"></i>No order escalations. ETA coverage looks healthy.
                        </div>
                        @endforelse
                    </div>

                    {{-- Services Needing Attention --}}
                    <div class="col-12 col-lg-6">
                        <h6 class="mb-3 text-muted fw-semibold" style="font-size:.8rem;letter-spacing:.07em;text-transform:uppercase;">Services Needing Attention</h6>
                        @forelse($assistantInsights['critical_services'] as $insight)
                        <div class="card radius-10 border mb-3">
                            <div class="card-body py-3 px-3">
                                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $insight['reference'] }}</div>
                                        <div class="small text-muted">{{ $insight['title'] }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill {{ $insight['risk_level'] === 'high' ? 'bg-danger' : ($insight['risk_level'] === 'medium' ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ ucfirst($insight['risk_level']) }} risk
                                        </span>
                                        <div class="small text-muted mt-1">ETA: {{ $insight['eta_label'] }}</div>
                                    </div>
                                </div>
                                <p class="mb-1 small">{{ $insight['summary'] }}</p>
                                <p class="mb-0 small text-muted"><strong>Next:</strong> {{ $insight['next_action'] }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-success border-0 radius-10 py-2">
                            <i class="bx bx-check-circle me-2"></i>No service escalations. Teams are within ETA windows.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================
     ROW 5: Recent Orders + Payments
     ============================ --}}
<div class="row g-4 mb-4">
    {{-- Recent Orders --}}
    <div class="col-12 col-lg-6">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <h6 class="mb-0 fw-bold">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary ms-auto radius-30">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Order #</th>
                                <th style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Customer</th>
                                <th style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Status</th>
                                <th class="text-end" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><span class="fw-semibold">{{ $order->order_no }}</span></td>
                                <td class="text-muted small">{{ $order->user?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if(in_array($order->order_status, ['delivered','completed'])) bg-light-success text-success
                                        @elseif(in_array($order->order_status, ['pending','processing'])) bg-light-warning text-warning
                                        @else bg-light-secondary text-secondary @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold">&#8358;{{ number_format($order->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="col-12 col-lg-6">
        <div class="card radius-10 h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <h6 class="mb-0 fw-bold">Recent Payments</h6>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary ms-auto radius-30">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Reference</th>
                                <th style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">User</th>
                                <th style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Status</th>
                                <th class="text-end" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td><span class="fw-semibold">{{ $payment->reference }}</span></td>
                                <td class="text-muted small">{{ $payment->user?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($payment->status === 'paid') bg-light-success text-success
                                        @elseif($payment->status === 'pending') bg-light-warning text-warning
                                        @else bg-light-danger text-danger @endif">
                                        {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold">&#8358;{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================
     ROW 6: Emergency Cards
     ============================ --}}
<div class="row g-4">
    <div class="col-12">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div>
                        <h6 class="mb-0 fw-bold">Recent Emergencies</h6>
                        <small class="text-muted">Fast-glance cards for high-sensitivity support requests.</small>
                    </div>
                    <a href="{{ route('admin.emergency.index') }}" class="btn btn-sm btn-outline-danger ms-auto radius-30">Emergency Desk</a>
                </div>

                <div class="row g-3">
                    @forelse($recentEmergencies as $item)
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card radius-10 border border-danger border-opacity-25 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="widgets-icons bg-light-danger text-danger rounded-2" style="width:36px;height:36px;min-width:36px;">
                                        <i class='bx bx-error fs-5'></i>
                                    </div>
                                    <strong class="small">{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}</strong>
                                </div>
                                <div class="small text-muted mb-1">{{ $item->phone }}</div>
                                <div class="small mb-2">{{ $item->location_text ?: 'No location provided' }}</div>
                                <span class="badge rounded-pill bg-light-danger text-danger">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-success border-0 radius-10 mb-0">
                            <i class="bx bx-check-shield me-2"></i>No emergency records at this time.
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(() => {
    const labels              = @json($analytics['labels']);
    const orders              = @json($analytics['orders']);
    const revenue             = @json($analytics['revenue']);
    const paymentStatusLabels = @json($analytics['payment_status']['labels']);
    const paymentStatusData   = @json($analytics['payment_status']['data']);
    const moduleLabels        = @json($analytics['module_activity']['labels']);
    const moduleData          = @json($analytics['module_activity']['data']);

    const gridColor   = 'rgba(0,0,0,0.06)';
    const inkColor    = '#444';
    const mutedColor  = '#888';
    const accentGreen = '#13c296';
    const accentBlue  = '#0d6efd';

    // Performance line chart
    new Chart(document.getElementById('adminPerformanceChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Orders',
                    data: orders,
                    borderColor: accentGreen,
                    backgroundColor: 'rgba(19,194,150,0.10)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: accentGreen,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.38,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue (₦)',
                    data: revenue,
                    borderColor: accentBlue,
                    backgroundColor: 'rgba(13,110,253,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: accentBlue,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.38,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                x:  { grid: { display: false }, ticks: { color: mutedColor } },
                y:  { beginAtZero: true, grid: { color: gridColor }, ticks: { color: mutedColor } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: mutedColor } }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: inkColor, usePointStyle: true, boxWidth: 10, padding: 16 }
                }
            }
        }
    });

    // Payment status doughnut
    new Chart(document.getElementById('adminPaymentStatusChart'), {
        type: 'doughnut',
        data: {
            labels: paymentStatusLabels,
            datasets: [{
                data: paymentStatusData,
                backgroundColor: ['#13c296','#0d6efd','#ffc107','#dc3545','#6c757d'],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: inkColor, usePointStyle: true, boxWidth: 10, padding: 14 }
                }
            }
        }
    });

    // Module activity bar
    new Chart(document.getElementById('adminModuleActivityChart'), {
        type: 'bar',
        data: {
            labels: moduleLabels,
            datasets: [{
                label: 'Records',
                data: moduleData,
                backgroundColor: ['#13c296','#0d6efd','#ffc107','#dc3545','#6c757d'],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false }, ticks: { color: mutedColor } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: mutedColor } }
            },
            plugins: { legend: { display: false } }
        }
    });
})();
</script>
@endpush
