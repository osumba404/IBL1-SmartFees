@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Courses</h1>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Course
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="courses-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Duration</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $course->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $course->department ?? 'No Department' }}</small>
                                    </div>
                                </td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->duration_months }} months</td>
                                <td>KSh {{ number_format($course->total_fee, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $course->status === 'active' ? 'success' : ($course->status === 'inactive' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                                onclick="confirmDelete('{{ route('admin.courses.destroy', $course) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No courses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(url) {
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Initialize DataTable
    $(document).ready(function() {
        $('#courses-table').DataTable({
            order: [[0, 'desc']],
            responsive: true
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Dark mode fixes for DataTables */
    [data-theme="dark"] .dataTables_wrapper {
        color: var(--text-primary) !important;
    }
    
    [data-theme="dark"] .dataTables_wrapper .dataTables_filter input {
        background-color: var(--card-bg) !important;
        border-color: var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    
    [data-theme="dark"] .dataTables_wrapper .dataTables_length select {
        background-color: var(--card-bg) !important;
        border-color: var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    
    [data-theme="dark"] .dataTables_wrapper .dataTables_info {
        color: var(--text-secondary) !important;
    }
    
    [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: var(--text-primary) !important;
        background: var(--card-bg) !important;
        border-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: var(--text-primary) !important;
        background: var(--bg-secondary) !important;
        border-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        color: white !important;
        background: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }
    
    [data-theme="dark"] .table-bordered {
        border-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] .table-bordered th,
    [data-theme="dark"] .table-bordered td {
        border-color: var(--border-color) !important;
        background-color: var(--card-bg) !important;
        color: var(--text-primary) !important;
    }
    
    [data-theme="dark"] .table thead th {
        background-color: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }
    
    [data-theme="dark"] strong {
        color: var(--text-primary) !important;
    }
    
    [data-theme="dark"] .text-muted {
        color: var(--text-secondary) !important;
    }
    
    [data-theme="dark"] .bg-success {
        background-color: var(--success-color) !important;
    }
    
    [data-theme="dark"] .bg-warning {
        background-color: var(--warning-color) !important;
    }
    
    [data-theme="dark"] .bg-danger {
        background-color: var(--danger-color) !important;
    }
</style>
@endpush
