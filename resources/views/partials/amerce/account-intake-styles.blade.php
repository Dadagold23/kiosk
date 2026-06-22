@once
    @push('styles')
    <style>
        .amerce-intake-hero{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.52), transparent 34%),
                linear-gradient(135deg, rgba(255,255,255,.58) 0%, rgba(255,255,255,.18) 100%),
                var(--customer-accent);
            border:1px solid rgba(255,255,255,.55);
            color:var(--customer-ink);
            position:relative;
        }

        .amerce-intake-hero.is-primary{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.52), transparent 34%),
                linear-gradient(135deg, rgba(28, 95, 168, .12) 0%, rgba(17, 61, 115, .05) 100%),
                var(--customer-accent);
        }

        .amerce-intake-hero.is-warning{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.52), transparent 34%),
                linear-gradient(135deg, rgba(241, 191, 99, .18) 0%, rgba(255,255,255,.16) 100%),
                color-mix(in srgb, var(--customer-accent) 78%, #fff0cf 22%);
        }

        .amerce-intake-hero.is-success{
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.52), transparent 34%),
                linear-gradient(135deg, rgba(65, 153, 113, .16) 0%, rgba(255,255,255,.16) 100%),
                color-mix(in srgb, var(--customer-accent) 78%, #e8fff4 22%);
        }

        .amerce-intake-copy{
            color:var(--customer-muted);
            max-width:56ch;
        }

        .amerce-intake-stat{
            background:rgba(255,255,255,.76);
            border:1px solid var(--customer-border);
            border-radius:22px;
            box-shadow:var(--customer-shadow-soft);
            padding:1rem 1.05rem;
        }

        .amerce-intake-stat .label{
            color:var(--customer-muted);
            font-size:.76rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.35rem;
            text-transform:uppercase;
        }

        .amerce-intake-stat .value{
            color:var(--customer-primary-deep);
            font-size:1.05rem;
            font-weight:800;
            letter-spacing:-.02em;
        }

        .amerce-field-block{
            background:var(--customer-surface-soft);
            border:1px solid var(--customer-border);
            border-radius:20px;
            padding:1rem 1.05rem;
        }

        .amerce-field-block .form-label{
            color:var(--customer-muted);
            font-size:.74rem;
            font-weight:800;
            letter-spacing:.08em;
            margin-bottom:.65rem;
            text-transform:uppercase;
        }

        .amerce-field-block .form-control,
        .amerce-field-block .form-select{
            background:#fff;
            border:1px solid var(--customer-border);
            border-radius:16px;
            box-shadow:none;
            color:var(--customer-ink);
            min-height:46px;
            padding:.72rem .9rem;
        }

        .amerce-field-block .form-control:focus,
        .amerce-field-block .form-select:focus{
            border-color:var(--customer-primary);
            box-shadow:0 0 0 .2rem color-mix(in srgb, var(--customer-primary) 16%, transparent);
        }

        .amerce-field-block textarea.form-control{
            min-height:140px;
        }

        .amerce-support-card{
            background:var(--customer-surface-soft);
            border:1px solid var(--customer-border);
            border-radius:22px;
            padding:1.2rem;
        }
    </style>
    @endpush
@endonce
