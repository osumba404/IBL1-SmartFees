@extends('layouts.admin')

@section('title', 'Account Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Account Settings</h1>
    <p class="page-subtitle">Manage your personal account preferences</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" value="{{ $admin->email }}" readonly>
                        <div class="form-text">Email address cannot be changed. Contact system administrator if needed.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $admin->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shield-lock me-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <div class="form-control-plaintext">
                        <span class="badge bg-primary">{{ ucfirst($admin->role) }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-control-plaintext">
                        <span class="badge {{ $admin->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Last Login</label>
                    <div class="form-control-plaintext">
                        {{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y g:i A') : 'Never' }}
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Account Created</label>
                    <div class="form-control-plaintext">
                        {{ $admin->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Permissions</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @if($admin->can_manage_students)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Manage Students
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    @endif
                    
                    @if($admin->can_manage_courses)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Manage Courses
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    @endif
                    
                    @if($admin->can_manage_payments)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Manage Payments
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    @endif
                    
                    @if($admin->can_manage_fees)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Manage Fees
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    @endif
                    
                    @if($admin->can_view_reports)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            View Reports
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    @endif
                    
                    @if($admin->can_approve_students)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Approve Students
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    @endif
                    
                    @if($admin->isSuperAdmin())
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Super Admin
                            <i class="bi bi-shield-fill-check text-primary"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection