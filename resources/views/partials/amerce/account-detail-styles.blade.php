@once
    @push('styles')
    <style>
        .muara-detail-hero{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 36%),
                linear-gradient(120deg, #103d73 0%, #1c5fa8 56%, #5e8ec6 100%);
            color:#fff;
            position:relative;
        }

        .muara-detail-hero.is-warning{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 36%),
                linear-gradient(120deg, #76501a 0%, #c28b33 60%, #ddb86b 100%);
        }

        .muara-detail-hero.is-success{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 36%),
                linear-gradient(120deg, #164b5a 0%, #2f7e8f 56%, #6aaebe 100%);
        }

        .muara-detail-hero::after{
            border:1px solid rgba(255,255,255,.14);
            border-radius:22px;
            content:"";
            inset:1rem;
            pointer-events:none;
            position:absolute;
        }

        .muara-detail-hero > *{
            position:relative;
            z-index:1;
        }

        .muara-detail-copy{
            color:rgba(255,255,255,.82);
        }

        .muara-detail-stat{
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.12);
            border-radius:18px;
            padding:1rem;
        }

        .muara-detail-stat .label{
            color:rgba(255,255,255,.7);
            font-size:.76rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.35rem;
            text-transform:uppercase;
        }

        .muara-detail-stat .value{
            color:#fff;
            font-size:1.06rem;
            font-weight:800;
        }

        .muara-panel-card{
            background:var(--customer-surface-soft);
            border:1px solid var(--customer-border);
            border-radius:20px;
            padding:1rem;
        }

        .muara-panel-card .label{
            color:var(--customer-muted);
            font-size:.74rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.35rem;
            text-transform:uppercase;
        }

        .muara-panel-card .value{
            color:var(--customer-ink);
            font-size:.95rem;
            font-weight:700;
            line-height:1.5;
        }

        .muara-note-block{
            background:var(--customer-surface-soft);
            border:1px solid var(--customer-border);
            border-radius:20px;
            padding:1rem;
        }

        .muara-timeline{
            display:grid;
            gap:.9rem;
        }

        .muara-timeline-item{
            background:var(--customer-surface-soft);
            border:1px solid var(--customer-border);
            border-radius:18px;
            padding:1rem;
            position:relative;
        }

        .muara-timeline-item::before{
            background:var(--customer-primary);
            border-radius:999px;
            content:"";
            height:10px;
            left:1rem;
            position:absolute;
            top:1rem;
            width:10px;
        }

        .muara-timeline-item .content{
            padding-left:1rem;
        }

        .muara-side-stack{
            display:grid;
            gap:.85rem;
        }

        .muara-payment-card{
            background:var(--customer-surface-soft);
            border:1px solid var(--customer-border);
            border-radius:18px;
            padding:1rem;
        }
    </style>
    @endpush
@endonce
