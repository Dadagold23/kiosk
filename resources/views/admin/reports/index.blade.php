@extends('layouts.admin')

@section('meta_title', 'Reports & Analytics | Kiosk Admin')
@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Operational, financial, and module performance insights in the same updated dashboard language.')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Analytics</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports Desk</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<!-- Top KPIs Row -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Users</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $stats['users'] }}</h4>
                        <p class="mb-0 font-13 text-muted">Registered accounts</p>
                    </div>
                    <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                        <i class="bx bx-user fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Products</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $stats['products'] }}</h4>
                        <p class="mb-0 font-13 text-muted">Catalog items</p>
                    </div>
                    <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                        <i class="bx bx-box fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Payments</p>
                        <h4 class="my-1 fw-bold text-dark">{{ $stats['payments'] }}</h4>
                        <p class="mb-0 font-13 text-muted">Logged transactions</p>
                    </div>
                    <div class="widgets-icons bg-light-warning text-warning ms-auto rounded-3">
                        <i class="bx bx-receipt fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-0 shadow-sm mb-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Revenue Logged</p>
                        <h4 class="my-1 fw-bold text-dark text-nowrap" style="font-size: 1.15rem;">₦{{ number_format($stats['total_paid'], 2) }}</h4>
                        <p class="mb-0 font-13 text-muted">Settled payment value</p>
                    </div>
                    <div class="widgets-icons bg-light-danger text-danger ms-auto rounded-3">
                        <i class="bx bx-wallet fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Line chart -->
    <div class="col-xl-8">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">Revenue vs Order Trend</h5>
                        <p class="text-muted small mb-0">Six-month operational movement for finance and throughput.</p>
                    </div>
                    <span class="badge rounded-pill text-info bg-light-info text-uppercase px-3 py-2">Rolling 6 Months</span>
                </div>
                <div style="height: 320px; position: relative;">
                    <canvas id="reportRevenueOrdersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Doughnut chart -->
    <div class="col-xl-4">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Payment Distribution</h5>
                <p class="text-muted small mb-4">Where payment records currently stand.</p>
                <div style="height: 320px; position: relative;">
                    <canvas id="reportPaymentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Bar chart -->
    <div class="col-lg-5">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Platform Activity</h5>
                <p class="text-muted small mb-4">Relative activity across platform modules.</p>
                <div style="height: 300px; position: relative;">
                    <canvas id="reportModuleChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Business snapshot -->
    <div class="col-lg-7">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Business Snapshot</h5>
                <div class="row row-cols-1 row-cols-sm-2 g-3">
                    <div class="col">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Paid Payments</small>
                            <h4 class="my-1 fw-bold text-dark">{{ $stats['paid_payments'] }}</h4>
                            <p class="mb-0 text-secondary small">Successfully settled payment records.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Orders</small>
                            <h4 class="my-1 fw-bold text-dark">{{ $stats['orders'] }}</h4>
                            <p class="mb-0 text-secondary small">All orders currently logged in the system.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Services & Consultancy</small>
                            <h4 class="my-1 fw-bold text-dark">{{ $stats['services'] + $stats['consultancy'] }}</h4>
                            <p class="mb-0 text-secondary small">Combined advisory and service workload.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 radius-10 bg-light border shadow-none h-100">
                            <small class="text-muted d-block text-uppercase mb-1">Emergency Cases</small>
                            <h4 class="my-1 fw-bold text-dark">{{ $stats['emergencies'] }}</h4>
                            <p class="mb-0 text-secondary small">Emergency records that require oversight.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Payments list -->
    <div class="col-lg-6">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Latest Finance Activity</h5>
                <div class="list-group list-group-flush">
                    @forelse($recentPayments as $payment)
                        <div class="list-group-item px-0 py-3 bg-transparent d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark">{{ $payment->reference }}</strong>
                                <div class="small text-muted">{{ $payment->user?->name }} · <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $payment->status) }}</span></div>
                            </div>
                            <div class="fw-bold text-dark">₦{{ number_format($payment->amount, 2) }}</div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No payments yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders list -->
    <div class="col-lg-6">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Latest Order Activity</h5>
                <div class="list-group list-group-flush">
                    @forelse($recentOrders as $order)
                        <div class="list-group-item px-0 py-3 bg-transparent d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark">{{ $order->order_no }}</strong>
                                <div class="small text-muted">{{ $order->user?->name }} · <span class="text-uppercase small fw-bold text-secondary">{{ str_replace('_', ' ', $order->order_status) }}</span></div>
                            </div>
                            <div class="fw-bold text-dark">₦{{ number_format($order->total, 2) }}</div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No orders yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Emergencies Grid -->
    <div class="col-12 mt-4">
        <div class="card radius-10 border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-4 fw-bold text-dark">Emergency Oversight</h5>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                    @forelse($recentEmergencies as $item)
                        <div class="col">
                            <div class="p-3 radius-10 bg-light border shadow-none h-100">
                                <strong class="text-dark d-block mb-1 text-truncate" title="{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}">{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}</strong>
                                <small class="text-muted d-block mb-2">{{ $item->phone }}</small>
                                <span class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3">
                                    <i class="bx bxs-circle align-middle me-1"></i>{{ str_replace('_', ' ', $item->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-4">No emergency records yet.</div>
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
    const labels = @json($analytics['labels']);
    const orders = @json($analytics['orders']);
    const revenue = @json($analytics['revenue']);
    const paymentStatusLabels = @json($analytics['payments_by_status']['labels']);
    const paymentStatusData = @json($analytics['payments_by_status']['data']);
    const moduleLabels = @json($analytics['modules']['labels']);
    const moduleData = @json($analytics['modules']['data']);

    const gridColor = 'rgba(114, 130, 122, 0.12)';
    const inkColor = '#212529';
    const mutedColor = '#6c757d';
    const accentGreen = '#198754';
    const accentBlue = '#0d6efd';

    new Chart(document.getElementById('reportRevenueOrdersChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Orders',
                    data: orders,
                    borderColor: accentBlue,
                    backgroundColor: 'rgba(13, 110, 253, 0.12)',
                    borderWidth: 3,
                    tension: 0.38,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue',
                    data: revenue,
                    borderColor: accentGreen,
                    backgroundColor: 'rgba(25, 135, 84, 0.10)',
                    borderWidth: 3,
                    tension: 0.38,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false }, ticks: { color: mutedColor } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: mutedColor } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: mutedColor } }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: inkColor, usePointStyle: true, boxWidth: 10, padding: 18 }
                }
            }
        }
    });

    new Chart(document.getElementById('reportPaymentStatusChart'), {
        type: 'doughnut',
        data: {
            labels: paymentStatusLabels,
            datasets: [{
                data: paymentStatusData,
                backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6c757d'],
                borderColor: '#ffffff',
                borderWidth: 4,
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
                    labels: { color: inkColor, usePointStyle: true, boxWidth: 10, padding: 16 }
                }
            }
        }
    });

    new Chart(document.getElementById('reportModuleChart'), {
        type: 'bar',
        data: {
            labels: moduleLabels,
            datasets: [{
                label: 'Records',
                data: moduleData,
                backgroundColor: ['#198754', '#0d6efd', '#0dcaf0', '#ffc107', '#dc3545'],
                borderRadius: 16,
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
