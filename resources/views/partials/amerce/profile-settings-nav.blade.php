@php
    $sections = [
        ['target' => '#profile-overview', 'label' => 'Overview'], ['target' => '#personal-information', 'label' => 'Personal'], ['target' => '#delivery-profile', 'label' => 'Delivery'], ['target' => '#billing-profile', 'label' => 'Billing'], ['target' => '#kyc-profile', 'label' => 'KYC'],
    ];
@endphp

<div class="d-flex flex-wrap gap-2 mb-4">
    @foreach($sections as $section)
        <a href="{{ $section['target'] }}" class="customer-soft-button">{{ $section['label'] }}</a>
    @endforeach
</div>
