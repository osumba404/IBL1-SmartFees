<!DOCTYPE html>
<html>
<head>
    <title>Payment Cancelled - Edulink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Payment Cancelled</h3>
                        <p class="text-muted">Your payment was cancelled or failed to process.</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('payment.create') }}" class="btn btn-primary">
                                Try Again
                            </a>
                            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>