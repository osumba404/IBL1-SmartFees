@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">System Settings</h1>
    <p class="page-subtitle">Configure system-wide settings and preferences</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Gateway Settings</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>M-Pesa Configuration</h6>
                        <div class="mb-3">
                            <label class="form-label">Consumer Key</label>
                            <input type="text" class="form-control" value="{{ config('services.mpesa.consumer_key', 'Not configured') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shortcode</label>
                            <input type="text" class="form-control" value="{{ config('services.mpesa.shortcode', 'Not configured') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Environment</label>
                            <input type="text" class="form-control" value="{{ config('services.mpesa.sandbox', true) ? 'Sandbox' : 'Production' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Stripe Configuration</h6>
                        <div class="mb-3">
                            <label class="form-label">Publishable Key</label>
                            <input type="text" class="form-control" value="{{ config('services.stripe.key', 'Not configured') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Webhook Endpoint</label>
                            <input type="text" class="form-control" value="{{ route('webhooks.stripe') }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Payment gateway settings are configured via environment variables. Contact your system administrator to modify these settings.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">College Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">College Name</label>
                            <input type="text" class="form-control" value="{{ config('app.college_name', 'Edulink International College Nairobi') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" value="{{ config('app.college_currency', 'KES') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Timezone</label>
                            <input type="text" class="form-control" value="{{ config('app.timezone', 'Africa/Nairobi') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Application Environment</label>
                            <input type="text" class="form-control" value="{{ config('app.env') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Laravel Version</label>
                            <input type="text" class="form-control" value="{{ app()->version() }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PHP Version</label>
                            <input type="text" class="form-control" value="{{ PHP_VERSION }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Database Connection</label>
                            <input type="text" class="form-control" value="{{ config('database.default') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cache Driver</label>
                            <input type="text" class="form-control" value="{{ config('cache.default') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-2 fw-bold text-primary">{{ \App\Models\Admin::count() }}</div>
                            <div class="text-muted">Total Admins</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-2 fw-bold text-success">{{ \App\Models\Student::count() }}</div>
                            <div class="text-muted">Total Students</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-2 fw-bold text-info">{{ \App\Models\Course::count() }}</div>
                            <div class="text-muted">Total Courses</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-2 fw-bold text-warning">{{ \App\Models\Payment::count() }}</div>
                            <div class="text-muted">Total Payments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Maintenance</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Maintenance Actions:</strong> These actions should be performed with caution and preferably during off-peak hours.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="clearCache()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Clear Application Cache
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="clearRoutes()">
                                <i class="bi bi-signpost me-2"></i>Clear Route Cache
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="clearViews()">
                                <i class="bi bi-eye me-2"></i>Clear View Cache
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-warning" onclick="optimizeApp()">
                                <i class="bi bi-speedometer2 me-2"></i>Optimize Application
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="runMigrations()">
                                <i class="bi bi-database me-2"></i>Run Migrations
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmAction('This will seed the database with sample data. Continue?', seedDatabase)">
                                <i class="bi bi-database-fill-add me-2"></i>Seed Database
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function makeMaintenanceRequest(url, confirmMessage, successMessage) {
    if (confirm(confirmMessage)) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(successMessage || data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Network error: ' + error.message);
        });
    }
}

function clearCache() {
    makeMaintenanceRequest(
        '{{ route("admin.maintenance.clear-cache") }}',
        'Clear application cache? This may temporarily slow down the application.',
        'Application cache cleared successfully.'
    );
}

function clearRoutes() {
    makeMaintenanceRequest(
        '{{ route("admin.maintenance.clear-routes") }}',
        'Clear route cache? This will require routes to be re-cached.',
        'Route cache cleared successfully.'
    );
}

function clearViews() {
    makeMaintenanceRequest(
        '{{ route("admin.maintenance.clear-views") }}',
        'Clear view cache? Compiled views will be regenerated on next request.',
        'View cache cleared successfully.'
    );
}

function optimizeApp() {
    makeMaintenanceRequest(
        '{{ route("admin.maintenance.optimize") }}',
        'Optimize application? This will cache routes, views, and configuration.',
        'Application optimized successfully.'
    );
}

function runMigrations() {
    makeMaintenanceRequest(
        '{{ route("admin.maintenance.migrate") }}',
        'Run database migrations? This may modify the database structure.',
        'Database migrations completed successfully.'
    );
}

function seedDatabase() {
    makeMaintenanceRequest(
        '{{ route("admin.maintenance.seed") }}',
        'Seed database with sample data? This will add test data to the database.',
        'Database seeded successfully.'
    );
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
</script>
@endsection