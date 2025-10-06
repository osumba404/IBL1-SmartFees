@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Profile</h1>
    <p class="page-subtitle">View and manage your profile information</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Profile Information</h5>
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
                        <div class="form-text">Email address cannot be changed.</div>
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
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Account Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <div class="form-control-plaintext">
                        <span class="badge bg-primary">{{ str_replace('_', ' ', ucwords($admin->role, '_')) }}</span>
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
                        {{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y g:i A') : 'First login' }}
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Member Since</label>
                    <div class="form-control-plaintext">
                        {{ $admin->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection