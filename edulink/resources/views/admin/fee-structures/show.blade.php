@extends('layouts.admin')

@section('title', 'Fee Structure Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $feeStructure->name }}</h5>
                    <div>
                        <a href="{{ route('admin.fee-structures.edit', $feeStructure) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.fee-structures.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Code:</strong></td>
                                    <td>{{ $feeStructure->fee_structure_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $feeStructure->course->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Semester:</strong></td>
                                    <td>{{ $feeStructure->semester->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $feeStructure->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($feeStructure->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Fee Breakdown</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Tuition Fee:</strong></td>
                                    <td>KES {{ number_format($feeStructure->tuition_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lab Fee:</strong></td>
                                    <td>KES {{ number_format($feeStructure->lab_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Library Fee:</strong></td>
                                    <td>KES {{ number_format($feeStructure->library_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong>KES {{ number_format($feeStructure->total_amount, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection