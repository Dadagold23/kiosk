@extends('layouts.customer')

@section('meta_title', 'My Emergency Requests | Kiosk')
@section('customer_page_title', 'Emergency Requests')
@section('customer_page_subtitle', 'Track emergency alerts, assigned units, and live response progress.')

@section('customer_body')
@php
    $resolvedCount = $requests->getCollection()->where('status', 'resolved')->count(); $activeCount = $requests->getCollection()->whereIn('status', ['pending', 'dispatched', 'in_progress', 'en_route'])->count();
    $emergencySummary = [
        ['label' => 'Total alerts', 'value' => $requests->total()], ['label' => 'Active', 'value' => $activeCount], ['label' => 'Resolved', 'value' => $resolvedCount], ['label' => 'Latest state', 'value' => $requests->first()?->state_name ?: 'Nigeria'],
    ];
@endphp
<div class="customer-page-grid">
    @include('partials.amerce.account-module-list', [
        'tone' => 'danger',
        'eyebrow' => 'Kiosk Emergency Desk',
        'title' => 'Track emergency alerts, response assignment, and live field updates from one dashboard.',
        'description' => 'Each alert keeps its state, LGA, assigned responder, and latest movement in a single account workflow.',
        'summary' => $emergencySummary,
        'listTitle' => 'Emergency Response History',
        'listSubtitle' => 'Every submitted alert tied to your Kiosk account.',
        'createRoute' => 'emergency.index',
        'createLabel' => 'New Emergency Alert',
        'records' => $requests,
        'emptyMessage' => 'No emergency requests found.',
        'kickerResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->emergency_type)),
        'titleResolver' => fn ($record) => $record->state_name ?: 'Emergency request',
        'dateResolver' => fn ($record) => $record->created_at->format('d M Y, h:i A'),
        'statusResolver' => fn ($record) => ucfirst(str_replace('_', ' ', $record->status)),
        'paymentResolver' => fn ($record) => $record->latestTrackingEvent ? 'Latest: '.ucfirst(str_replace('_', ' ', $record->latestTrackingEvent->status)) : 'Awaiting field update',
        'statusToneResolver' => fn ($record) => $record->status === 'resolved' ? 'success' : ($record->status === 'cancelled' ? 'secondary' : 'danger'),
        'paymentToneResolver' => fn ($record) => $record->latestTrackingEvent ? 'light' : 'warning',
        'metaResolver' => fn ($record) => [
            ['label' => 'LGA / Location', 'value' => $record->local_government_area ?: ($record->location_text ?: 'Location not provided')],
            ['label' => 'Assigned Unit', 'value' => $record->assigned_unit ?: 'Unassigned'],
        ],
        'snippetResolver' => fn ($record) => $record->assigned_unit_contact ?: 'Awaiting responder contact.',
        'openRouteResolver' => fn ($record) => route('customer.emergency.show', $record),
    ])

    <div class="customer-pagination">
        {{ $requests->links() }}
    </div>
</div>
@endsection
