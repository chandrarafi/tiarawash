<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TiaraWash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6f7ff',
                            100: '#bae7ff',
                            500: '#0088cc',
                            600: '#0077b6',
                            700: '#005577',
                        },
                        secondary: {
                            100: '#f8f9fc',
                            200: '#e3e6f0',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #00aaff 0%, #0077b6 100%);
        }

        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>

<body class="h-full font-sans">
    <div class="min-h-full flex">
        <!-- Left Panel - Login Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo and Title -->
                <div class="text-center mb-8 animate-fade-in">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full mb-4 animate-float">
                        <i class="fas fa-car-wash text-white text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">TiaraWash</h1>
                    <p class="text-gray-600 mt-2">Sistem Manajemen Cuci Kendaraan</p>
                </div>

                <!-- Login Form -->
                <div class="animate-slide-up">
                    <form id="loginForm" class="space-y-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user text-primary-500 mr-2"></i>Username atau Email
                            </label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200"
                                placeholder="Masukkan username atau email">
                            <div id="username-error" class="text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock text-primary-500 mr-2"></i>Password
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    class="appearance-none block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200"
                                    placeholder="Masukkan password">
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <div id="password-error" class="text-red-500 text-sm mt-1 hidden"></div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    class="h-4 w-4 text-primary-500 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="#" class="text-sm text-primary-500 hover:text-primary-600 font-medium">
                                Lupa password?
                            </a>
                        </div>

                        <div>
                            <button
                                type="submit"
                                id="loginBtn"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200 transform hover:scale-105">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i class="fas fa-sign-in-alt text-primary-200 group-hover:text-primary-100"></i>
                                </span>
                                <span id="loginBtnText">Masuk</span>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Belum punya akun?
                                <a href="<?= base_url('auth/register') ?>" class="text-primary-500 hover:text-primary-600 font-medium transition duration-200">
                                    Daftar sekarang
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Panel - Hero Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 gradient-bg">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                <div class="relative h-full flex items-center justify-center">
                    <div class="text-center text-white animate-fade-in">
                        <div class="mb-8">
                            <i class="fas fa-car text-6xl mb-4 animate-float"></i>
                            <h2 class="text-4xl font-bold mb-4">Selamat Datang!</h2>
                            <p class="text-xl opacity-90 max-w-md mx-auto">
                                Kelola bisnis cuci kendaraan Anda dengan mudah dan efisien
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 max-w-sm mx-auto">
                            <div class="glass-effect rounded-lg p-4 text-left">
                                <i class="fas fa-calendar-check text-primary-200 text-2xl mb-2"></i>
                                <h3 class="font-semibold text-gray-800">Manajemen Booking</h3>
                                <p class="text-sm text-gray-600">Kelola jadwal booking pelanggan</p>
                            </div>
                            <div class="glass-effect rounded-lg p-4 text-left">
                                <i class="fas fa-users text-primary-200 text-2xl mb-2"></i>
                                <h3 class="font-semibold text-gray-800">Data Pelanggan</h3>
                                <p class="text-sm text-gray-600">Sistem customer relationship</p>
                            </div>
                            <div class="glass-effect rounded-lg p-4 text-left">
                                <i class="fas fa-chart-line text-primary-200 text-2xl mb-2"></i>
                                <h3 class="font-semibold text-gray-800">Laporan Bisnis</h3>
                                <p class="text-sm text-gray-600">Analisis performa dan keuangan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div id="alertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 m-4 max-w-sm w-full animate-slide-up">
            <div class="flex items-center mb-4">
                <div id="alertIcon" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                    <i id="alertIconClass" class="text-xl"></i>
                </div>
                <h3 id="alertTitle" class="text-lg font-medium text-gray-900"></h3>
            </div>
            <p id="alertMessage" class="text-sm text-gray-600 mb-4"></p>
            <button
                id="alertOkBtn"
                class="w-full bg-primary-500 hover:bg-primary-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                OK
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIcon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Login form submission
            $('#loginForm').submit(function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.text-red-500').addClass('hidden');
                $('#username, #password').removeClass('border-red-500');

                const loginBtn = $('#loginBtn');
                const loginBtnText = $('#loginBtnText');
                const originalText = loginBtnText.text();

                // Show loading state
                loginBtn.prop('disabled', true);
                loginBtnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');

                $.ajax({
                    url: '<?= base_url('auth/login') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('success', 'Berhasil!', response.message);
                            setTimeout(() => {
                                window.location.href = response.redirect || '<?= base_url('admin') ?>';
                            }, 1500);
                        } else {
                            showAlert('error', 'Login Gagal', response.message);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.errors) {
                            // Show field-specific errors
                            Object.keys(response.errors).forEach(field => {
                                $(`#${field}`).addClass('border-red-500');
                                $(`#${field}-error`).text(response.errors[field]).removeClass('hidden');
                            });
                        } else {
                            showAlert('error', 'Error', 'Terjadi kesalahan. Silakan coba lagi.');
                        }
                    },
                    complete: function() {
                        // Reset button state
                        loginBtn.prop('disabled', false);
                        loginBtnText.text(originalText);
                    }
                });
            });

            // Alert modal functions
            function showAlert(type, title, message) {
                const alertModal = $('#alertModal');
                const alertIcon = $('#alertIcon');
                const alertIconClass = $('#alertIconClass');
                const alertTitle = $('#alertTitle');
                const alertMessage = $('#alertMessage');

                // Set icon and colors based on type
                if (type === 'success') {
                    alertIcon.removeClass().addClass('flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3 bg-green-100');
                    alertIconClass.removeClass().addClass('fas fa-check text-green-600 text-xl');
                } else if (type === 'error') {
                    alertIcon.removeClass().addClass('flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3 bg-red-100');
                    alertIconClass.removeClass().addClass('fas fa-times text-red-600 text-xl');
                }

                alertTitle.text(title);
                alertMessage.text(message);
                alertModal.removeClass('hidden').addClass('flex');
            }

            // Close alert modal
            $('#alertOkBtn').click(function() {
                $('#alertModal').removeClass('flex').addClass('hidden');
            });

            // Close modal on backdrop click
            $('#alertModal').click(function(e) {
                if (e.target === this) {
                    $(this).removeClass('flex').addClass('hidden');
                }
            });
        });
    </script>
</body>

</html>