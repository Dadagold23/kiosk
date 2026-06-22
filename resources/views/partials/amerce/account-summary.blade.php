<div class="kiosk-account-summary">
    <div class="acount-order_stats mb-4">
        <div dir="ltr" class="swiper tf-swiper" data-preview="3" data-tablet="3" data-mobile-sm="2" data-mobile="1"
            data-space-lg="20" data-space-md="15" data-space="10" data-pagination="1" data-pagination-sm="2"
            data-pagination-md="3" data-pagination-lg="3">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="order-box">
                        <div class="order_info">
                            <p class="info__label cl-text-2">Awaiting Fulfillment</p>
                            <h5 class="info__count type-semibold">{{ $stats['pending_orders'] }}</h5>
                        </div>
                        <div class="order_icon"><i class="icon icon-HourglassMedium"></i></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="order-box">
                        <div class="order_info">
                            <p class="info__label cl-text-2">Pending Payments</p>
                            <h5 class="info__count type-semibold">{{ $stats['pending_payments'] }}</h5>
                        </div>
                        <div class="order_icon"><i class="icon icon-ReceiptX"></i></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="order-box">
                        <div class="order_info">
                            <p class="info__label cl-text-2">Total Orders</p>
                            <h5 class="info__count type-semibold">{{ $stats['orders'] }}</h5>
                        </div>
                        <div class="order_icon"><i class="icon icon-Package"></i></div>
                    </div>
                </div>
            </div>
            <div class="sw-dot-default tf-sw-pagination"></div>
        </div>
    </div>

    <div class="account-my_recent">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="title-case mb-0">Recent Orders</h6>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="overflow-auto">
            <table class="table-my_recent">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Products</th>
                        <th>Pricing</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    @php($firstItem = $order->items->first())
                    <tr class="tb-order-item">
                        <td class="tb-order_code fw-medium">{{ $order->order_no }}</td>
                        <td>
                            <div class="tb-order_product">
                                <a href="{{ route('orders.show', $order) }}" class="img-prd">
                                    <img loading="lazy" width="48" height="48"
                                        src="{{ $firstItem?->image_url ?? asset(config('kiosk.assets.product_placeholder')) }}"
                                        alt="{{ $firstItem?->product_name ?? 'Order item' }}">
                                </a>
                                <div class="infor-prd">
                                    <a href="{{ route('orders.show', $order) }}" class="prd_name link fw-medium lh-24">
                                        {{ $firstItem?->product_name ?? 'Order details' }}
                                    </a>
                                    <p class="prd_type cl-text-2 text-caption-01">
                                        {{ $order->items->count() }} item{{ $order->items->count() === 1 ? '' : 's' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="tb-order_price fw-medium">&#8358;{{ number_format($order->total, 2) }}</td>
                        <td>
                            <div class="tb-order_status text-label">
                                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-muted py-3">No orders yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
