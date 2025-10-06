@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Reports & Analytics</h2>
            <p class="page-subtitle">Generate and view system reports</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-credit-card fa-3x text-primary mb-3"></i>
                    <h5>Payment Reports</h5>
                    <p class="text-muted">View payment transactions and statistics</p>
                    <a href="{{ route('admin.reports.payments') }}" class="btn btn-primary">View Reports</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fa-3x text-success mb-3"></i>
                    <h5>Student Reports</h5>
                    <p class="text-muted">Student enrollment and status reports</p>
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-success">View Reports</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-book fa-3x text-warning mb-3"></i>
                    <h5>Course Reports</h5>
                    <p class="text-muted">Course enrollment and performance data</p>
                    <a href="{{ route('admin.reports.courses') }}" class="btn btn-warning">View Reports</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up fa-3x text-info mb-3"></i>
                    <h5>Financial Reports</h5>
                    <p class="text-muted">Revenue and financial analytics</p>
                    <a href="{{ route('admin.reports.financial') }}" class="btn btn-info">View Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection