@php
    $tone = $tone ?? 'primary'; $emptyMessage = $emptyMessage ?? 'No records found.';
    $createLabel = $createLabel ?? null;
    $createRoute = $createRoute ?? null;
    $summary = $summary ?? [];
    $records = $records ?? collect();
@endphp

@once
    @push('styles')
    <style>
        .muara-module-hero{
            color:#fff;
            position:relative;
        }

        .muara-module-hero::after{
            border:1px solid rgba(255,255,255,.14);
            border-radius:22px;
            content:"";
            inset:1rem;
            pointer-events:none;
            position:absolute;
        }

        .muara-module-hero > *{
            position:relative;
            z-index:1;
        }

        .muara-module-hero-primary{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 34%),
                linear-gradient(120deg, #103d73 0%, #1c5fa8 56%, #5e8ec6 100%);
        }

        .muara-module-hero-warning{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 34%),
                linear-gradient(120deg, #76501a 0%, #c28b33 60%, #ddb86b 100%);
        }

        .muara-module-hero-success{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 34%),
                linear-gradient(120deg, #164b5a 0%, #2f7e8f 56%, #6aaebe 100%);
        }

        .muara-module-hero-danger{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 34%),
                linear-gradient(120deg, #6d232a 0%, #be4e59 56%, #db8993 100%);
        }

        .muara-module-copy{
            color:rgba(255,255,255,.82);
        }

        .muara-summary-grid{
            display:grid;
            gap:.85rem;
            grid-template-columns:repeat(2, minmax(0, 1fr));
        }

        .muara-summary-card{
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.12);
            border-radius:18px;
            padding:.95rem;
        }

        .muara-summary-label{
            color:rgba(255,255,255,.7);
            font-size:.74rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.35rem;
            text-transform:uppercase;
        }

        .muara-summary-value{
            color:#fff;
            font-size:1.3rem;
            font-weight:800;
        }

        .muara-record-card{
            background:linear-gradient(180deg, #fff 0%, var(--customer-surface-soft) 100%);
            border:1px solid var(--customer-border);
            border-radius:22px;
            box-shadow:var(--customer-shadow-soft);
            padding:1rem;
        }

        .muara-record-card .record-kicker{
            color:var(--customer-muted);
            font-size:.72rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.35rem;
            text-transform:uppercase;
        }

        .muara-record-card .record-title{
            color:var(--customer-ink);
            font-size:1rem;
            font-weight:800;
            margin-bottom:.2rem;
        }

        .muara-record-card .record-date{
            color:var(--customer-muted);
            font-size:.82rem;
            margin-bottom:.75rem;
        }

        .muara-meta-stack{
            display:grid;
            gap:.62rem;
            margin:1rem 0 .85rem;
        }

        .muara-meta-row{
            align-items:center;
            display:flex;
            font-size:.9rem;
            gap:1rem;
            justify-content:space-between;
        }

        .muara-meta-row span{
            color:var(--customer-muted);
        }
    </style>
    @endpush
@endonce

<div class="customer-card customer-page-block muara-module-hero muara-module-hero-{{ $tone }} mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <span class="customer-welcome-chip">{{ $eyebrow }}</span>
            <h2 class="fw-bold mb-2">{{ $title }}</h2>
            <p class="mb-0 muara-module-copy">{{ $description }}</p>
        </div>
        <div class="col-lg-4">
            <div class="muara-summary-grid">
                @foreach($summary as $item)
                    <div class="muara-summary-card">
                        <div class="muara-summary-label">{{ $item['label'] }}</div>
                        <div class="muara-summary-value">{{ $item['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="customer-panel-head mb-4">
    <div>
        <span class="customer-eyebrow">Account Stream</span>
        <h3 class="customer-section-title">{{ $listTitle }}</h3>
        <p class="customer-section-copy">{{ $listSubtitle }}</p>
    </div>
    @if($createRoute && $createLabel)
        <a href="{{ route($createRoute) }}" class="btn customer-btn-primary">{{ $createLabel }}</a>
    @endif
</div>

<div class="row g-4">
    @forelse($records as $record)
        @php($badgeTone = $statusToneResolver($record))
        @php($paymentTone = $paymentToneResolver($record))
        <div class="col-md-6 col-xl-4">
            <article class="muara-record-card h-100">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="record-kicker">{{ $kickerResolver($record) }}</div>
                        <div class="record-title">{{ $titleResolver($record) }}</div>
                        <div class="record-date">{{ $dateResolver($record) }}</div>
                    </div>
                    <a href="{{ $openRouteResolver($record) }}" class="customer-soft-button">Open</a>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="customer-status-pill is-{{ $badgeTone === 'success' ? 'success' : ($badgeTone === 'danger' || $badgeTone === 'secondary' ? 'danger' : 'primary') }}">{{ $statusResolver($record) }}</span>
                    <span class="customer-status-pill is-{{ $paymentTone === 'success' ? 'success' : ($paymentTone === 'danger' ? 'danger' : ($paymentTone === 'light' ? 'primary' : 'warning')) }}">{{ $paymentResolver($record) }}</span>
                </div>

                <div class="muara-meta-stack">
                    @foreach($metaResolver($record) as $meta)
                        <div class="muara-meta-row">
                            <span>{{ $meta['label'] }}</span>
                            <strong>{{ $meta['value'] }}</strong>
                        </div>
                    @endforeach
                </div>

                @if($snippetResolver($record))
                    <p class="text-muted small mb-0">{{ $snippetResolver($record) }}</p>
                @endif
            </article>
        </div>
    @empty
        <div class="col-12">
            <div class="customer-empty">{{ $emptyMessage }}</div>
        </div>
    @endforelse
</div>
