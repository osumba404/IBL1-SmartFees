@extends('layouts.admin')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Reports Dashboard</h1>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <h2>{{ number_format($totalStudents) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h2>KSh {{ number_format($totalPayments, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Payments</h5>
                    <h2>KSh {{ number_format($pendingPayments, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <h2>{{ number_format($totalCourses) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.reports.financial') }}" class="btn btn-primary mb-2 d-block">Financial Reports</a>
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-info mb-2 d-block">Student Reports</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Revenue ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    @if($monthlyRevenue->count() > 0)
                        @foreach($monthlyRevenue as $month => $revenue)
                            <div class="d-flex justify-content-between">
                                <span>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</span>
                                <span>KSh {{ number_format($revenue, 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No revenue data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection