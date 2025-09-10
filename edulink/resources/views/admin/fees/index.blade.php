@extends('layouts.admin')

@section('title', 'Fee Structures')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Fee Structures</h1>
            <p class="text-muted mb-0">Manage course fees and payment structures</p>
        </div>
        <div>
            <a href="{{ route('admin.fee-structures.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Fee Structure
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <select name="semester_id" class="form-select">
                        <option value="">All Semesters</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.fee-structures.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fee Structures Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Fee Structures ({{ $feeStructures->total() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($feeStructures->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Semester</th>
                                <th>Total Fee</th>
                                <th>Registration Fee</th>
                                <th>Tuition Fee</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feeStructures as $feeStructure)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $feeStructure->course->name }}</div>
                                            <small class="text-muted">{{ $feeStructure->course->course_code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $feeStructure->semester->name }}</div>
                                            <small class="text-muted">{{ $feeStructure->semester->academic_year }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            KSh {{ number_format($feeStructure->total_amount, 2) }}
                                        </span>
                                    </td>
                                    <td>KSh {{ number_format($feeStructure->registration_fee ?? 0, 2) }}</td>
                                    <td>KSh {{ number_format($feeStructure->tuition_fee ?? 0, 2) }}</td>
                                    <td>
                                        @switch($feeStructure->status)
                                            @case('active')
                                                <span class="badge bg-success">Active</span>
                                                @break
                                            @case('inactive')
                                                <span class="badge bg-warning">Inactive</span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-secondary">Archived</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ ucfirst($feeStructure->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $feeStructure->created_at->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.fee-structures.show', $feeStructure) }}">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.fee-structures.edit', $feeStructure) }}">
                                                        <i class="bi bi-pencil me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($feeStructure->status === 'active')
                                                    <li>
                                                        <form action="{{ route('admin.fee-structures.toggle-status', $feeStructure) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="bi bi-pause-circle me-2"></i>Deactivate
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.fee-structures.toggle-status', $feeStructure) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bi bi-play-circle me-2"></i>Activate
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('admin.fee-structures.destroy', $feeStructure) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this fee structure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    {{ $feeStructures->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-currency-dollar display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Fee Structures Found</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['course_id', 'semester_id', 'status']))
                            No fee structures match your current filters.
                        @else
                            Get started by creating your first fee structure.
                        @endif
                    </p>
                    @if(!request()->hasAny(['course_id', 'semester_id', 'status']))
                        <a href="{{ route('admin.fee-structures.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Fee Structure
                        </a>
                    @else
                        <a href="{{ route('admin.fee-structures.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-x-circle me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Active Structures</h6>
                            <h4 class="mb-0">{{ $feeStructures->where('status', 'active')->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle-fill fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Revenue</h6>
                            <h4 class="mb-0">KSh {{ number_format($feeStructures->sum('total_amount'), 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Courses Covered</h6>
                            <h4 class="mb-0">{{ $feeStructures->unique('course_id')->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Avg Fee Amount</h6>
                            <h4 class="mb-0">KSh {{ number_format($feeStructures->avg('total_amount'), 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form when filters change
document.querySelectorAll('select[name="course_id"], select[name="semester_id"], select[name="status"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});

// Confirm delete actions
document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to delete this fee structure? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
