<!-- resources/views/dashboard.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(["resources/css/app.css","resources/js/app.js"])
    <title>Dashboard</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .btn-green-gradient {
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 50%, #15803d 100%);
            color: #fff;
            border: 0;
        }
        .btn-green-gradient:hover {
            filter: brightness(0.95);
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">My Laravel App</a>
            <div class="d-flex align-items-center gap-2">
                
                {{-- Show Confirmations button only for admins --}}
                @if(auth()->check() && (auth()->user()->role ?? 'customer') === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-success btn-sm me-2">
                        Confirmations
                    </a>
                @endif

                {{-- Logout button --}}
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="btn btn-danger btn-sm">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Welcome to Your Dashboard</h3>
            </div>
            <div class="card-body">
                
                <!-- Success alert -->
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>Login successful! 🎉</div>
                </div>

                <!-- Greeting -->
                <p class="lead">Hello, <strong>{{ Auth::user()->name }}</strong> 👋</p>
                <hr>

                <!-- User details -->
                <h5>Your Details</h5>
                <ul class="list-unstyled mb-4">
                    <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
                    <li><strong>Joined on:</strong> {{ Auth::user()->created_at->format('d M, Y') }}</li>
                </ul>

                <!-- Actions -->
                 <div class="d-flex flex-wrap gap-3 mb-4">
    <!-- Back to Bookings -->
    <a href="{{ url('/bookings') }}" 
       class="btn btn-success btn-lg px-4 shadow-sm d-flex align-items-center gap-2">
        <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
         Go to Booking Page
    </a>

    @if(auth()->check() && (auth()->user()->role ?? 'customer') === 'admin')
        <!-- Admin Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="btn btn-outline-primary btn-lg px-4 shadow-sm d-flex align-items-center gap-2">
            <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Admin Panel
        </a>

        <!-- Add Forklift -->
        <a href="{{ route('admin.forklifts.create') }}" 
           class="btn btn-outline-success btn-lg px-4 shadow-sm d-flex align-items-center gap-2">
            <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Forklift
        </a>

        <!-- View All Forklifts -->
        <a href="{{ route('admin.forklifts.index') }}" 
           class="btn btn-outline-info btn-lg px-4 shadow-sm d-flex align-items-center gap-2">
            <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
            </svg>
            View All Forklifts
        </a>
    @endif
</div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons (for check-circle) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>
