<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
            --accent-color: #f8f9fc;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 420px;
            margin: 0 auto;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 25px 20px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }

        .login-header h4 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #e1e1e1;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            border-color: var(--primary-color);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            border: 1px solid #e1e1e1;
            background-color: #f8f9fa;
        }

        .btn-login {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            border-radius: 15px;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .remember-me {
            color: #6c757d;
        }

        .input-group {
            margin-bottom: 5px;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card position-relative">
                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="login-header">
                    <!-- Anda bisa menambahkan logo perusahaan di sini -->
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="brand-logo" onerror="this.style.display='none'">
                    <h4><i class="bi bi-shield-lock me-2"></i>Login Admin</h4>
                </div>

                <div class="login-body">
                    <!-- Alert untuk pesan error/success -->
                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('message') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Alert untuk error -->
                    <div id="loginError" class="alert alert-danger alert-dismissible fade show" style="display: none;" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span id="errorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <form id="loginForm" method="post">
                        <div class="mb-4">
                            <label for="username" class="form-label">Username atau Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required
                                    placeholder="Masukkan username atau email">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Masukkan password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label remember-me" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100" id="btnLogin">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toggle Password Visibility
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Handle form submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                // Show loading overlay
                $('#loadingOverlay').css('display', 'flex');

                // Disable form
                $('#loginForm :input').prop('disabled', true);

                // Get form data
                const username = $('#username').val();
                const password = $('#password').val();
                const remember = $('#remember').prop('checked') ? 'on' : 'off';

                $.ajax({
                    url: '<?= site_url('auth/login') ?>',
                    type: 'POST',
                    data: {
                        username: username,
                        password: password,
                        remember: remember
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect;
                        } else {
                            $('#errorMessage').text(response.message);
                            $('#loginError').show();
                        }
                    },
                    error: function() {
                        $('#errorMessage').text('Terjadi kesalahan. Silakan coba lagi.');
                        $('#loginError').show();
                    },
                    complete: function() {
                        // Hide loading overlay
                        $('#loadingOverlay').hide();
                        // Enable form
                        $('#loginForm :input').prop('disabled', false);
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').not('#loginError').alert('close');
            }, 5000);
        });
    </script>
</body>

</html>