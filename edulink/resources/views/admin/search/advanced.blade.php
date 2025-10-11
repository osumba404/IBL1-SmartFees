@extends('layouts.admin')

@section('title', 'Advanced Search')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Advanced Search</h1>
            <p class="text-muted">Search students, payments, and transactions</p>
        </div>
    </div>

    <!-- Search Tabs -->
    <ul class="nav nav-tabs mb-4" id="searchTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#students-tab">Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#payments-tab">Payments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#transactions-tab">Transactions</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Students Search -->
        <div class="tab-pane fade show active" id="students-tab">
            <div class="card">
                <div class="card-header">
                    <h5>Student Search</h5>
                </div>
                <div class="card-body">
                    <form id="student-search-form">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="First or last name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student ID</label>
                                <input type="text" name="student_id" class="form-control" placeholder="Student ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email address">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">Search Students</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="student-results" class="mt-4"></div>
        </div>

        <!-- Payments Search -->
        <div class="tab-pane fade" id="payments-tab">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Search & Filters</h5>
                </div>
                <div class="card-body">
                    <form id="payment-search-form">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Student Name</label>
                                <input type="text" name="student_name" class="form-control" placeholder="Student name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">All Methods</option>
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount Range</label>
                                <div class="input-group">
                                    <input type="number" name="amount_min" class="form-control" placeholder="Min">
                                    <input type="number" name="amount_max" class="form-control" placeholder="Max">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">Filter Payments</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="payment-results" class="mt-4"></div>
        </div>

        <!-- Transactions Search -->
        <div class="tab-pane fade" id="transactions-tab">
            <div class="card">
                <div class="card-header">
                    <h5>Transaction Search</h5>
                </div>
                <div class="card-body">
                    <form id="transaction-search-form">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Transaction ID</label>
                                <input type="text" name="transaction_id" class="form-control" placeholder="Transaction ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference</label>
                                <input type="text" name="reference" class="form-control" placeholder="Payment reference">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student</label>
                                <input type="text" name="student_lookup" class="form-control" placeholder="Type student name..." id="student-lookup">
                                <input type="hidden" name="student_id" id="selected-student-id">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Search Transactions</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="transaction-results" class="mt-4"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Student search
    document.getElementById('student-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('{{ route("admin.search.students") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('student-results').innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('student-results').innerHTML = '<div class="alert alert-danger">Error searching students. Please try again.</div>';
        });
    });

    // Payment search
    document.getElementById('payment-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('{{ route("admin.search.payment-filters") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('payment-results').innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('payment-results').innerHTML = '<div class="alert alert-danger">Error searching payments. Please try again.</div>';
        });
    });

    // Transaction search
    document.getElementById('transaction-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('{{ route("admin.search.transactions") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('transaction-results').innerHTML = data.html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('transaction-results').innerHTML = '<div class="alert alert-danger">Error searching transactions. Please try again.</div>';
        });
    });

    // Student lookup
    const studentLookup = document.getElementById('student-lookup');
    let searchTimeout;
    
    studentLookup.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;
        
        if (query.length < 2) return;
        
        searchTimeout = setTimeout(() => {
            fetch(`{{ route("admin.search.student-lookup") }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    // Simple implementation - you can enhance with dropdown
                    if (data.length > 0) {
                        document.getElementById('selected-student-id').value = data[0].id;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300);
    });
});
</script>
@endpush