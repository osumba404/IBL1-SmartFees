@extends('layouts.admin')

@section('title', 'Fee Structures - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Fee Structures</h1>
                    <p class="text-muted">{{ $course->name }} ({{ $course->course_code }})</p>
                </div>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Course Fee Structure</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editFeeModal">
                <i class="bi bi-pencil"></i> Edit Fees
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Total Fee:</strong></td>
                            <td>KSh {{ number_format($course->total_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Registration Fee:</strong></td>
                            <td>KSh {{ number_format($course->registration_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Examination Fee:</strong></td>
                            <td>KSh {{ number_format($course->examination_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Library Fee:</strong></td>
                            <td>KSh {{ number_format($course->library_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lab Fee:</strong></td>
                            <td>KSh {{ number_format($course->lab_fee, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Allows Installments:</strong></td>
                            <td>
                                <span class="badge bg-{{ $course->allows_installments ? 'success' : 'secondary' }}">
                                    {{ $course->allows_installments ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                        @if($course->allows_installments)
                        <tr>
                            <td><strong>Max Installments:</strong></td>
                            <td>{{ $course->max_installments }}</td>
                        </tr>
                        <tr>
                            <td><strong>Minimum Deposit:</strong></td>
                            <td>{{ $course->minimum_deposit_percentage }}%</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($course->allows_installments)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Installment Breakdown</h5>
        </div>
        <div class="card-body">
            @php
                $totalFee = $course->total_fee;
                $minDeposit = ($totalFee * $course->minimum_deposit_percentage) / 100;
                $remaining = $totalFee - $minDeposit;
                $installmentAmount = $remaining / ($course->max_installments - 1);
            @endphp
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Installment</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1st Payment (Deposit)</td>
                            <td>KSh {{ number_format($minDeposit, 2) }}</td>
                            <td>Minimum deposit ({{ $course->minimum_deposit_percentage }}%)</td>
                        </tr>
                        @for($i = 2; $i <= $course->max_installments; $i++)
                        <tr>
                            <td>{{ $i }}{{ $i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th') }} Payment</td>
                            <td>KSh {{ number_format($installmentAmount, 2) }}</td>
                            <td>Installment payment</td>
                        </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th>Total</th>
                            <th>KSh {{ number_format($totalFee, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Edit Fee Modal -->
<div class="modal fade" id="editFeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Fee Structure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.courses.update-fees', $course) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Fee</label>
                                <input type="number" name="total_fee" class="form-control" 
                                       value="{{ $course->total_fee }}" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Registration Fee</label>
                                <input type="number" name="registration_fee" class="form-control" 
                                       value="{{ $course->registration_fee }}" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Examination Fee</label>
                                <input type="number" name="examination_fee" class="form-control" 
                                       value="{{ $course->examination_fee }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Library Fee</label>
                                <input type="number" name="library_fee" class="form-control" 
                                       value="{{ $course->library_fee }}" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lab Fee</label>
                                <input type="number" name="lab_fee" class="form-control" 
                                       value="{{ $course->lab_fee }}" step="0.01">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="allows_installments" class="form-check-input" 
                                           id="allowsInstallments" {{ $course->allows_installments ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allowsInstallments">
                                        Allow Installment Payments
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="installmentOptions" style="{{ $course->allows_installments ? '' : 'display: none;' }}">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Maximum Installments</label>
                                    <input type="number" name="max_installments" class="form-control" 
                                           value="{{ $course->max_installments }}" min="2" max="12">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Minimum Deposit Percentage</label>
                                    <input type="number" name="minimum_deposit_percentage" class="form-control" 
                                           value="{{ $course->minimum_deposit_percentage }}" min="10" max="100" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Fee Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('allowsInstallments').addEventListener('change', function() {
    const installmentOptions = document.getElementById('installmentOptions');
    installmentOptions.style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection