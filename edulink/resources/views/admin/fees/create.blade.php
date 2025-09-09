@extends('layouts.admin')

@section('title', 'Create Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New Fee Structure</h1>
        <a href="{{ route('admin.fee-structures.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Fee Structures
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.fee-structures.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Fee Structure Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select class="form-select @error('academic_year') is-invalid @enderror" 
                                id="academic_year" name="academic_year" required>
                            <option value="">Select Academic Year</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}/{{ $year + 1 }}" 
                                    {{ old('academic_year') == "$year/" . ($year + 1) ? 'selected' : '' }}>
                                    {{ $year }}/{{ $year + 1 }}
                                </option>
                            @endfor
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_id') is-invalid @enderror" 
                                id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" 
                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }} ({{ $course->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester_id') is-invalid @enderror" 
                                id="semester_id" name="semester_id" required>
                            <option value="">Select Semester</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" 
                                    {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }} ({{ \Carbon\Carbon::parse($semester->start_date)->format('M Y') }} - {{ \Carbon\Carbon::parse($semester->end_date)->format('M Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Fee Items</label>
                        <div id="fee-items">
                            <div class="fee-item row mb-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="fee_items[0][name]" 
                                           placeholder="Fee Item Name" required>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text">Ksh</span>
                                        <input type="number" step="0.01" min="0" class="form-control" 
                                               name="fee_items[0][amount]" placeholder="Amount" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-fee-item" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-fee-item" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus me-1"></i> Add Fee Item
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Fee Structure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let feeItemCount = 1;
        
        // Add new fee item
        document.getElementById('add-fee-item').addEventListener('click', function() {
            const feeItems = document.getElementById('fee-items');
            const newItem = document.querySelector('.fee-item').cloneNode(true);
            
            // Update the indices in the name attributes
            newItem.innerHTML = newItem.innerHTML.replace(/\[0\]/g, `[${feeItemCount}]`);
            
            // Clear the input values
            newItem.querySelectorAll('input').forEach(input => {
                input.value = '';
                input.required = true;
            });
            
            // Enable the remove button
            const removeBtn = newItem.querySelector('.remove-fee-item');
            removeBtn.disabled = false;
            removeBtn.addEventListener('click', function() {
                feeItems.removeChild(newItem);
            });
            
            feeItems.appendChild(newItem);
            feeItemCount++;
        });
        
        // Remove fee item
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-fee-item')) {
                const feeItem = e.target.closest('.fee-item');
                if (feeItem && document.querySelectorAll('.fee-item').length > 1) {
                    feeItem.remove();
                }
            }
        });
    });
</script>
@endpush
