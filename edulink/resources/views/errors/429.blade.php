<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Too Many Requests | Edulink SmartFees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #e91e63 0%, #ad1457 100%); min-height: 100vh; }
        .error-container { min-height: 100vh; display: flex; align-items: center; }
        .error-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .error-icon { font-size: 8rem; color: #e91e63; }
        .error-code { font-size: 6rem; font-weight: 700; color: #e91e63; margin: 0; }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="row justify-content-center w-100">
            <div class="col-lg-6 col-md-8">
                <div class="error-card p-5 text-center">
                    <i class="bi bi-speedometer2 error-icon"></i>
                    <h1 class="error-code">429</h1>
                    <h2 class="mb-3">Too Many Requests</h2>
                    <p class="text-muted mb-4">You've made too many requests. Please wait a moment before trying again.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button onclick="setTimeout(() => location.reload(), 5000)" class="btn btn-primary">
                            <i class="bi bi-hourglass-split"></i> Wait & Retry
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>