<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app-guest')

@section('title', 'Login')

@section('content')
<div class="login-container">
    <div class="login-box">
        <!-- Header dengan ornament hijau -->
        <div class="login-header">
            <div class="header-ornament">
                <div class="ornament-circle ornament-1"></div>
                <div class="ornament-circle ornament-2"></div>
                <div class="ornament-circle ornament-3"></div>
                <div class="ornament-circle ornament-4"></div>
            </div>
            <div class="login-logo">
                <div class="logo-container">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Meeting Management" class="logo-image">
                    @else
                        <div class="logo-fallback">
                            <i class="fas fa-handshake"></i>
                        </div>
                    @endif
                </div>
                <h1>Meeting<span>Management</span></h1>
                <p class="tagline">Efficient Meeting Solutions</p>
            </div>
        </div>
        
        <!-- Card Login yang lebih compact -->
        <div class="login-card">
            <div class="card-header">
                <h2>Welcome Back!</h2>
                <p>Sign in to continue to your dashboard</p>
            </div>
            
            <div class="card-body">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Login failed:</strong> Please check your credentials
                    </div>
                @endif

                <form action="{{ route('login') }}" method="post" class="login-form">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') error @enderror" 
                                   placeholder="Enter your email" 
                                   value="{{ old('email') }}" 
                                   required autofocus>
                        </div>
                        @error('email')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') error @enderror" 
                                   placeholder="Enter your password" 
                                   required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember" class="custom-checkbox">
                            <label for="remember">
                                <span class="checkmark"></span>
                                Remember me
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                        <div class="forgot-password">
                            <a href="#">Forgot password?</a>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="login-btn">
                        <span class="btn-text">Sign In</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <!-- Additional Links -->
                <div class="login-links">
                    @if (Route::has('register'))
                    <p class="register-link">
                        Don't have an account? 
                        <a href="{{ route('register') }}">Create one here</a>
                    </p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; {{ date('Y') }} Meeting Management System. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Background Ornaments Hijau -->
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
</div>

<style>
/* Base Styles dengan tema hijau */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 50%, #f0fff4 100%);
    position: relative;
    overflow: hidden;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Ukuran login box yang lebih compact */
.login-box {
    width: 100%;
    max-width: 380px;
    z-index: 10;
    position: relative;
}

/* Header Styles dengan Ornament Hijau */
.login-header {
    text-align: center;
    margin-bottom: 30px;
    position: relative;
}

.header-ornament {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 110%;
    height: 80px;
    z-index: -1;
}

.ornament-circle {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    opacity: 0.15;
    animation: float 6s ease-in-out infinite;
}

.ornament-1 {
    width: 60px;
    height: 60px;
    top: 5px;
    left: 5%;
    animation-delay: 0s;
}

.ornament-2 {
    width: 45px;
    height: 45px;
    top: 20px;
    right: 10%;
    animation-delay: 1.5s;
}

.ornament-3 {
    width: 35px;
    height: 35px;
    top: 0px;
    right: 25%;
    animation-delay: 3s;
}

.ornament-4 {
    width: 50px;
    height: 50px;
    top: 15px;
    left: 20%;
    animation-delay: 4.5s;
}

.logo-container {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    border: 3px solid white;
    position: relative;
    z-index: 2;
}

.logo-image {
    width: 45px;
    height: 45px;
    object-fit: contain;
    border-radius: 8px;
}

.logo-fallback {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-fallback i {
    font-size: 1.5rem;
    color: #10b981;
}

.login-logo h1 {
    color: #1a202c;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    position: relative;
}

.login-logo h1 span {
    font-weight: 300;
    color: #10b981;
}

.login-logo .tagline {
    color: #64748b;
    font-size: 0.9rem;
    margin-top: 6px;
    font-weight: 500;
}

/* Card Styles yang lebih compact */
.login-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    position: relative;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.card-header {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    padding: 25px 30px;
    text-align: center;
    color: #065f46;
    border-bottom: 1px solid #dcfce7;
}

.card-header h2 {
    margin: 0 0 6px 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.card-header p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.85rem;
}

.card-body {
    padding: 30px;
}

/* Form Styles yang lebih compact */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #374151;
    font-size: 0.85rem;
}

.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-icon i:first-child {
    position: absolute;
    left: 12px;
    color: #94a3b8;
    z-index: 2;
    font-size: 0.9rem;
}

