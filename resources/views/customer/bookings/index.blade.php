@extends('layouts.customer')

@section('customer_page_title', 'Bookings')
@section('customer_page_subtitle', 'Track hotel, flight, lounge, resort, and park booking workflows.')

@section('customer_body')
@php
    $confirmedCount = $bookings->getCollection()->where('status', 'confirmed')->count(); $pendingPaymentCount = $bookings->getCollection()->where('payment_status', 'pending')->count();
    $bookingSummary = [
        ['label' => 'Total bookings', 'value' => $bookings->total()], ['label' => 'Confirmed', 'value' => $confirmedCount], ['label' => 'Pending payment', 'value' => $pendingPaymentCount], ['label' => 'Latest amount', 'value' => $bookings->first() ? 'NGN '.number_format((float) $bookings->first()->amount, 2) : 'NGN 0.00'],
    ];
@endphp
<div class="customer-page-grid">
    @include('partials.amerce.account-module-list', [
        'tone' => 'success',
        'eyebrow' => 'Kiosk Booking Desk',
        'title' => 'Watch trips, stays, and reservation requests from one bookings hub.',
        'description' => 'Follow booking progress, payment readiness, and location details without leaving your customer account flow.',
        'summary' => $bookingSummary,
        'listTitle' => 'My Bookings',
        'listSubtitle' => 'Hotel, flight, lounge, resort, and park workflows tied to your account.',
        'createRoute' => 'customer.bookings.create',
        'createLabel' => 'New Booking',
        'records' => $bookings,
        'emptyMessage' => 'No booking requests found.',
        'kickerResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->booking_type)),
        'titleResolver' => fn ($record) => ($record->customer_name ?: 'Booking request'),
        'dateResolver' => fn ($record) => $record->created_at->format('d M Y, h:i A'),
        'statusResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->status)),
        'paymentResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->payment_status)),
        'statusToneResolver' => fn ($record) => in_array($record->status, ['confirmed', 'completed'], true) ? 'success' : (in_array($record->status, ['cancelled', 'rejected'], true) ? 'danger' : 'primary'),
        'paymentToneResolver' => fn ($record) => $record->payment_status === 'paid' ? 'success' : ($record->payment_status === 'failed' ? 'danger' : 'warning'),
        'metaResolver' => fn ($record) => [
            ['label' => 'Location', 'value' => $record->location ?: 'Not provided'],
            ['label' => 'Amount', 'value' => 'NGN '.number_format((float) $record->amount, 2)],
        ],
        'snippetResolver' => fn ($record) => \Illuminate\Support\Str::limit(trim(collect([$record->notes ?? null, $record->special_requests ?? null, $record->destination ?? null])->filter()->implode(' | ')), 115),
        'openRouteResolver' => fn ($record) => route('customer.bookings.show', $record),
    ])

    <div class="customer-pagination">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
