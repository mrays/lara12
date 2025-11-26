@extends('layouts.sneat-auth')

@section('title', 'Exputra Billing - Login')

@section('content')
<!-- Login -->
<div class="card">
    <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-4">
            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('images/exputra-logo.png') }}" alt="Exputra" style="height: 25px;">
                </span>
                <span class="app-brand-text demo text-body fw-bolder">Exputra Billing</span>
            </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-2">Welcome to Exputra Billing! 👋</h4>
        <p class="mb-4">Please sign in to your account and start the adventure</p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                       placeholder="Enter your email or username" value="{{ old('email') }}" required autofocus autocomplete="username" />
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            <small>Forgot Password?</small>
                        </a>
                    @endif
                </div>
                <div class="input-group input-group-merge">
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" 
                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                           aria-describedby="password" required autocomplete="current-password" />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember" />
                    <label class="form-check-label" for="remember_me"> Remember Me </label>
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
            </div>
        </form>

        {{-- Register link hidden --}}
        {{-- @if (Route::has('register'))
            <p class="text-center">
                <span>New on our platform?</span>
                <a href="{{ route('register') }}">
                    <span>Create an account</span>
                </a>
            </p>
        @endif --}}
    </div>
</div>
<!-- /Login -->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const passwordToggle = document.querySelector('.form-password-toggle .input-group-text');
    const passwordInput = document.querySelector('#password');
    
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('formAuthentication');
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email) {
                e.preventDefault();
                document.getElementById('email').focus();
                return false;
            }
            
            if (!password) {
                e.preventDefault();
                document.getElementById('password').focus();
                return false;
            }
        });
    }
});
</script>
@endpush