.input-with-icon .form-control {
    width: 100%;
    padding: 12px 45px 12px 40px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: #f8fafc;
    color: #1a202c;
}

.input-with-icon .form-control:focus {
    outline: none;
    border-color: #10b981;
    background: white;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.input-with-icon .form-control.error {
    border-color: #dc2626;
}

.password-toggle {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    z-index: 3;
}

.password-toggle:hover {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.password-toggle:active {
    transform: scale(0.95);
}

/* Error Messages */
.error-message {
    display: flex;
    align-items: center;
    color: #dc2626;
    font-size: 0.75rem;
    margin-top: 4px;
}

.error-message i {
    margin-right: 4px;
    font-size: 0.7rem;
}

/* Form Options */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.remember-me {
    display: flex;
    align-items: center;
}

.custom-checkbox {
    display: none;
}

.custom-checkbox + label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.85rem;
    color: #4b5563;
}

.checkmark {
    width: 16px;
    height: 16px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    margin-right: 6px;
    position: relative;
    transition: all 0.3s ease;
}

.custom-checkbox:checked + label .checkmark {
    background: #10b981;
    border-color: #10b981;
}

.custom-checkbox:checked + label .checkmark::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 10px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.forgot-password a {
    color: #10b981;
    text-decoration: none;
    font-size: 0.85rem;
    transition: color 0.3s ease;
    font-weight: 500;
}

.forgot-password a:hover {
    color: #059669;
    text-decoration: underline;
}

/* Login Button */
.login-btn {
    width: 100%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.login-btn:active {
    transform: translateY(0);
}

.login-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Alert Styles */
.alert {
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.alert-success {
    background: #f0fdf4;
    color: #065f46;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Links */
.login-links {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.register-link {
    color: #6b7280;
    font-size: 0.85rem;
    margin: 0;
}

.register-link a {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.register-link a:hover {
    color: #059669;
    text-decoration: underline;
}

/* Footer */
.login-footer {
    text-align: center;
    margin-top: 25px;
    color: #94a3b8;
    font-size: 0.75rem;
}

/* Background Ornaments Hijau yang Lebih Banyak */
.background-ornaments {
    position: absolute;
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
    position: absolute;
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

/* Floating Animation */
@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
    }
    50% { 
        transform: translateY(-20px) rotate(180deg); 
    }
}

.logo-container {
    animation: float 4s ease-in-out infinite;
}

/* Responsive Design */
@media (max-width: 480px) {
    .login-container {
        padding: 15px;
    }
    
    .login-box {
        max-width: 340px;
    }
    
    .card-body {
        padding: 25px 20px;
    }
    
    .form-options {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
    
    .login-logo h1 {
        font-size: 1.8rem;
    }
    
    .background-ornaments .green-ornament {
        opacity: 0.05;
    }
    
    .floating-particles {
        display: none;
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

/* Smooth transitions for all interactive elements */
* {
    transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality - FIXED VERSION
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type');
            const newType = type === 'password' ? 'text' : 'password';
            
            passwordInput.setAttribute('type', newType);
            
            // Update icon
            const icon = this.querySelector('i');
            if (newType === 'text') {
                icon.className = 'fas fa-eye-slash';
                this.setAttribute('title', 'Hide password');
            } else {
                icon.className = 'fas fa-eye';
                this.setAttribute('title', 'Show password');
            }
            
            // Focus back to input for better UX
            passwordInput.focus();
        });
        
        // Add hover effects
        passwordToggle.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        passwordToggle.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
    
    // Add loading state to login button
    const loginForm = document.querySelector('.login-form');
    const loginBtn = document.querySelector('.login-btn');
    
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function(e) {
            const btnText = loginBtn.querySelector('.btn-text');
            const btnIcon = loginBtn.querySelector('i');
            
            if (btnText && btnIcon) {
                loginBtn.disabled = true;
                btnText.textContent = 'Signing In...';
                btnIcon.className = 'fas fa-spinner loading';
            }
        });
    }
    
    // Create additional floating particles dynamically
    function createParticles() {
        const particlesContainer = document.querySelector('.floating-particles');
        if (particlesContainer) {
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
    }
    
    createParticles();
    
    // Add input focus effects
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>
@endsection