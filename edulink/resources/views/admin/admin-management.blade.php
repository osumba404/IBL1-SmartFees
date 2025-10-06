@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Admin Management</h2>
            <p class="page-subtitle">Manage administrator accounts and permissions</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Administrator Accounts</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
                <i class="bi bi-plus me-2"></i>Create Admin
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $adminUser)
                        <tr>
                            <td>{{ $adminUser->name }}</td>
                            <td>{{ $adminUser->email }}</td>
                            <td><span class="badge bg-primary">{{ ucfirst($adminUser->role ?? 'admin') }}</span></td>
                            <td><span class="badge bg-{{ $adminUser->is_active ? 'success' : 'danger' }}">{{ $adminUser->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $adminUser->last_login_at ? $adminUser->last_login_at->format('M d, Y g:i A') : 'Never' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editAdmin({{ $adminUser->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No administrators found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $admins->links() }}
        </div>
    </div>
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.admins.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="finance">Finance Officer</option>
                            <option value="registrar">Registrar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_students" id="manage_students">
                            <label class="form-check-label" for="manage_students">Manage Students</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_courses" id="manage_courses">
                            <label class="form-check-label" for="manage_courses">Manage Courses</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_payments" id="manage_payments">
                            <label class="form-check-label" for="manage_payments">Manage Payments</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="view_reports" id="view_reports">
                            <label class="form-check-label" for="view_reports">View Reports</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editAdmin(adminId) {
    alert('Edit functionality not yet implemented for admin ID: ' + adminId);
}
</script>
@endpush