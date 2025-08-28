<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'User Dashboard') - Monitoring Ibu Hamil</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Notification System -->
    <script src="{{ asset('js/notification-system.js') }}"></script>
    
    <script>
    // Update notification badge real-time
    function updateNotificationBadge() {
        fetch('/user/notifikasi/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error updating badge:', error));
    }
    
    // Update badge setiap 30 detik
    setInterval(updateNotificationBadge, 30000);
    
    // Update badge saat halaman load
    document.addEventListener('DOMContentLoaded', updateNotificationBadge);
    </script>
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --transition: all 0.3s ease;
            --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --border-radius: 0.35rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fc;
            overflow-x: hidden;
        }

        /* ===== DESKTOP LAYOUT ===== */
        @media (min-width: 769px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                width: var(--sidebar-width);
                background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
                color: white;
                z-index: 1000;
                transition: var(--transition);
                box-shadow: var(--shadow);
                overflow: hidden;
            }

            .sidebar.collapsed {
                width: var(--sidebar-collapsed-width);
            }

            .sidebar.collapsed .sidebar-brand-text,
            .sidebar.collapsed .sidebar-subtitle,
            .sidebar.collapsed .nav-link span,
            .sidebar.collapsed .nav-badge {
                opacity: 0;
                transform: translateX(-20px);
                pointer-events: none;
            }

            .sidebar.collapsed .nav-link {
                justify-content: center;
                padding: 1rem 0.5rem;
            }

            .sidebar.collapsed .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }

            .main-content {
                margin-left: var(--sidebar-width);
                transition: var(--transition);
                min-height: 100vh;
                padding: 2rem;
                padding-top: 4rem;
            }

            .main-content.expanded {
                margin-left: var(--sidebar-collapsed-width);
            }

            .sidebar-toggle-outside {
                display: block !important;
            }

            .mobile-menu-toggle {
                display: none !important;
            }

            .top-nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                padding: 1rem 0;
                border-bottom: 1px solid #e3e6f0;
            }

            .top-nav-left h1 {
                font-size: 2rem;
                font-weight: 700;
                color: var(--dark-color);
                margin-bottom: 0.25rem;
            }

            .top-nav-left small {
                font-size: 1rem;
                color: var(--secondary-color);
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 0.75rem 1.5rem;
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
            }

            .user-avatar {
                width: 45px;
                height: 45px;
                background: var(--primary-color);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                font-weight: 700;
            }
        }

        /* ===== MOBILE LAYOUT ===== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                height: 100vh;
                width: 100%;
                background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
                color: white;
                z-index: 1000;
                transition: var(--transition);
                overflow-y: auto;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-toggle-outside {
                display: none !important;
            }

            .mobile-menu-toggle {
                display: block !important;
                background: var(--primary-color);
                border: none;
                color: white;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--transition);
                box-shadow: var(--shadow);
                font-size: 1.2rem;
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1001;
            }

            .mobile-menu-toggle:hover {
                background: var(--secondary-color);
                transform: scale(1.1);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
                padding-top: 1rem;
            }

            .top-nav {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                margin-bottom: 1.5rem;
                padding: 1rem 0;
                border-bottom: 1px solid #e3e6f0;
            }

            .top-nav-left h1 {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--dark-color);
                margin-bottom: 0.25rem;
            }

            .top-nav-left small {
                font-size: 0.9rem;
                color: var(--secondary-color);
            }

            .top-nav-right {
                width: 100%;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 1rem;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.5rem 1rem;
                background: white;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                background: var(--primary-color);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                font-weight: 700;
            }

            /* Mobile Overlay */
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: var(--transition);
            }

            .mobile-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }

        /* ===== SHARED STYLES ===== */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-brand-text {
            font-size: 1.2rem;
            font-weight: 700;
            transition: var(--transition);
        }

        .sidebar-subtitle {
            font-size: 0.8rem;
            opacity: 0.8;
            transition: var(--transition);
        }

        .sidebar-nav {
            padding: 1rem 0;
            overflow-y: auto;
            height: calc(100vh - 140px);
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            position: relative;
            gap: 0.75rem;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .nav-link span {
            flex: 1;
            transition: var(--transition);
        }

        .nav-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .nav-link:hover .nav-badge {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Sidebar Toggle Button (Outside Sidebar) */
        .sidebar-toggle-outside {
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            z-index: 1001;
            box-shadow: var(--shadow);
            font-size: 1.2rem;
        }

        .sidebar-toggle-outside:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .sidebar-toggle-outside:active {
            transform: scale(0.95);
        }

        /* ===== RESPONSIVE CARDS ===== */
        .card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ===== RESPONSIVE GRID ===== */
        .row {
            margin: 0 -0.75rem;
        }

        .col-md-4,
        .col-md-6,
        .col-md-8,
        .col-md-12 {
            padding: 0 0.75rem;
        }

        @media (max-width: 768px) {

            .col-md-4,
            .col-md-6,
            .col-md-8 {
                margin-bottom: 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        /* ===== RESPONSIVE CHARTS ===== */
        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 250px;
            }
        }

        /* ===== RESPONSIVE TABLES ===== */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 600px;
        }

        @media (max-width: 768px) {
            .table {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 0.5rem;
            }
        }

        /* ===== RESPONSIVE FORMS ===== */
        .form-control {
            border-radius: var(--border-radius);
            border: 1px solid #d1d3e2;
            padding: 0.75rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn {
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* ===== LOADING STATES ===== */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ===== UTILITY CLASSES ===== */
        .text-center-mobile {
            text-align: center;
        }

        .d-none-mobile {
            display: block;
        }

        .d-block-mobile {
            display: none;
        }

        @media (max-width: 768px) {
            .text-center-mobile {
                text-align: center;
            }

            .d-none-mobile {
                display: none;
            }

            .d-block-mobile {
                display: block;
            }
        }

        /* ===== ANIMATIONS ===== */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .slide-in-left {
            animation: slideInLeft 0.3s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        /* ===== SCROLLBAR STYLING ===== */
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    @php
                        $pengaturan = \App\Models\PengaturanAplikasi::first();
                    @endphp
                    
                    @if($pengaturan && $pengaturan->logo)
                        <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
                    @else
                        <i class="fas fa-baby"></i>
                    @endif
                </div>
                <div>
                    <div class="sidebar-brand-text">{{ $pengaturan->nama_aplikasi ?? 'Monitoring Ibu Hamil' }}</div>
                    <div class="sidebar-subtitle">Dashboard User</div>
                </div>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('user.monitoring') }}" class="nav-link {{ request()->routeIs('user.monitoring*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Monitoring Harian</span>
                    <span class="nav-badge">Live</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('user.profil') }}" class="nav-link {{ request()->routeIs('user.profil*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    <span>Profil & QR Code</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('user.rekomendasi') }}" class="nav-link {{ request()->routeIs('user.rekomendasi*') ? 'active' : '' }}">
                    <i class="fas fa-lightbulb"></i>
                    <span>Rekomendasi Personal</span>
                </a>
            </div>





            <div class="nav-item">
                <a href="{{ route('user.notifikasi') }}" class="nav-link {{ request()->routeIs('user.notifikasi*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Notifikasi</span>
                    <span class="nav-badge" id="notification-badge" style="display: {{ Auth::user()->notifikasi()->where('is_read', false)->count() > 0 ? 'inline' : 'none' }};">
                        {{ Auth::user()->notifikasi()->where('is_read', false)->count() }}
                    </span>
                </a>
            </div>





            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar Toggle Button (Outside Sidebar) -->
    <button class="sidebar-toggle-outside" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="top-nav-left">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <small class="text-muted">@yield('breadcrumb', 'Selamat datang di dashboard monitoring kesehatan')</small>
            </div>

            <div class="top-nav-right">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ substr(Auth::user()->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ Auth::user()->nama_lengkap }}</div>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Sidebar Scripts -->
    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                try {
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('mainContent');
                    
                    if (sidebar && mainContent) {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');

                        // Update toggle button icon
                        const icon = this.querySelector('i');
                        if (icon) {
                            if (sidebar.classList.contains('collapsed')) {
                                icon.className = 'fas fa-chevron-right';
                            } else {
                                icon.className = 'fas fa-bars';
                            }
                        }

                        // Save state to localStorage
                        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                    }
                } catch (error) {
                    console.log('Error toggling sidebar:', error);
                }
            });
        }

        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                try {
                    const sidebar = document.getElementById('sidebar');
                    const mobileOverlay = document.getElementById('mobileOverlay');
                    
                    if (sidebar && mobileOverlay) {
                        sidebar.classList.toggle('show');
                        mobileOverlay.classList.toggle('show');

                        // Prevent body scroll when sidebar is open
                        if (sidebar.classList.contains('show')) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = '';
                        }
                    }
                } catch (error) {
                    console.log('Error toggling mobile menu:', error);
                }
            });
        }

        // Mobile Overlay Click
        const mobileOverlay = document.getElementById('mobileOverlay');
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function() {
                try {
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) {
                        sidebar.classList.remove('show');
                        this.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                } catch (error) {
                    console.log('Error handling overlay click:', error);
                }
            });
        }

        // Restore sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                const toggleButton = document.getElementById('sidebarToggle');
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('mainContent');
                
                if (toggleButton && sidebar && mainContent) {
                    const icon = toggleButton.querySelector('i');
                    
                    if (sidebarCollapsed) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                        if (icon) {
                            icon.className = 'fas fa-chevron-right';
                        }
                    }

                    // Add fade-in animation to main content
                    mainContent.classList.add('fade-in');
                }
            } catch (error) {
                console.log('Error restoring sidebar state:', error);
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            try {
                const sidebar = document.getElementById('sidebar');
                const mobileOverlay = document.getElementById('mobileOverlay');

                if (window.innerWidth > 768 && sidebar && mobileOverlay) {
                    // Desktop: hide mobile sidebar and overlay
                    sidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            } catch (error) {
                console.log('Error handling window resize:', error);
            }
        });

        // Close mobile sidebar when clicking outside
        document.addEventListener('click', function(e) {
            try {
                const sidebar = document.getElementById('sidebar');
                const mobileMenuToggle = document.getElementById('mobileMenuToggle');
                const mobileOverlay = document.getElementById('mobileOverlay');

                if (window.innerWidth <= 768 &&
                    sidebar && mobileMenuToggle && mobileOverlay &&
                    !sidebar.contains(e.target) &&
                    !mobileMenuToggle.contains(e.target) &&
                    !mobileOverlay.contains(e.target)) {
                    sidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            } catch (error) {
                console.log('Error handling click outside:', error);
            }
        });








    </script>

    @yield('scripts')
    
    <!-- Loading State Management -->
    <script>
        // Prevent multiple event binding
        let eventListenersBound = false;
        
        // Initialize only once
        if (!eventListenersBound) {
            eventListenersBound = true;
            
            // Add loading state management
            document.addEventListener('DOMContentLoaded', function() {
                // Remove any existing loading states
                const loadingElements = document.querySelectorAll('.loading, .loading-spinner');
                loadingElements.forEach(el => el.remove());
                
                // Add page transition effect
                const mainContent = document.getElementById('mainContent');
                if (mainContent) {
                    mainContent.style.opacity = '0';
                    mainContent.style.transition = 'opacity 0.3s ease';
                    
                    setTimeout(() => {
                        mainContent.style.opacity = '1';
                    }, 100);
                }
            });
            
            // Handle page navigation
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('#') && !link.href.includes('javascript:')) {
                    // Add loading state for navigation
                    const mainContent = document.getElementById('mainContent');
                    if (mainContent) {
                        mainContent.style.opacity = '0.7';
                    }
                }
            });
        }
    </script>
</body>

</html>