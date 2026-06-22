<section class="kiosk-about-stats">
    <div dir="ltr" class="swiper tf-swiper" data-preview="4" data-tablet="3" data-mobile-sm="2" data-mobile="1"
        data-space-lg="28" data-space-md="18" data-space="12" data-pagination="1" data-pagination-sm="2"
        data-pagination-md="3" data-pagination-lg="4">
        <div class="swiper-wrapper">
            @foreach($stats as $stat)
            <div class="swiper-slide">
                <div class="box-why h-100">
                    <p class="h1 fw-medium mb-2">{{ $stat['value'] }}<span>{{ $stat['suffix'] ?? '' }}</span></p>
                    <p class="title h5 fw-medium">{{ $stat['title'] }}</p>
                    <p class="sub cl-text-2 mb-0">{{ $stat['copy'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="sw-dot-default tf-sw-pagination"></div>
    </div>
</section>