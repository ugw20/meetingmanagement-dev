<!-- resources/views/layouts/app-guest.blade.php -->
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
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">
    
    <!-- Fallback favicon -->
    @if(!file_exists(public_path('images/favicon')))
    <link rel="apple-touch-icon" sizes="180x180" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🤝</text></svg>">
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🤝</text></svg>">
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🤝</text></svg>">
    <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🤝</text></svg>">
    @endif

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 50%, #f0fff4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        /* Background Ornaments */
        .background-ornaments {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .green-ornament {
            position: absolute;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            opacity: 0.08;
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .ornament-1 {
            width: 120px;
            height: 120px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .ornament-2 {
            width: 80px;
            height: 80px;
            top: 20%;
            right: 8%;
            animation-delay: 2s;
        }

        .ornament-3 {
            width: 150px;
            height: 150px;
            bottom: 15%;
            left: 8%;
            animation-delay: 4s;
        }

        .ornament-4 {
            width: 100px;
            height: 100px;
            bottom: 25%;
            right: 12%;
            animation-delay: 6s;
        }

        .ornament-5 {
            width: 60px;
            height: 60px;
            top: 45%;
            left: 15%;
            animation-delay: 1s;
        }

        .ornament-6 {
            width: 90px;
            height: 90px;
            top: 35%;
            right: 18%;
            animation-delay: 5s;
        }

        /* Floating Particles */
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            opacity: 0.1;
            border-radius: 50%;
            animation: float 10s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 8px;
            height: 8px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 6px;
            height: 6px;
            top: 60%;
            left: 20%;
            animation-delay: 1.5s;
        }

        .particle:nth-child(3) {
            width: 10px;
            height: 10px;
            top: 40%;
            right: 15%;
            animation-delay: 3s;
        }

        .particle:nth-child(4) {
            width: 7px;
            height: 7px;
            top: 70%;
            right: 25%;
            animation-delay: 4.5s;
        }

        .particle:nth-child(5) {
            width: 5px;
            height: 5px;
            top: 30%;
            left: 30%;
            animation-delay: 6s;
        }

        .particle:nth-child(6) {
            width: 9px;
            height: 9px;
            top: 50%;
            right: 30%;
            animation-delay: 2s;
        }

        .particle:nth-child(7) {
            width: 6px;
            height: 6px;
            top: 80%;
            left: 40%;
            animation-delay: 5s;
        }

        .particle:nth-child(8) {
            width: 8px;
            height: 8px;
            top: 25%;
            right: 35%;
            animation-delay: 7s;
        }

        /* Content Container */
        .guest-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
            }
            50% { 
                transform: translateY(-20px) rotate(180deg); 
            }
        }

        /* Loading Animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading {
            animation: spin 1s linear infinite;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .guest-container {
                max-width: 340px;
            }
            
            .background-ornaments .green-ornament {
                opacity: 0.05;
            }
            
            .floating-particles {
                display: none;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(16, 185, 129, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
    </style>

    @stack('styles')
</head>
<body class="hold-transition login-page">
    <!-- Background Ornaments -->
    <div class="background-ornaments">
        <div class="green-ornament ornament-1"></div>
        <div class="green-ornament ornament-2"></div>
        <div class="green-ornament ornament-3"></div>
        <div class="green-ornament ornament-4"></div>
        <div class="green-ornament ornament-5"></div>
        <div class="green-ornament ornament-6"></div>
    </div>
    
    <!-- Floating Particles -->
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Main Content -->
    <div class="guest-container">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });

            // Create additional floating particles dynamically
            function createParticles() {
                const particlesContainer = document.querySelector('.floating-particles');
                for (let i = 0; i < 4; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.random() * 8 + 4 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 10 + 's';
                    particle.style.opacity = Math.random() * 0.1 + 0.05;
                    particlesContainer.appendChild(particle);
                }
            }

            createParticles();

            // Add loading state to submit buttons
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const btnText = submitBtn.querySelector('.btn-text');
                        const btnIcon = submitBtn.querySelector('i');
                        
                        if (btnText) btnText.textContent = 'Processing...';
                        if (btnIcon) btnIcon.className = 'fas fa-spinner loading';
                        submitBtn.disabled = true;
                    }
                });
            });

            // Password toggle functionality for any password fields
            document.querySelectorAll('.password-toggle').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = this.closest('.input-with-icon').querySelector('input[type="password"], input[type="text"]');
                    if (passwordInput) {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>