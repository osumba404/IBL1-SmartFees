@extends('layouts.student')

@section('title', 'My Notifications')

@push('styles')
<style>
    .notification-list .list-group-item {
        border-right: 0;
        border-left: 0;
        border-radius: 0;
        padding: 1.25rem 1rem;
        transition: background-color 0.2s ease-in-out;
    }
    .notification-list .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .notification-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.2rem;
        margin-right: 1rem;
    }
    .notification-body {
        flex-grow: 1;
    }
    .notification-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        border: 2px dashed #e0e0e0;
        border-radius: .5rem;
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Notifications</h2>
            <p class="page-subtitle">Stay updated with important alerts about your account and enrollments.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Notifications</h5>
                </div>
                <div class="card-body p-0">
                    <div class="notification-list">
                        @forelse ($notifications as $notification)
                            <div class="list-group-item d-flex align-items-start">
                                @php
                                    $iconClass = 'bi-info-circle';
                                    $iconBg = 'bg-primary';
                                    switch ($notification->notification_type) {
                                        case 'enrollment':
                                            $iconClass = 'bi-person-check';
                                            $iconBg = 'bg-success';
                                            break;
                                        case 'payment_reminder':
                                            $iconClass = 'bi-alarm';
                                            $iconBg = 'bg-warning text-dark';
                                            break;
                                        case 'payment_received':
                                            $iconClass = 'bi-wallet2';
                                            $iconBg = 'bg-info';
                                            break;
                                        case 'payment_failed':
                                            $iconClass = 'bi-exclamation-triangle';
                                            $iconBg = 'bg-danger';
                                            break;
                                    }
                                @endphp
                                <div class="notification-icon text-white {{ $iconBg }}">
                                    <i class="bi {{ $iconClass }}"></i>
                                </div>
                                <div class="notification-body">
                                    <h6 class="mb-1">{{ $notification->title }}</h6>
                                    <p class="mb-1">{{ $notification->message }}</p>
                                    <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state m-4">
                                <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                <h4 class="mt-3">No Notifications Yet</h4>
                                <p class="text-muted">You don't have any notifications at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection