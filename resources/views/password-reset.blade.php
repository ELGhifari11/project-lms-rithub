<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ config('app.name') }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="40" r="0.5" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="80" r="1.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .icon-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ec4899, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.3);
        }

        .icon-container::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, #ec4899, #3b82f6);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.5;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }

        .icon-container i {
            color: white;
            font-size: 2rem;
        }

        .title {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ec4899, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: #ec4899;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1);
            transform: translateY(-1px);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            z-index: 1;
        }

        .form-input:focus + .input-icon {
            color: #ec4899;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 2;
            padding: 0.25rem;
            border-radius: 50%;
        }

        .toggle-password:hover {
            color: #ec4899;
            background: rgba(236, 72, 153, 0.1);
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .password-requirements {
            margin-top: 0.75rem;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.05), rgba(59, 130, 246, 0.05));
            border-radius: 12px;
            border-left: 4px solid #ec4899;
        }

        .requirement {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .requirement:last-child {
            margin-bottom: 0;
        }

        .requirement i {
            margin-right: 0.5rem;
            width: 12px;
            font-size: 0.75rem;
        }

        .requirement.valid {
            color: #059669;
        }

        .requirement.valid i {
            color: #059669;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ec4899, #3b82f6);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.3);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(236, 72, 153, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link a:hover {
            color: #ec4899;
            transform: translateX(-2px);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #16a34a;
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Tablet styles */
        @media (min-width: 768px) {
            .container {
                padding: 2.5rem;
                max-width: 450px;
            }

            .title {
                font-size: 2rem;
            }

            .form-input {
                padding: 1.25rem 1.25rem 1.25rem 3.5rem;
                font-size: 1.1rem;
            }

            .input-icon {
                left: 1.25rem;
                font-size: 1.2rem;
            }

            .toggle-password {
                right: 1.25rem;
                font-size: 1.2rem;
            }
        }

        /* Desktop styles */
        @media (min-width: 1024px) {
            body {
                padding: 2rem;
            }

            .container {
                padding: 3rem;
                max-width: 500px;
            }

            .icon-container {
                width: 90px;
                height: 90px;
            }

            .icon-container i {
                font-size: 2.25rem;
            }

            .title {
                font-size: 2.25rem;
            }

            .subtitle {
                font-size: 1rem;
            }
        }

        /* Animation for form entrance */
        .container {
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon-container">
                <i class="fas fa-key"></i>
            </div>
            <h1 class="title">Reset Password</h1>
            <p class="subtitle">Masukkan password baru Anda untuk melanjutkan</p>
        </div>

        <!-- Dynamic Success/Error Messages -->
        <div id="messageContainer" style="display: none;"></div>

        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
            @csrf

            <!-- Hidden token field -->
            <input type="hidden" name="token" value="{{ request('token') }}">

            <!-- Email field (hidden but required) -->
            <input type="hidden" name="email" value="{{ request('email') ?? old('email') }}">

            <!-- New Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password Baru</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Masukkan password baru"
                        required
                        autocomplete="new-password"
                    >
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="toggle-password" data-target="password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="password-requirements">
                    <div class="requirement" data-requirement="length">
                        <i class="fas fa-times"></i>
                        Minimal 8 karakter
                    </div>
                    <div class="requirement" data-requirement="uppercase">
                        <i class="fas fa-times"></i>
                        Mengandung huruf besar
                    </div>
                    <div class="requirement" data-requirement="lowercase">
                        <i class="fas fa-times"></i>
                        Mengandung huruf kecil
                    </div>
                    <div class="requirement" data-requirement="number">
                        <i class="fas fa-times"></i>
                        Mengandung angka
                    </div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Konfirmasi password baru"
                        required
                        autocomplete="new-password"
                    >
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="toggle-password" data-target="password_confirmation">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-key" style="margin-right: 0.5rem;"></i>
                Reset Password
            </button>
        </form>
    </div>

    <script>
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    targetInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password requirements validation
        const passwordInput = document.getElementById('password');
        const requirements = {
            length: (password) => password.length >= 8,
            uppercase: (password) => /[A-Z]/.test(password),
            lowercase: (password) => /[a-z]/.test(password),
            number: (password) => /\d/.test(password)
        };

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            Object.keys(requirements).forEach(req => {
                const element = document.querySelector(`[data-requirement="${req}"]`);
                const icon = element.querySelector('i');

                if (requirements[req](password)) {
                    element.classList.add('valid');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-check');
                } else {
                    element.classList.remove('valid');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-times');
                }
            });
        });

        // Show message function
        function showMessage(message, isSuccess = false) {
            const container = document.getElementById('messageContainer');
            container.style.display = 'block';
            container.className = isSuccess ? 'success-message' : 'error-message';
            container.innerHTML = `
                <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                ${message}
            `;

            // Scroll to message
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Hide message function
        function hideMessage() {
            document.getElementById('messageContainer').style.display = 'none';
        }

        // Form validation and AJAX submission
        document.getElementById('resetForm').addEventListener('submit', async function(e) {
            e.preventDefault(); // Always prevent default form submission

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const submitBtn = document.getElementById('submitBtn');
            const form = this;

            // Hide previous messages
            hideMessage();

            // Client-side validation
            if (password !== confirmPassword) {
                showMessage('Password dan konfirmasi password tidak cocok!');
                return;
            }

            // Check password requirements
            const allRequirementsMet = Object.keys(requirements).every(req =>
                requirements[req](password)
            );

            if (!allRequirementsMet) {
                showMessage('Password belum memenuhi semua persyaratan!');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>Memproses...';

            try {
                // Get form data
                const formData = new FormData(form);

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Make AJAX request
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Success - password reset berhasil
                    showMessage(data.message || 'Password berhasil direset!', true);
                } else {
                    // Error response
                    if (data.errors) {
                        // Validation errors
                        const errorMessages = Object.values(data.errors).flat();
                        showMessage(errorMessages.join('<br>'));
                    } else {
                        // General error message
                        showMessage(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                showMessage('Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-key" style="margin-right: 0.5rem;"></i>Reset Password';
            }
        });

        // Real-time password confirmation validation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#ef4444';
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });
    </script>
</body>
</html>
