<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Demo - Edulink SmartFees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            
            /* Light theme variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        [data-theme="dark"] {
            /* Dark theme variables */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
            --card-bg: #1e293b;
        }

        body {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .card {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            transition: background-color 0.3s ease;
        }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        [data-theme="dark"] .form-control {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Theme">
        <i class="bi bi-moon-fill" id="theme-icon"></i>
    </button>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-5">Edulink SmartFees - Theme Demo</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Student Portal</h5>
                    </div>
                    <div class="card-body">
                        <p>Experience the student portal with both light and dark themes.</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary">Make Payment</button>
                            <button class="btn btn-outline-primary">View Courses</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Admin Portal</h5>
                    </div>
                    <div class="card-body">
                        <p>Manage students and courses with theme support.</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success">Add Student</button>
                            <button class="btn btn-outline-success">View Reports</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Theme Features</h5>
                    </div>
                    <div class="card-body">
                        <p>Toggle between light and dark modes seamlessly.</p>
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" placeholder="Sample input">
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-warning">Test Button</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Theme Implementation</h5>
                        <p>The theme system uses CSS custom properties (variables) to dynamically switch between light and dark modes. The theme preference is saved in localStorage and persists across sessions.</p>
                        <ul>
                            <li><strong>Light Mode:</strong> Clean, bright interface with white backgrounds</li>
                            <li><strong>Dark Mode:</strong> Easy on the eyes with dark backgrounds and light text</li>
                            <li><strong>Smooth Transitions:</strong> All elements transition smoothly between themes</li>
                            <li><strong>Persistent:</strong> Theme choice is remembered across browser sessions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            const currentTheme = html.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                themeIcon.className = 'bi bi-moon-fill';
                localStorage.setItem('demo-theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                themeIcon.className = 'bi bi-sun-fill';
                localStorage.setItem('demo-theme', 'dark');
            }
        }

        // Load saved theme
        function loadTheme() {
            const savedTheme = localStorage.getItem('demo-theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            html.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill';
            } else {
                themeIcon.className = 'bi bi-moon-fill';
            }
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', loadTheme);
    </script>
</body>
</html>