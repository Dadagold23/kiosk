@extends('layouts.customer')

@section('customer_page_title', 'Service Requests')
@section('customer_page_subtitle', 'Track all service jobs requested through Kiosk.')

@section('customer_body')
@php
    $completedCount = $requests->getCollection()->where('status', 'completed')->count(); $pendingPaymentCount = $requests->getCollection()->where('payment_status', 'pending')->count();
    $serviceSummary = [
        ['label' => 'Total requests', 'value' => $requests->total()], ['label' => 'Completed', 'value' => $completedCount], ['label' => 'Pending payment', 'value' => $pendingPaymentCount], ['label' => 'Latest fee', 'value' => $requests->first() ? 'NGN '.number_format((float) $requests->first()->fee, 2) : 'NGN 0.00'],
    ];
@endphp
<div class="customer-page-grid">
    @include('partials.amerce.account-module-list', [
        'tone' => 'primary',
        'eyebrow' => 'Kiosk Service Desk',
        'title' => 'Track every submitted service job in one account stream.',
        'description' => 'Follow progress, payment state, and technician handoff without jumping between disconnected pages.',
        'summary' => $serviceSummary,
        'listTitle' => 'My Service Requests',
        'listSubtitle' => 'Live service jobs connected to your customer account.',
        'createRoute' => 'customer.services.create',
        'createLabel' => 'New Request',
        'records' => $requests,
        'emptyMessage' => 'No service requests found.',
        'kickerResolver' => fn ($record) => $record->category?->name ?: 'Service request',
        'titleResolver' => fn ($record) => $record->title,
        'dateResolver' => fn ($record) => $record->created_at->format('d M Y, h:i A'),
        'statusResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->status)),
        'paymentResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->payment_status)),
        'statusToneResolver' => fn ($record) => in_array($record->status, ['completed', 'resolved'], true) ? 'success' : (in_array($record->status, ['cancelled', 'rejected'], true) ? 'danger' : 'primary'),
        'paymentToneResolver' => fn ($record) => $record->payment_status === 'paid' ? 'success' : ($record->payment_status === 'failed' ? 'danger' : 'warning'),
        'metaResolver' => fn ($record) => [
            ['label' => 'Category', 'value' => $record->category?->name ?: 'General'],
            ['label' => 'Fee', 'value' => 'NGN '.number_format((float) $record->fee, 2)],
        ],
        'snippetResolver' => fn ($record) => \Illuminate\Support\Str::limit(strip_tags((string) $record->description), 115),
        'openRouteResolver' => fn ($record) => route('customer.services.show', $record),
    ])

    <div class="customer-pagination">
        {{ $requests->links() }}
    </div>
</div>
@endsection
