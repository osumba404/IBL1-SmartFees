@extends('layouts.admin')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Course</h1>
        <div>
            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Course
            </a>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> All Courses
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Course Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $course->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code', $course->course_code) }}" required placeholder="e.g., CS101, BBA201">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('department') is-invalid @enderror" 
                               id="department" name="department" value="{{ old('department', $course->department) }}" required>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                            <option value="">Select Level</option>
                            <option value="certificate" {{ old('level', $course->level) == 'certificate' ? 'selected' : '' }}>Certificate</option>
                            <option value="diploma" {{ old('level', $course->level) == 'diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="degree" {{ old('level', $course->level) == 'degree' ? 'selected' : '' }}>Degree</option>
                            <option value="masters" {{ old('level', $course->level) == 'masters' ? 'selected' : '' }}>Masters</option>
                            <option value="phd" {{ old('level', $course->level) == 'phd' ? 'selected' : '' }}>PhD</option>
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="duration_months" class="form-label">Duration (months) <span class="text-danger">*</span></label>
                        <input type="number" min="1" max="120" class="form-control @error('duration_months') is-invalid @enderror" 
                               id="duration_months" name="duration_months" value="{{ old('duration_months', $course->duration_months) }}" required>
                        @error('duration_months')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="credit_hours" class="form-label">Credit Hours</label>
                        <input type="number" min="1" class="form-control @error('credit_hours') is-invalid @enderror" 
                               id="credit_hours" name="credit_hours" value="{{ old('credit_hours', $course->credit_hours) }}">
                        @error('credit_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="total_fee" class="form-label">Total Fee <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">KSh</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('total_fee') is-invalid @enderror" 
                                   id="total_fee" name="total_fee" value="{{ old('total_fee', $course->total_fee) }}" required>
                        </div>
                        @error('total_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="enrollment_capacity" class="form-label">Enrollment Capacity</label>
                        <input type="number" min="1" class="form-control @error('enrollment_capacity') is-invalid @enderror" 
                               id="enrollment_capacity" name="enrollment_capacity" value="{{ old('enrollment_capacity', $course->max_students) }}" 
                               placeholder="Leave empty for unlimited">
                        @error('enrollment_capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" placeholder="Course description and details">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(isset($availableCourses) && $availableCourses->count() > 0)
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="prerequisites" class="form-label">Prerequisites</label>
                        <select class="form-select @error('prerequisites') is-invalid @enderror" id="prerequisites" name="prerequisites[]" multiple>
                            @foreach($availableCourses as $availableCourse)
                                <option value="{{ $availableCourse->id }}" 
                                    {{ in_array($availableCourse->id, old('prerequisites', $course->prerequisites ?? [])) ? 'selected' : '' }}>
                                    {{ $availableCourse->course_code }} - {{ $availableCourse->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple prerequisites</div>
                        @error('prerequisites')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Enrollment Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                            <option value="1" {{ old('is_active', $course->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Open</option>
                            <option value="0" {{ old('is_active', $course->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">Course Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="discontinued" {{ old('status', $course->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize any required scripts here
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript if needed
    });
</script>
@endpush

@endsection
