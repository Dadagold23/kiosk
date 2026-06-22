@extends('layouts.customer')

@section('customer_page_title', 'Consultancy Requests')
@section('customer_page_subtitle', 'Track submitted consultancy cases, consultant assignment, and delivery status.')

@section('customer_body')
@php
    $completedCount = $requests->getCollection()->where('status', 'completed')->count(); $paidCount = $requests->getCollection()->where('payment_status', 'paid')->count();
    $consultancySummary = [
        ['label' => 'Total cases', 'value' => $requests->total()], ['label' => 'Completed', 'value' => $completedCount], ['label' => 'Paid', 'value' => $paidCount], ['label' => 'Latest fee', 'value' => $requests->first() ? 'NGN '.number_format((float) $requests->first()->fee, 2) : 'NGN 0.00'],
    ];
@endphp
<div class="customer-page-grid">
    @include('partials.amerce.account-module-list', [
        'tone' => 'warning',
        'eyebrow' => 'Kiosk Advisory Desk',
        'title' => 'Keep consultancy cases, payment state, and advisory delivery in one view.',
        'description' => 'Review what is pending, what has been paid for, and which cases are ready for consultant follow-up.',
        'summary' => $consultancySummary,
        'listTitle' => 'My Consultancy Requests',
        'listSubtitle' => 'Submitted advisory cases connected to your Kiosk account.',
        'createRoute' => 'customer.consultancy.create',
        'createLabel' => 'New Consultancy Request',
        'records' => $requests,
        'emptyMessage' => 'No consultancy requests found.',
        'kickerResolver' => fn ($record) => $record->category?->name ?: 'Consultancy case',
        'titleResolver' => fn ($record) => $record->subject,
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
        'openRouteResolver' => fn ($record) => route('customer.consultancy.show', $record),
    ])

    <div class="customer-pagination">
        {{ $requests->links() }}
    </div>
</div>
@endsection
