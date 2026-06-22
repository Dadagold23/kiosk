@extends('layouts.admin')

@section('meta_title', 'Edit User | Kiosk')
@section('page_title', 'Edit User')
@section('page_subtitle', 'Update account details and role assignment without leaving the new admin workflow')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Accounts</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit: {{ $user->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary radius-30"><i class="bx bx-arrow-back me-1"></i>Back to Users</a>
    </div>
</div>
<!--end breadcrumb-->

<!-- Edit Profile Info Card -->
<div class="card radius-10 border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="mb-1 fw-bold text-dark">Identity Management</h5>
        <p class="text-muted small mb-4">Adjust profile, contact information, and role access from a single form.</p>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control radius-30" required>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control radius-30" required>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control radius-30">
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Role</label>
                <select name="role" class="form-select radius-30">
                    <option value="">Select role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label small text-secondary fw-bold text-uppercase">Address</label>
                <textarea name="address" rows="3" class="form-control radius-15">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">KYC Status</label>
                <select name="kyc_status" class="form-select radius-30">
                    <option value="">Not submitted</option>
                    @foreach(['pending' => 'Pending Review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'requires_review' => 'Requires Review'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('kyc_status', $user->kyc_status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Identity Type</label>
                <select name="identity_type" class="form-select radius-30">
                    <option value="">Select identity type</option>
                    @foreach(['nin' => 'NIN', 'national_id' => 'National ID', 'drivers_license' => 'Driver\'s License', 'international_passport' => 'International Passport', 'voters_card' => 'Voter\'s Card', 'residence_permit' => 'Residence Permit', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('identity_type', $user->identity_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Identity Country</label>
                <input type="text" name="identity_country" value="{{ old('identity_country', $user->identity_country) }}" class="form-control radius-30">
            </div>

            <div class="col-md-6">
                <label class="form-label small text-secondary fw-bold text-uppercase">Identity Number</label>
                <input type="text" name="identity_number" value="{{ old('identity_number', $user->identity_number) }}" class="form-control radius-30">
            </div>

            <div class="col-12 text-end mt-4">
                <button class="btn btn-primary radius-30 px-4">Update User</button>
            </div>
        </form>
    </div>
</div>

<!-- KYC Verification Logs & Sandbox lookup card -->
<div class="card radius-10 border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Dojah Sandbox Verification</h5>
                <p class="text-muted small mb-0">Run a government ID lookup against Dojah sandbox using the customer identity details currently saved.</p>
                @if(str_contains((string) config('kiosk.kyc.dojah.base_url'), 'sandbox'))
                    <p class="small text-danger mt-1 mb-0"><i class="bx bx-info-circle me-1"></i>NIN lookup uses Dojah's test NIN <strong>70123456789</strong>. Real NIN values often fail in sandbox.</p>
                @endif
            </div>
            <form action="{{ route('admin.users.verify-kyc', $user) }}" method="POST" class="mb-0">
                @csrf
                <button class="btn btn-outline-primary radius-30 px-3">Run Dojah Check</button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-3">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Current KYC</small>
                    <strong class="text-dark">{{ $user->kyc_status ? ucfirst(str_replace('_', ' ', $user->kyc_status)) : 'Not submitted' }}</strong>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Identity Type</small>
                    <strong class="text-dark">{{ $user->identity_type ? ucfirst(str_replace('_', ' ', $user->identity_type)) : 'Missing' }}</strong>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Identity Country</small>
                    <strong class="text-dark">{{ $user->identity_country ?: 'Missing' }}</strong>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="p-3 radius-10 bg-light border shadow-none h-100">
                    <small class="text-muted d-block text-uppercase mb-1">Last Approved At</small>
                    <strong class="text-dark text-truncate small d-block">{{ $user->kyc_approved_at?->format('d M Y, h:i A') ?: 'Not approved' }}</strong>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Run</th>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>Identity</th>
                        <th>Review Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->kycVerifications->take(8) as $verification)
                        @php($payload = $verification->response_payload ?? [])
                        @php($normalized = data_get($payload, 'normalized', []))
                        @php($checks = data_get($payload, 'checks', []))
                        @php($photo = data_get($normalized, 'photo'))
                        @php
                            $vTone = match($verification->status) {
                                'approved', 'verified' => 'text-success bg-light-success',
                                'failed', 'rejected' => 'text-danger bg-light-danger',
                                default => 'text-warning bg-light-warning',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $verification->created_at->format('d M Y') }}</strong>
                                <div class="small text-muted">{{ $verification->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-capitalize">{{ $verification->provider }}</div>
                                <div class="small text-secondary text-capitalize">{{ $verification->environment }}</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $vTone }} p-2 text-uppercase px-3">
                                    <i class="bx bxs-circle align-middle me-1"></i>{{ str_replace('_', ' ', $verification->status) }}
                                </span>
                                @if($checks !== [])
                                    <div class="small text-muted mt-1">
                                        Name match: {{ data_get($checks, 'name_match') ? 'Yes' : 'No' }}
                                        @if(data_get($checks, 'dob_match') !== null)
                                            | DOB match: {{ data_get($checks, 'dob_match') ? 'Yes' : 'No' }}
                                        @endif
                                    </div>
                                @endif
                              </td>
                            <td>
                                @if($photo)
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="data:image/jpeg;base64,{{ $photo }}" alt="KYC photo" class="rounded-3 border" style="width:56px; height:56px; object-fit:cover;">
                                        <div>
                                            <strong class="text-dark d-block" style="font-size: 0.9rem;">{{ data_get($normalized, 'full_name') ?: 'Returned identity' }}</strong>
                                            <small class="text-muted">{{ $verification->identity_number_masked ?: 'Masked ID unavailable' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <strong class="text-dark d-block" style="font-size: 0.9rem;">{{ data_get($normalized, 'full_name') ?: 'No returned name' }}</strong>
                                    <small class="text-muted">{{ $verification->identity_number_masked ?: 'Masked ID unavailable' }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="text-dark" style="font-size: 0.9rem;">{{ $verification->notes ?: 'No notes recorded.' }}</div>
                                @if($verification->checkedBy)
                                    <div class="small text-muted mt-1">Checked by {{ $verification->checkedBy->name }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No KYC verification attempts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
