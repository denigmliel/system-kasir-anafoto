@extends('layouts.auth')

@section('title', 'Login - ANA FOTOCOPY')

@push('head')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 40%, #9b2e5a 75%, #c9302c 100%);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 12px 10px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .login-container {
            background: white;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 340px;
            width: min(340px, 92vw);
            display: flex;
            flex-direction: column;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            background: linear-gradient(135deg, #c9302c 0%, #e74c3c 100%);
            padding: 18px 16px 14px;
            flex: none;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 10px;
        }

        .logo {
            width: 96px;
            height: 96px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: 4px solid white;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .login-left h1 {
            font-size: 26px;
            margin-bottom: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .login-right {
            padding: 16px 16px 18px;
            flex: none;
            background: white;
        }

        .login-header {
            margin-bottom: 16px;
        }

        .login-header h2 {
            color: #1e3c72;
            font-size: 22px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            color: #475467;
            padding: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .toggle-password:focus {
            outline: 2px solid rgba(30, 60, 114, 0.25);
            outline-offset: 3px;
        }

        .toggle-password:hover {
            background: rgba(30, 60, 114, 0.08);
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
            pointer-events: none;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-group select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: block;
            max-width: 100%;
            min-width: 0;
            padding-right: 44px;
            background-image:
                linear-gradient(45deg, transparent 50%, #475467 50%),
                linear-gradient(135deg, #475467 50%, transparent 50%),
                linear-gradient(to right, #e0e0e0, #e0e0e0);
            background-position:
                calc(100% - 18px) 50%,
                calc(100% - 12px) 50%,
                calc(100% - 32px) 50%;
            background-size: 8px 8px, 8px 8px, 1px 16px;
            background-repeat: no-repeat;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1e3c72;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .form-group input.is-invalid,
        .form-group select.is-invalid {
            border-color: #c9302c;
        }

        .invalid-feedback {
            color: #c9302c;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .forgot-password {
            color: #c9302c;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #c9302c 0%, #e74c3c 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(201, 48, 44, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(201, 48, 44, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #fee;
            color: #c9302c;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #28a745;
            border: 1px solid #cfc;
        }

        @media (max-width: 520px) {
            .login-container {
                width: min(360px, 96vw);
            }

            .form-group input,
            .form-group select {
                font-size: 16px;
            }

            .form-group select {
                width: 100%;
                max-width: calc(100vw - 40px);
            }
        }

        @media (max-width: 400px) {
            .form-group select {
                max-width: calc(100vw - 28px);
            }
        }

        @media (max-width: 640px) {
            .login-container {
                border-radius: 14px;
                max-height: none;
                min-height: auto;
                overflow: auto;
            }

            .login-left {
                padding: 16px 14px 12px;
            }

            .login-right {
                padding: 14px 14px 16px;
            }

        }
    </style>
@endpush

@section('content')
    <div class="login-container">
        <div class="login-left">
            <div class="logo">
                <img src="{{ asset('img/logo.png') }}" alt="Logo ANA Fotocopy">
            </div>
            <h1>ANA FOTOCOPY</h1>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Selamat Datang!</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="role">Masuk sebagai</label>
                    <select
                        id="role"
                        name="role"
                        class="@error('role') is-invalid @enderror"
                        required
                    >
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="gudang" {{ old('role') === 'gudang' ? 'selected' : '' }}>Gudang</option>
                    </select>
                    @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="@error('email') is-invalid @enderror"
                        required
                        autofocus
                        placeholder=""
                    >
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="@error('password') is-invalid @enderror"
                            required
                            placeholder="********"
                        >
                        <button type="button" class="toggle-password" id="toggle-password" aria-label="Tampilkan password">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label for="remember">Ingat Saya</label>
                    </div>
                    <a href="#" class="forgot-password">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">LOGIN</button>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 600);
                }, 3500);
            });

            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                const eyeOpen = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                `;

                const eyeClosed = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3l18 18M9.88 9.88A3 3 0 0112 9c1.657 0 3 1.343 3 3a3 3 0 01-.88 2.12M9.88 9.88 4.12 4.12M9.88 9.88 5.5 14.3M14.12 14.12 18.5 9.7M3.98 8.223C5.845 6.09 8.701 5 12 5c2.026 0 3.913.48 5.46 1.313M20.02 15.777C18.155 17.91 15.299 19 12 19c-2.026 0-3.913-.48-5.46-1.313" />
                    </svg>
                `;

                const updateToggle = () => {
                    const isHidden = passwordInput.type === 'password';
                    togglePassword.innerHTML = isHidden ? eyeOpen : eyeClosed;
                    togglePassword.setAttribute('aria-label', isHidden ? 'Tampilkan password' : 'Sembunyikan password');
                };

                togglePassword.addEventListener('click', () => {
                    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                    updateToggle();
                    passwordInput.focus();
                    passwordInput.setSelectionRange(passwordInput.value.length, passwordInput.value.length);
                });

                updateToggle();
            }
        });
    </script>
@endpush
