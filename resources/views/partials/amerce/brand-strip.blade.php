<section class="kiosk-brand-strip">
    <div class="row g-4 align-items-center">
        @foreach($brands as $brand)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="rounded-4 border bg-white p-4 text-center h-100">
                    @if(!empty($brand['image']))
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-4 mb-3" style="min-height:84px;padding:1rem;">
                            <img src="{{ $brand['image'] }}" alt="{{ $brand['name'] }}" style="max-height:38px;max-width:100%;object-fit:contain;">
                        </div>
                    @endif
                    <strong class="d-block mb-2" style="color:var(--kiosk-ink);font-family:'Space Grotesk',sans-serif;font-size:.95rem;">{{ $brand['name'] }}</strong>
                    <span class="text-muted small d-block">{{ $brand['meta'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
