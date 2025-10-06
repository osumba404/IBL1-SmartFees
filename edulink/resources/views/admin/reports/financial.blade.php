@extends('layouts.admin')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Financial Reports</h2>
            <p class="page-subtitle">View revenue and financial analytics</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Total Revenue</h5>
                    <h3 class="text-success">KES {{ number_format($stats['total_revenue'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Outstanding Balance</h5>
                    <h3 class="text-warning">KES {{ number_format($stats['total_outstanding'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Monthly Revenue</h5>
                    <h3 class="text-info">KES {{ number_format($stats['monthly_revenue'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Methods</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['payment_methods'] as $method)
                                <tr>
                                    <td>{{ ucfirst($method->payment_method) }}</td>
                                    <td>KES {{ number_format($method->total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No payment methods found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Export Options</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.export.financial') }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>Export Financial Summary
                        </a>
                        <a href="{{ route('admin.reports.export.payments') }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Export Payment Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection