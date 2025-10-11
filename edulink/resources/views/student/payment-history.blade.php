@extends('layouts.student')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Payment History</h2>
            <p class="page-subtitle">View all your payment transactions and records</p>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success">
                <div class="card-body text-center">
                    <div class="stats-value">KES {{ number_format($paymentStats['total_payments'], 2) }}</div>
                    <div class="stats-label">Total Payments</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning">
                <div class="card-body text-center">
                    <div class="stats-value">KES {{ number_format($paymentStats['pending_payments'], 2) }}</div>
                    <div class="stats-label">Pending Payments</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger">
                <div class="card-body text-center">
                    <div class="stats-value">{{ $paymentStats['failed_payments'] }}</div>
                    <div class="stats-label">Failed Payments</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-value">
                        @if($paymentStats['last_payment'])
                            {{ $paymentStats['last_payment']->created_at->format('M d') }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="stats-label">Last Payment</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Search & Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Search & Filter Payments</h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                        <i class="bi bi-funnel"></i> Advanced Filters
                    </button>
                </div>
                <div class="card-body">
                    <!-- Quick Search -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="quick-search" 
                                       placeholder="Search by transaction ID, course name, or amount..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-primary" type="button" onclick="performQuickSearch()">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters (Collapsible) -->
                    <div class="collapse {{ request()->hasAny(['status', 'method', 'date_from', 'date_to', 'amount_min', 'amount_max']) ? 'show' : '' }}" id="advancedFilters">
                        <form method="GET" action="{{ route('student.payments.history') }}" class="row g-3">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select">
                                    <option value="">All Methods</option>
                                    <option value="mpesa" {{ request('method') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                    <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Min Amount (KES)</label>
                                <input type="number" name="amount_min" class="form-control" 
                                       value="{{ request('amount_min') }}" step="0.01" min="0">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Max Amount (KES)</label>
                                <input type="number" name="amount_max" class="form-control" 
                                       value="{{ request('amount_max') }}" step="0.01" min="0">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <a href="{{ route('student.payments.history') }}" class="btn btn-outline-secondary">Clear All</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment Transactions</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="exportPayments()">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <a href="{{ route('student.statements.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-text me-1"></i>Statements
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive" id="payments-table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction ID</th>
                                        <th>Course</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="payments-table-body">
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $payment->created_at->format('M d, Y') }}</strong><br>
                                                    <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="small">{{ $payment->transaction_id ?? $payment->id }}</code>
                                            </td>
                                            <td>
                                                @if($payment->enrollment && $payment->enrollment->course)
                                                    <div>
                                                        <strong>{{ $payment->enrollment->course->name }}</strong><br>
                                                        <small class="text-muted">{{ $payment->enrollment->course->course_code }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-primary">KES {{ number_format($payment->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    @switch($payment->payment_method)
                                                        @case('mpesa')
                                                            <i class="bi bi-phone me-1"></i>M-Pesa
                                                            @break
                                                        @case('card')
                                                            <i class="bi bi-credit-card me-1"></i>Card
                                                            @break
                                                        @case('bank_transfer')
                                                            <i class="bi bi-bank me-1"></i>Bank Transfer
                                                            @break
                                                        @case('cash')
                                                            <i class="bi bi-cash me-1"></i>Cash
                                                            @break
                                                        @default
                                                            {{ ucfirst($payment->payment_method) }}
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td>
                                                @switch($payment->status)
                                                    @case('completed')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Completed
                                                        </span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-clock me-1"></i>Pending
                                                        </span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle me-1"></i>Failed
                                                        </span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-dash-circle me-1"></i>Cancelled
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ ucfirst($payment->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewPaymentDetails({{ $payment->id }})">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    @if($payment->status === 'completed')
                                                        <button class="btn btn-outline-success btn-sm" onclick="downloadReceipt({{ $payment->id }})">
                                                            <i class="bi bi-receipt"></i>
                                                        </button>
                                                    @endif
                                                    @if($payment->status === 'failed')
                                                        <button class="btn btn-outline-warning btn-sm" onclick="retryPayment({{ $payment->id }})">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4" id="pagination-container">
                            <div class="text-muted" id="pagination-info">
                                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                            </div>
                            <div id="pagination-links">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payment History</h5>
                            <p class="text-muted">You haven't made any payments yet.</p>
                            <a href="{{ route('student.fees.index') }}" class="btn btn-primary">Make a Payment</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetailsContent">
                    <!-- Payment details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadReceiptBtn" style="display: none;">
                    <i class="bi bi-receipt me-1"></i>Download Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Fix pagination arrow sizes */
.pagination .page-link {
    font-size: 14px !important;
    padding: 8px 12px !important;
    min-width: 40px !important;
    height: 40px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.pagination .page-link * {
    font-size: 12px !important;
}

.pagination .page-link svg {
    width: 14px !important;
    height: 14px !important;
}

/* Mobile pagination fixes */
@media (max-width: 768px) {
    .pagination .page-link {
        font-size: 12px !important;
        padding: 6px 10px !important;
        min-width: 36px !important;
        height: 36px !important;
    }
    
    .pagination .page-link * {
        font-size: 10px !important;
    }
    
    .pagination .page-link svg {
        width: 12px !important;
        height: 12px !important;
    }
}

/* Dark mode text fixes */
[data-theme="dark"] .badge.bg-light {
    background-color: var(--bg-secondary) !important;
    color: var(--text-primary) !important;
    border: 1px solid var(--border-color) !important;
}

[data-theme="dark"] .text-dark {
    color: var(--text-primary) !important;
}

[data-theme="dark"] code {
    background-color: var(--bg-secondary) !important;
    color: var(--text-primary) !important;
}

[data-theme="dark"] .stats-card .stats-value,
[data-theme="dark"] .stats-card .stats-label {
    color: white !important;
}
</style>
@endpush

@push('scripts')
<script>
function viewPaymentDetails(paymentId) {
    // Load payment details via AJAX
    fetch(`/student/payments/${paymentId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('paymentDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load payment details');
        });
}

function downloadReceipt(paymentId) {
    // Download payment receipt
    window.open(`/student/payments/${paymentId}/receipt`, '_blank');
}

function retryPayment(paymentId) {
    if (confirm('Are you sure you want to retry this payment?')) {
        fetch(`/student/payments/${paymentId}/retry`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    alert('Payment retry initiated successfully!');
                    location.reload();
                }
            } else {
                alert('Failed to retry payment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while retrying payment');
        });
    }
}

function exportPayments() {
    // Get current filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    const exportUrl = '/student/payments/export?' + urlParams.toString();
    window.open(exportUrl, '_blank');
}

// AJAX Pagination with multiple event handling approaches
function setupAjaxPagination() {
    // Method 1: Direct event delegation on document
    document.addEventListener('click', function(e) {
        // Check if clicked element is within pagination
        if (e.target.closest('.pagination')) {
            const link = e.target.closest('a');
            if (link && link.href && link.href !== '#' && !link.href.includes('javascript:')) {
                console.log('Pagination link clicked:', link.href);
                e.preventDefault();
                e.stopImmediatePropagation();
                loadPaymentsPage(link.href);
                return false;
            }
        }
    }, true); // Use capture phase
    
    // Method 2: Direct targeting of existing pagination links
    function attachPaginationHandlers() {
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(function(link) {
            if (link.href && link.href !== '#' && !link.href.includes('javascript:')) {
                link.addEventListener('click', function(e) {
                    console.log('Direct pagination click:', this.href);
                    e.preventDefault();
                    e.stopPropagation();
                    loadPaymentsPage(this.href);
                    return false;
                });
            }
        });
    }
    
    // Attach handlers to existing links
    attachPaginationHandlers();
    
    // Re-attach handlers when pagination updates
    window.attachPaginationHandlers = attachPaginationHandlers;
}

function loadPaymentsPage(url) {
    console.log('Loading page:', url); // Debug log
    
    // Show loading state
    const tableContainer = document.getElementById('payments-table-container');
    const paginationContainer = document.getElementById('pagination-container');
    
    if (tableContainer) {
        tableContainer.style.opacity = '0.5';
        tableContainer.style.pointerEvents = 'none';
    }
    
    // Add loading indicator
    if (paginationContainer) {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading-indicator';
        loadingDiv.innerHTML = '<div class="text-center p-3"><i class="bi bi-arrow-clockwise spin"></i> Loading...</div>';
        paginationContainer.appendChild(loadingDiv);
    }
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        console.log('Response received'); // Debug log
        
        // Parse the response HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Update table body
        const newTableBody = doc.getElementById('payments-table-body');
        const currentTableBody = document.getElementById('payments-table-body');
        if (newTableBody && currentTableBody) {
            currentTableBody.innerHTML = newTableBody.innerHTML;
        }
        
        // Update pagination info
        const newPaginationInfo = doc.getElementById('pagination-info');
        const currentPaginationInfo = document.getElementById('pagination-info');
        if (newPaginationInfo && currentPaginationInfo) {
            currentPaginationInfo.innerHTML = newPaginationInfo.innerHTML;
        }
        
        // Update pagination links
        const newPaginationLinks = doc.getElementById('pagination-links');
        const currentPaginationLinks = document.getElementById('pagination-links');
        if (newPaginationLinks && currentPaginationLinks) {
            currentPaginationLinks.innerHTML = newPaginationLinks.innerHTML;
        }
        
        // Remove loading indicator
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.remove();
        }
        
        // Restore table state
        if (tableContainer) {
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        }
        
        // Fix pagination arrows and re-attach handlers after update
        setTimeout(function() {
            // Force pagination arrow fixes
            document.querySelectorAll('.pagination .page-link').forEach(function(link) {
                link.style.setProperty('font-size', '14px', 'important');
                link.style.setProperty('padding', '8px 12px', 'important');
                link.style.setProperty('min-width', '40px', 'important');
                link.style.setProperty('height', '40px', 'important');
                link.style.setProperty('display', 'flex', 'important');
                link.style.setProperty('align-items', 'center', 'important');
                link.style.setProperty('justify-content', 'center', 'important');
                
                link.querySelectorAll('*').forEach(function(child) {
                    child.style.setProperty('font-size', '12px', 'important');
                    if (child.tagName === 'SVG') {
                        child.style.setProperty('width', '14px', 'important');
                        child.style.setProperty('height', '14px', 'important');
                    }
                });
            });
            
            if (typeof fixPaginationArrows === 'function') {
                fixPaginationArrows();
            }
            if (typeof window.attachPaginationHandlers === 'function') {
                window.attachPaginationHandlers();
            }
        }, 100);
        
        // Update URL without page refresh
        if (window.history && window.history.pushState) {
            window.history.pushState({}, '', url);
        }
        
        console.log('Page updated successfully'); // Debug log
    })
    .catch(error => {
        console.error('Error loading page:', error);
        
        // Remove loading indicator
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.remove();
        }
        
        // Restore table state
        if (tableContainer) {
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        }
        
        // Show error message
        alert('Failed to load page. Please try again.');
    });
}

// Initialize AJAX pagination when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupAjaxPagination();
});

// Also initialize immediately in case DOM is already loaded
setupAjaxPagination();

// Quick search functionality
function performQuickSearch() {
    const searchValue = document.getElementById('quick-search').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('search', searchValue);
    
    if (typeof loadPaymentsPage === 'function') {
        loadPaymentsPage(currentUrl.toString());
    } else {
        window.location.href = currentUrl.toString();
    }
}

// Quick search on Enter key
const quickSearch = document.getElementById('quick-search');
if (quickSearch) {
    quickSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performQuickSearch();
        }
    });
}

// Auto-refresh pending payments every 30 seconds
setInterval(function() {
    const pendingRows = document.querySelectorAll('tr:has(.badge.bg-warning)');
    if (pendingRows.length > 0) {
        // Refresh current page via AJAX instead of full reload
        const currentUrl = window.location.href;
        loadPaymentsPage(currentUrl);
    }
}, 30000);
</script>
@endpush
