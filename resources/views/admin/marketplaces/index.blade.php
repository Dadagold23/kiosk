@extends('layouts.admin')

@section('meta_title', 'Marketplaces | Kiosk Admin')
@section('page_title', 'Marketplaces')
@section('page_subtitle', 'Manage global marketplace feeds, sync runs, and imported products inside the unified admin workspace')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Marketplaces</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Sync Panel</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<!-- Marketplace Sync Control Card -->
<div class="card radius-10 border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="mb-1 fw-bold">Marketplace Sync</h5>
                <p class="text-muted small mb-0">Trigger manual imports, prune stale data, and review the health of connected provider feeds.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.marketplaces.sync') }}" class="row g-3 align-items-center mb-4">
            @csrf
            <div class="col-md-4 col-lg-3">
                <select class="form-select radius-30" name="provider">
                    <option value="">All Providers</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider['key'] }}">{{ $provider['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="seedOnly" name="seed_only">
                    <label class="form-check-label" for="seedOnly">Seed Only</label>
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="pruneMissing" name="prune_missing" checked>
                    <label class="form-check-label" for="pruneMissing">Prune Missing</label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary radius-30 px-4">Run Sync</button>
            </div>
        </form>

        <!-- Stats row inside the block -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mt-2">
            <div class="col">
                <div class="p-3 radius-10 bg-light border shadow-none">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary small text-uppercase">Global Products</p>
                            <h5 class="my-1 fw-bold text-dark">{{ number_format($summary['total_global']) }}</h5>
                        </div>
                        <div class="widgets-icons bg-light-primary text-primary ms-auto rounded-3">
                            <i class="bx bx-shopping-bag fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3 radius-10 bg-light border shadow-none">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary small text-uppercase">Active</p>
                            <h5 class="my-1 fw-bold text-success">{{ number_format($summary['active_global']) }}</h5>
                        </div>
                        <div class="widgets-icons bg-light-success text-success ms-auto rounded-3">
                            <i class="bx bx-check-circle fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3 radius-10 bg-light border shadow-none">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary small text-uppercase">Inactive</p>
                            <h5 class="my-1 fw-bold text-danger">{{ number_format($summary['inactive_global']) }}</h5>
                        </div>
                        <div class="widgets-icons bg-light-danger text-danger ms-auto rounded-3">
                            <i class="bx bx-x-circle fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="p-3 radius-10 bg-light border shadow-none">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary small text-uppercase">Last Sync</p>
                            <h5 class="my-1 fw-bold text-info small text-truncate" title="{{ $summary['last_run']?->started_at?->diffForHumans() ?? 'Never' }}">
                                {{ $summary['last_run']?->started_at?->diffForHumans() ?? 'Never' }}
                            </h5>
                        </div>
                        <div class="widgets-icons bg-light-info text-info ms-auto rounded-3">
                            <i class="bx bx-time fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Marketplace Providers Column -->
    <div class="col-xl-4">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Marketplace Providers</h5>
                <p class="text-muted small mb-4">Toggle import availability per provider without leaving the feed desk.</p>

                <div class="list-group list-group-flush">
                    @forelse($providers as $provider)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <div class="fw-bold text-dark">{{ $provider['label'] }}</div>
                                    <div class="small text-muted text-break" style="font-size: 0.8rem;">
                                        {{ !empty($provider['feed_url']) ? $provider['feed_url'] : 'Seed file source' }}
                                    </div>
                                </div>
                                <div>
                                    @if($provider['enabled'])
                                        <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>Enabled</span>
                                    @else
                                        <span class="badge rounded-pill text-secondary bg-light-secondary p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>Disabled</span>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.marketplaces.providers.toggle', $provider['key']) }}" class="mt-2">
                                @csrf
                                <button class="btn btn-sm {{ $provider['enabled'] ? 'btn-outline-danger' : 'btn-outline-success' }} radius-30 px-3">
                                    {{ $provider['enabled'] ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No marketplace providers configured.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Global Marketplace Products Column -->
    <div class="col-xl-8">
        <div class="card radius-10 border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-1 fw-bold text-dark">Global Marketplace Products</h5>
                <p class="text-muted small mb-4">Review imported catalog items and activate or pause them for storefront use.</p>

                <form class="d-flex align-items-center gap-3 mb-4">
                    <div style="min-width:180px;">
                        <select class="form-select radius-30" name="provider" onchange="this.form.submit()">
                            <option value="">All Providers</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider['key'] }}" @selected(request('provider') === $provider['key'])>{{ $provider['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:180px;">
                        <select class="form-select radius-30" name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Marketplace</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        <div class="small text-muted">{{ $product->sku }}</div>
                                    </td>
                                    <td><span class="text-uppercase small fw-semibold text-secondary">{{ $product->source_marketplace }}</span></td>
                                    <td class="fw-bold text-dark">&#8358;{{ number_format($product->current_price, 2) }}</td>
                                    <td>
                                        @if($product->status)
                                            <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>Active</span>
                                        @else
                                            <span class="badge rounded-pill text-secondary bg-light-secondary p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->external_url)
                                            <a class="btn btn-sm btn-outline-primary radius-30 px-3" href="{{ $product->external_url }}" target="_blank" rel="noopener">View</a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.marketplaces.products.status', $product) }}">
                                            @csrf
                                            @if($product->status)
                                                <input type="hidden" name="action" value="deactivate">
                                                <button class="btn btn-sm btn-outline-danger radius-30 px-3">Deactivate</button>
                                            @else
                                                <input type="hidden" name="action" value="restore">
                                                <button class="btn btn-sm btn-outline-success radius-30 px-3">Restore</button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No global products available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sync Log Card -->
<div class="card radius-10 border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="mb-1 fw-bold text-dark">Sync Log</h5>
        <p class="text-muted small mb-4">Review the outcome of recent ingestion runs and the volume of catalog changes they produced.</p>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)
                        <tr>
                            <td><span class="text-uppercase small fw-bold text-secondary">{{ $run->provider }}</span></td>
                            <td>
                                @if($run->status === 'completed')
                                    <span class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>Completed</span>
                                @elseif($run->status === 'failed')
                                    <span class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>Failed</span>
                                @else
                                    <span class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3"><i class="bx bxs-circle align-middle me-1"></i>{{ ucfirst($run->status) }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $run->items_created }}</td>
                            <td class="fw-semibold">{{ $run->items_updated }}</td>
                            <td>{{ $run->started_at?->format('M d, Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No sync runs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($runs->hasPages())
            <div class="mt-4">
                {{ $runs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
