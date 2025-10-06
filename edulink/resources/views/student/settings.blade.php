@extends('layouts.student')

@section('title', 'Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Settings</h2>
            <p class="page-subtitle">Manage your account preferences and notifications</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Account Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Language</label>
                                <select class="form-select" name="language">
                                    <option value="en" {{ ($student->language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="sw" {{ ($student->language ?? 'en') === 'sw' ? 'selected' : '' }}>Kiswahili</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Timezone</label>
                                <select class="form-select" name="timezone">
                                    <option value="Africa/Nairobi" {{ ($student->timezone ?? 'Africa/Nairobi') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                                    <option value="UTC" {{ ($student->timezone ?? 'Africa/Nairobi') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">Notification Preferences</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notifications_email" 
                                   id="notifications_email" value="1" 
                                   {{ ($student->notifications_email ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifications_email">
                                Email Notifications
                            </label>
                            <div class="form-text">Receive payment confirmations and important updates via email</div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="notifications_sms" 
                                   id="notifications_sms" value="1" 
                                   {{ ($student->notifications_sms ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifications_sms">
                                SMS Notifications
                            </label>
                            <div class="form-text">Receive payment confirmations via SMS</div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Student ID</small><br>
                        <span class="fw-medium">{{ $student->student_id }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Email</small><br>
                        <span class="fw-medium">{{ $student->email }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Phone</small><br>
                        <span class="fw-medium">{{ $student->phone }}</span>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted">Member Since</small><br>
                        <span class="fw-medium">{{ $student->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection