@extends('layouts.admin')

@section('title', 'Edit Fee Structure')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Fee Structure</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fee-structures.update', $feeStructure) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course</label>
                                    <select name="course_id" id="course_id" class="form-select" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ $feeStructure->course_id == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester_id" class="form-label">Semester</label>
                                    <select name="semester_id" id="semester_id" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ $feeStructure->semester_id == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tuition_fee" class="form-label">Tuition Fee</label>
                                    <input type="number" step="0.01" name="tuition_fee" id="tuition_fee" class="form-control" value="{{ $feeStructure->tuition_fee }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registration_fee" class="form-label">Registration Fee</label>
                                    <input type="number" step="0.01" name="registration_fee" id="registration_fee" class="form-control" value="{{ $feeStructure->registration_fee }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lab_fee" class="form-label">Lab Fee</label>
                                    <input type="number" step="0.01" name="lab_fee" id="lab_fee" class="form-control" value="{{ $feeStructure->lab_fee }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="library_fee" class="form-label">Library Fee</label>
                                    <input type="number" step="0.01" name="library_fee" id="library_fee" class="form-control" value="{{ $feeStructure->library_fee }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="effective_from" class="form-label">Effective From</label>
                                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="{{ $feeStructure->effective_from->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="active" {{ $feeStructure->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $feeStructure->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="archived" {{ $feeStructure->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fee-structures.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Fee Structure</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection