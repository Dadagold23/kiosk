@extends('layouts.customer')

@section('meta_title', 'Search | Kiosk')
@section('customer_page_title', 'Search')
@section('customer_page_subtitle', $search !== '' ? 'Results for "' . $search . '"' : 'Search across your account activity and storefront items.')

@section('customer_body')
<div class="customer-page-grid">
    <section class="customer-card customer-page-block">
        <span class="customer-eyebrow">Search Desk</span>
        <h2 class="customer-section-title">Customer-wide search</h2>
        <p class="customer-section-copy mb-0">
            @if($search !== '')
                Found {{ $totalMatches }} quick result{{ $totalMatches === 1 ? '' : 's' }} across your account and the storefront.
            @else
                Use the top search bar to look through your orders, requests, bookings, emergency records, wishlist, and product matches.
            @endif
        </p>
    </section>

    @if($search !== '')
        @php
            $sections = [
                ['key' => 'orders', 'title' => 'Orders', 'empty' => 'No orders matched this search.'], ['key' => 'services', 'title' => 'Service Requests', 'empty' => 'No service requests matched this search.'], ['key' => 'consultancies', 'title' => 'Consultancy Requests', 'empty' => 'No consultancy requests matched this search.'], ['key' => 'bookings', 'title' => 'Bookings', 'empty' => 'No bookings matched this search.'], ['key' => 'emergencies', 'title' => 'Emergencies', 'empty' => 'No emergency records matched this search.'], ['key' => 'wishlist', 'title' => 'Wishlist', 'empty' => 'No wishlist items matched this search.'], ['key' => 'products', 'title' => 'Storefront Products', 'empty' => 'No product names matched this search.'],
            ];
        @endphp

        <div class="row g-4">
            @foreach($sections as $section)
                <div class="col-xl-6">
                    <section class="customer-card customer-page-block h-100">
                        <div class="customer-panel-head">
                            <div>
                                <span class="customer-eyebrow">{{ $section['title'] }}</span>
                                <h3 class="customer-section-title">{{ $section['title'] }}</h3>
                            </div>
                        </div>

                        <div class="customer-page-grid">
                            @forelse($results[$section['key']] as $item)
                                <div class="customer-info-card">
                                    @if($section['key'] === 'orders')
                                        <span class="label"><a href="{{ route('orders.show', $item) }}">{{ $item->order_no }}</a></span>
                                        <div class="value">{{ ucfirst(str_replace('_', ' ', $item->order_status)) }} | {{ ucfirst(str_replace('_', ' ', $item->payment_status)) }}</div>
                                    @elseif($section['key'] === 'services')
                                        <span class="label"><a href="{{ route('customer.services.show', $item) }}">{{ $item->title }}</a></span>
                                        <div class="value">{{ $item->category?->name ?: 'Service request' }} | {{ ucfirst(str_replace('_', ' ', $item->status)) }}</div>
                                    @elseif($section['key'] === 'consultancies')
                                        <span class="label"><a href="{{ route('customer.consultancy.show', $item) }}">{{ $item->subject }}</a></span>
                                        <div class="value">{{ $item->category?->name ?: 'Consultancy request' }} | {{ ucfirst(str_replace('_', ' ', $item->status)) }}</div>
                                    @elseif($section['key'] === 'bookings')
                                        <span class="label"><a href="{{ route('customer.bookings.show', $item) }}">{{ $item->title ?: ucfirst($item->booking_type) }}</a></span>
                                        <div class="value">{{ ucfirst($item->booking_type) }} | {{ ucfirst(str_replace('_', ' ', $item->status)) }}</div>
                                    @elseif($section['key'] === 'emergencies')
                                        <span class="label"><a href="{{ route('customer.emergency.show', $item) }}">{{ ucfirst(str_replace('_', ' ', $item->emergency_type)) }}</a></span>
                                        <div class="value">{{ $item->location_text ?: 'No location provided' }}</div>
                                    @elseif($section['key'] === 'wishlist')
                                        @if($item->product)
                                            <span class="label"><a href="{{ route('shop.show', $item->product->slug) }}">{{ $item->product->name }}</a></span>
                                            <div class="value">{{ $item->product->category?->name ?: 'Saved item' }}</div>
                                        @else
                                            <span class="label">Saved item</span>
                                            <div class="value">This product is no longer available in the storefront.</div>
                                        @endif
                                    @else
                                        <span class="label"><a href="{{ route('shop.show', $item->slug) }}">{{ $item->name }}</a></span>
                                        <div class="value">{{ $item->category?->name ?: 'Storefront product' }}</div>
                                    @endif
                                </div>
                            @empty
                                <div class="customer-panel-note">{{ $section['empty'] }}</div>
                            @endforelse
                        </div>
                    </section>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
