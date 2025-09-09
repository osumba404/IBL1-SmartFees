@extends('layouts.admin')

@section('title', 'Manage Semesters')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Semesters</h1>
        <a href="{{ route('admin.semesters.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Semester
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="semesters-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semesters as $semester)
                            <tr>
                                <td>{{ $semester->id }}</td>
                                <td>{{ $semester->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $now = now();
                                        $start = \Carbon\Carbon::parse($semester->start_date);
                                        $end = \Carbon\Carbon::parse($semester->end_date);
                                        $status = $now->between($start, $end) ? 'active' : ($now->lt($start) ? 'upcoming' : 'completed');
                                    @endphp
                                    <span class="badge bg-{{ 
                                        $status === 'active' ? 'success' : 
                                        ($status === 'upcoming' ? 'info' : 'secondary') 
                                    }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete('{{ route('admin.semesters.destroy', $semester) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No semesters found.</td>
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
                Are you sure you want to delete this semester? This action cannot be undone.
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
        $('#semesters-table').DataTable({
            order: [[0, 'desc']],
            responsive: true
        });
    });
</script>
@endpush
