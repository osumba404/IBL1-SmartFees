@extends('layouts.admin')

@section('title', 'Admin Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Management</h1>
    <p class="page-subtitle">Manage system administrators and their permissions</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System Administrators</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New Admin
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
                                <th>Permissions</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $adminUser)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($adminUser->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $adminUser->name }}</div>
                                            @if($adminUser->employee_id)
                                                <small class="text-muted">ID: {{ $adminUser->employee_id }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $adminUser->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $adminUser->role === 'super_admin' ? 'danger' : 'primary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $adminUser->role)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $adminUser->is_active ? 'success' : 'secondary' }}">
                                        {{ $adminUser->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($adminUser->can_manage_students)
                                            <span class="badge bg-info">Students</span>
                                        @endif
                                        @if($adminUser->can_manage_courses)
                                            <span class="badge bg-info">Courses</span>
                                        @endif
                                        @if($adminUser->can_manage_payments)
                                            <span class="badge bg-info">Payments</span>
                                        @endif
                                        @if($adminUser->can_manage_fees)
                                            <span class="badge bg-info">Fees</span>
                                        @endif
                                        @if($adminUser->can_view_reports)
                                            <span class="badge bg-info">Reports</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($adminUser->last_login_at)
                                        {{ $adminUser->last_login_at->diffForHumans() }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    @if($adminUser->id !== $admin->id)
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editAdmin({{ $adminUser->id }}, '{{ $adminUser->name }}', '{{ $adminUser->email }}', {{ $adminUser->is_active ? 'true' : 'false' }}, {{ json_encode([
                                                    'manage_students' => $adminUser->can_manage_students,
                                                    'manage_courses' => $adminUser->can_manage_courses,
                                                    'manage_payments' => $adminUser->can_manage_payments,
                                                    'manage_fees' => $adminUser->can_manage_fees,
                                                    'view_reports' => $adminUser->can_view_reports,
                                                    'approve_students' => $adminUser->can_approve_students
                                                ]) }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">Current User</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        No administrators found
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($admins->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $admins->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.admins.create') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Administrator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="finance">Finance Officer</option>
                            <option value="registrar">Registrar</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="manage_students" name="permissions[]" value="manage_students">
                            <label class="form-check-label" for="manage_students">Manage Students</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="manage_courses" name="permissions[]" value="manage_courses">
                            <label class="form-check-label" for="manage_courses">Manage Courses</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="manage_payments" name="permissions[]" value="manage_payments">
                            <label class="form-check-label" for="manage_payments">Manage Payments</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="manage_fees" name="permissions[]" value="manage_fees">
                            <label class="form-check-label" for="manage_fees">Manage Fees</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="view_reports" name="permissions[]" value="view_reports">
                            <label class="form-check-label" for="view_reports">View Reports</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="approve_students" name="permissions[]" value="approve_students">
                            <label class="form-check-label" for="approve_students">Approve Students</label>
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

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editAdminForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Administrator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Administrator</label>
                        <div id="editAdminInfo" class="form-control-plaintext"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Account Active</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_manage_students" name="permissions[]" value="manage_students">
                            <label class="form-check-label" for="edit_manage_students">Manage Students</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_manage_courses" name="permissions[]" value="manage_courses">
                            <label class="form-check-label" for="edit_manage_courses">Manage Courses</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_manage_payments" name="permissions[]" value="manage_payments">
                            <label class="form-check-label" for="edit_manage_payments">Manage Payments</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_manage_fees" name="permissions[]" value="manage_fees">
                            <label class="form-check-label" for="edit_manage_fees">Manage Fees</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_view_reports" name="permissions[]" value="view_reports">
                            <label class="form-check-label" for="edit_view_reports">View Reports</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_approve_students" name="permissions[]" value="approve_students">
                            <label class="form-check-label" for="edit_approve_students">Approve Students</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAdmin(id, name, email, isActive, permissions) {
    document.getElementById('editAdminInfo').innerHTML = `<strong>${name}</strong><br><small class="text-muted">${email}</small>`;
    document.getElementById('editAdminForm').action = `/admin/admins/${id}`;
    document.getElementById('edit_is_active').checked = isActive;
    
    // Reset all checkboxes
    document.querySelectorAll('#editAdminModal input[name="permissions[]"]').forEach(cb => cb.checked = false);
    
    // Set permissions
    Object.keys(permissions).forEach(permission => {
        if (permissions[permission]) {
            const checkbox = document.getElementById(`edit_${permission}`);
            if (checkbox) checkbox.checked = true;
        }
    });
    
    new bootstrap.Modal(document.getElementById('editAdminModal')).show();
}
</script>
@endsection