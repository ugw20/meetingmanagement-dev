<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('images/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="{{ asset('images/favicon/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Fallback favicon jika folder favicon tidak ada -->
    @if(!file_exists(public_path('images/favicon')))
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    @endif

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* Custom CSS untuk fixed header dan footer */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 57px;
        }
        
        .main-sidebar {
            position: fixed;
            top: 57px;
            left: 0;
            bottom: 0;
            z-index: 1029;
        }
        
        .content-wrapper {
            margin-top: 57px;
            min-height: calc(100vh - 57px - 53px);
            padding-bottom: 53px;
        }
        
        .main-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 53px;
            margin-left: 0;
        }
        
        /* Untuk sidebar collapsed */
        .sidebar-collapse .main-sidebar {
            transform: none !important;
        }
        
        /* Memastikan konten tidak tertutup */
        .content {
            padding: 20px 15px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .main-header {
                height: 50px;
            }
            
            .main-sidebar {
                top: 50px;
            }
            
            .content-wrapper {
                margin-top: 50px;
                min-height: calc(100vh - 50px - 53px);
            }
        }
        
        /* Memastikan breadcrumb tidak overlap */
        .content-header {
            padding-top: 10px;
            padding-bottom: 10px;
        }
        
        /* Smooth scrolling untuk anchor links */
        html {
            scroll-padding-top: 70px;
        }
        /* Custom table header styling */
        .table-header-custom {
            background-color: #2c3e50 !important;
            color: #ffffff !important;
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.85rem !important;
            padding: 15px 10px !important;
            border: none !important;
        }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar - Fixed Header -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge badge-danger navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        @if(auth()->user() && auth()->user()->notifications->count() > 0)
                            <span class="dropdown-item dropdown-header">{{ auth()->user()->unreadNotifications->count() }} Notifikasi Belum Dibaca</span>
                            
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="p-2 text-center">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-muted"><i class="fas fa-check-double mr-1"></i> Tandai semua dibaca</button>
                            </form>
                            @endif

                            <div class="dropdown-divider"></div>
                            
                            <div style="max-height: 300px; overflow-y: auto;">
                                @foreach(auth()->user()->notifications->take(5) as $notification)
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item {{ $notification->read_at ? 'text-muted' : 'font-weight-bold bg-light' }}" style="white-space: normal;">
                                        <div class="d-flex align-items-start">
                                            <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} {{ $notification->data['iconColor'] ?? 'text-primary' }} mt-1 mr-2"></i>
                                            <div>
                                                <p class="mb-0 text-sm">{{ $notification->data['title'] ?? 'Pemberitahuan' }}</p>
                                                <p class="text-xs {{ $notification->read_at ? 'text-muted' : 'text-dark' }} mb-1">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 60) }}</p>
                                                <p class="text-xs text-muted mb-0"><i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endforeach
                            </div>
                            
                            <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer text-center">Lihat Semua Notifikasi</a>
                        @else
                            <span class="dropdown-item dropdown-header">Belum ada notifikasi</span>
                        @endif
                    </div>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user mr-1"></i>
                        {{ Auth::user()->name }}
                        <i class="fas fa-caret-down ml-1"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar - Fixed Position -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link text-center py-3">
                <!-- Logo Image -->
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }} Logo" class="brand-image img-circle elevation-3" style="opacity: .8; max-height: 33px;">
                @elseif(file_exists(public_path('images/logo.svg')))
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }} Logo" class="brand-image img-circle elevation-3" style="opacity: .8; max-height: 33px;">
                @else
                    <i class="fas fa-handshake brand-icon"></i>
                @endif
                <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle img-circle elevation-2 text-light"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        <small class="text-light">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('meetings.index') }}" class="nav-link {{ request()->routeIs('meetings.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>Meeting</p>
            </a>
        </li>

        <!-- Menu Trash untuk Admin dan Manager -->
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <li class="nav-item">
            <a href="{{ route('trash.index') }}" class="nav-link {{ request()->routeIs('trash.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-trash"></i>
                <p>Tempat Sampah</p>
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('action-items.index') }}" class="nav-link {{ request()->routeIs('action-items.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Tindak Lanjut</p>
            </a>
        </li>

        @if(Auth::user()->isAdmin())
        <li class="nav-header">ADMINISTRASI</li>
        
        <li class="nav-item">
            <a href="{{ route('meeting-types.index') }}" class="nav-link {{ request()->routeIs('meeting-types.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-list"></i>
                <p>Jenis Meeting</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-building"></i>
                <p>Departemen</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Pengguna</p>
            </a>
        </li>
        @endif
    </ul>
</nav>
            </div>
        </aside>

        <!-- Content Wrapper - Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">@yield('title')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Page Content -->
                    <div class="content">
                        @yield('content')
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer - Fixed Position -->
        <footer class="main-footer bg-white border-top">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <strong>Copyright &copy; {{ date('Y') }} {{ config('app.name') }}.</strong>
                        All rights reserved.
                    </div>
                    <div class="col-sm-6 text-right">
                        <small class="text-muted">Version 1.0.0</small>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi AdminLTE
            $('[data-widget="pushmenu"]').PushMenu('collapse');
            
            // Handle responsive behavior
            function handleResponsive() {
                if ($(window).width() < 768) {
                    $('body').addClass('sidebar-collapse');
                } else {
                    $('body').removeClass('sidebar-collapse');
                }
            }
            
            // Initial call
            handleResponsive();
            
            // Handle window resize
            $(window).resize(function() {
                handleResponsive();
            });
            
            // Auto-hide alerts after 5 seconds
            $('.alert').delay(5000).fadeOut(300);
            
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 70
                    }, 1000);
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>