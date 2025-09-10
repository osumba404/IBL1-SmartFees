@extends('layouts.admin')

@section('title', 'Manage Students')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Students</h1>
        <div>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i> Add New Student
            </a>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-upload me-1"></i> Import
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Students List</h6>
            <div class="d-flex">
                <div class="input-group me-2" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                        <h6 class="dropdown-header">Filter by Status</h6>
                        <a class="dropdown-item" href="#" data-filter="status" data-value="active">Active</a>
                        <a class="dropdown-item" href="#" data-filter="status" data-value="inactive">Inactive</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Filter by Course</h6>
                        @foreach($courses as $course)
                            <a class="dropdown-item" href="#" data-filter="course" data-value="{{ $course->id }}">{{ $course->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Admission #</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->admission_number }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="rounded-circle" width="40" height="40">
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $student->name }}</div>
                                            <small class="text-muted">#{{ $student->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    @if($student->activeEnrollments->count() > 0)
                                        {{ $student->activeEnrollments->first()->course->name }}
                                        @if($student->activeEnrollments->count() > 1)
                                            <small class="text-muted">(+{{ $student->activeEnrollments->count() - 1 }} more)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">No active enrollments</span>
                                    @endif
                                </td>
                                <td>{{ $student->phone ?? 'N/A' }}</td>
                                <td>
                                    @if($student->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-student" data-id="{{ $student->id }}" data-bs-toggle="tooltip" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No students found.</td>
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
                Are you sure you want to delete this student? This action cannot be undone.
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose Excel/CSV File</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            <a href="{{ asset('templates/students_import_template.xlsx') }}" download class="text-decoration-none">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        const table = $('#studentsTable').DataTable({
            order: [[0, 'desc']],
            responsive: true,
            pageLength: 25,
            dom: '<"d-flex justify-content-between align-items-center mb-3"f<"d-flex align-items-center"><"d-flex align-items-center"l>><"table-responsive"t><"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
            language: {
                search: "",
                searchPlaceholder: "Search students...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries found",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            },
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_filter input').attr('placeholder', 'Search...');
                $('.dataTables_length select').addClass('form-select');
            }
        });

        // Handle delete button click
        $(document).on('click', '.delete-student', function() {
            const studentId = $(this).data('id');
            const form = $('#deleteForm');
            form.attr('action', `/admin/students/${studentId}`);
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });

        // Handle filter dropdown clicks
        $('[data-filter]').on('click', function(e) {
            e.preventDefault();
            const filter = $(this).data('filter');
            const value = $(this).data('value');
            
            // Update UI
            $(this).addClass('active').siblings().removeClass('active');
            
            // Apply filter to DataTable
            if (filter && value) {
                table.column(`${filter}:name`).search(value).draw();
            } else {
                table.search('').columns().search('').draw();
            }
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
    }
    .dataTables_wrapper .dataTables_length select {
        margin: 0 0.5em;
    }
    .dropdown-item.active {
        background-color: #0d6efd;
        color: white;
    }
    .avatar img {
        object-fit: cover;
    }
</style>
@endpush
