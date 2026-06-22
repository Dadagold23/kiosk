@extends('layouts.admin')

@section('meta_title', 'Activity Logs | Kiosk')
@section('page_title', 'Activity Logs')
@section('page_subtitle', 'Monitor important admin and system actions with a cleaner operations ledger')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">System</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<div class="card">
    <div class="card-body">
        <div class="d-lg-flex align-items-center mb-4 gap-3">
            <form method="GET" class="d-flex align-items-center flex-grow-1 row g-2">
                <div class="col-md-6 position-relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 radius-30" placeholder="Search action, description, user">
                    <span class="position-absolute top-50 translate-middle-y ms-3"><i class="bx bx-search"></i></span>
                </div>
                <div class="col-md-4">
                    <select name="action" class="form-select radius-30">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary radius-30 w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Action</th>
                        <th>User</th>
                        <th>Description</th>
                        <th>Subject</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td><span class="fw-semibold">{{ $log->action }}</span></td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ class_basename($log->subject_type ?? '') }}@if($log->subject_id) #{{ $log->subject_id }}@endif</td>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="mt-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
