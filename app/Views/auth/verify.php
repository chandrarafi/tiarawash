<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - TiaraWash</title>
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
                        'pulse-slow': 'pulse 2s infinite',
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

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .otp-input:focus {
            transform: scale(1.05);
        }
    </style>
</head>

<body class="h-full font-sans gradient-bg">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Card Container -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 animate-slide-up">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full mb-6 animate-float">
                        <i class="fas fa-shield-alt text-white text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi OTP</h1>
                    <p class="text-gray-600">
                        Kami telah mengirim kode verifikasi 6 digit ke email Anda
                    </p>
                    <p class="text-primary-600 font-medium mt-2" id="userEmail">
                        <?= session()->get('registration_data')['email'] ?? 'email@example.com' ?>
                    </p>
                </div>

                <!-- OTP Form -->
                <form id="otpForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4 text-center">
                            Masukkan Kode OTP
                        </label>
                        <div class="flex justify-center space-x-3">
                            <input type="text" id="otp1" maxlength="1" class="otp-input appearance-none block border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                            <input type="text" id="otp2" maxlength="1" class="otp-input appearance-none block border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                            <input type="text" id="otp3" maxlength="1" class="otp-input appearance-none block border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                            <input type="text" id="otp4" maxlength="1" class="otp-input appearance-none block border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                            <input type="text" id="otp5" maxlength="1" class="otp-input appearance-none block border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                            <input type="text" id="otp6" maxlength="1" class="otp-input appearance-none block border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                        </div>
                        <div id="otp-error" class="text-red-500 text-sm mt-2 text-center hidden"></div>
                    </div>

                    <!-- Timer -->
                    <div class="text-center">
                        <div id="timer" class="text-sm text-gray-600 mb-4">
                            Kode akan kedaluwarsa dalam <span id="countdown" class="font-bold text-primary-600">05:00</span>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            id="verifyBtn"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200 transform hover:scale-105">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-check text-primary-200 group-hover:text-primary-100"></i>
                            </span>
                            <span id="verifyBtnText">Verifikasi</span>
                        </button>
                    </div>

                    <!-- Resend OTP -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Tidak menerima kode?</p>
                        <button
                            type="button"
                            id="resendBtn"
                            class="text-primary-500 hover:text-primary-600 font-medium transition duration-200 disabled:text-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-redo mr-1"></i>
                            <span id="resendBtnText">Kirim ulang kode</span>
                        </button>
                    </div>

                    <!-- Back to Register -->
                    <div class="text-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            Ingin mengubah email?
                            <a href="<?= base_url('auth/register') ?>" class="text-primary-500 hover:text-primary-600 font-medium transition duration-200">
                                Kembali ke registrasi
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Tips -->
            <div class="bg-white bg-opacity-90 rounded-lg p-4 animate-fade-in">
                <h3 class="text-sm font-medium text-gray-900 mb-2">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Tips:
                </h3>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>• Periksa folder spam/junk email Anda</li>
                    <li>• Pastikan koneksi internet stabil</li>
                    <li>• Kode OTP berlaku selama 5 menit</li>
                </ul>
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
            let countdownTimer;
            let timeLeft = 300; // 5 minutes in seconds

            // Initialize countdown
            startCountdown();

            // OTP input handling
            $('.otp-input').on('input', function() {
                const current = $(this);
                const value = current.val();

                // Only allow numbers
                if (!/^\d$/.test(value)) {
                    current.val('');
                    return;
                }

                // Move to next input
                const nextInput = current.next('.otp-input');
                if (nextInput.length && value) {
                    nextInput.focus();
                }

                // Check if all inputs are filled
                checkOTPComplete();
            });

            // Handle backspace
            $('.otp-input').on('keydown', function(e) {
                if (e.key === 'Backspace' && !$(this).val()) {
                    const prevInput = $(this).prev('.otp-input');
                    if (prevInput.length) {
                        prevInput.focus();
                    }
                }
            });

            // Handle paste
            $('.otp-input').on('paste', function(e) {
                e.preventDefault();
                const pastedData = e.originalEvent.clipboardData.getData('text');
                const digits = pastedData.replace(/\D/g, '').slice(0, 6);

                $('.otp-input').each(function(index) {
                    if (digits[index]) {
                        $(this).val(digits[index]);
                    }
                });

                checkOTPComplete();
            });

            function checkOTPComplete() {
                const otpValues = $('.otp-input').map(function() {
                    return $(this).val();
                }).get();
                const isComplete = otpValues.every(val => val !== '') && otpValues.length === 6;

                if (isComplete) {
                    $('#verifyBtn').removeClass('opacity-50').prop('disabled', false);
                } else {
                    $('#verifyBtn').addClass('opacity-50').prop('disabled', true);
                }
            }

            function startCountdown() {
                countdownTimer = setInterval(function() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    $('#countdown').text(display);

                    if (timeLeft <= 0) {
                        clearInterval(countdownTimer);
                        $('#timer').html('<span class="text-red-500 font-bold">Kode OTP telah kedaluwarsa</span>');
                        $('#verifyBtn').prop('disabled', true).addClass('opacity-50');
                        $('#resendBtn').prop('disabled', false).removeClass('text-gray-400').addClass('text-primary-500');
                    }

                    timeLeft--;
                }, 1000);
            }

            // Verify OTP
            $('#otpForm').submit(function(e) {
                e.preventDefault();

                const otpCode = $('.otp-input').map(function() {
                    return $(this).val();
                }).get().join('');

                if (otpCode.length !== 6) {
                    showError('Masukkan kode OTP 6 digit');
                    return;
                }

                const verifyBtn = $('#verifyBtn');
                const verifyBtnText = $('#verifyBtnText');
                const originalText = verifyBtnText.text();

                // Show loading state
                verifyBtn.prop('disabled', true);
                verifyBtnText.html('<i class="fas fa-spinner fa-spin mr-2"></i>Memverifikasi...');

                $.ajax({
                    url: '<?= base_url('auth/verify') ?>',
                    type: 'POST',
                    data: {
                        otp_code: otpCode
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('success', 'Berhasil!', response.message);
                            setTimeout(() => {
                                window.location.href = response.redirect;
                            }, 2000);
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function() {
                        showError('Terjadi kesalahan. Silakan coba lagi.');
                    },
                    complete: function() {
                        // Reset button state
                        verifyBtn.prop('disabled', false);
                        verifyBtnText.text(originalText);
                    }
                });
            });

            // Resend OTP
            $('#resendBtn').click(function() {
                const resendBtn = $(this);
                const resendBtnText = $('#resendBtnText');
                const originalText = resendBtnText.text();

                // Show loading state
                resendBtn.prop('disabled', true);
                resendBtnText.html('<i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...');

                $.ajax({
                    url: '<?= base_url('auth/resend-otp') ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('success', 'Berhasil!', response.message);
                            // Reset timer
                            clearInterval(countdownTimer);
                            timeLeft = 300;
                            startCountdown();
                            // Clear OTP inputs
                            $('.otp-input').val('');
                            $('#otp1').focus();
                        } else {
                            showAlert('error', 'Gagal', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Error', 'Terjadi kesalahan. Silakan coba lagi.');
                    },
                    complete: function() {
                        // Reset button state
                        resendBtn.prop('disabled', false);
                        resendBtnText.text(originalText);
                    }
                });
            });

            function showError(message) {
                $('#otp-error').text(message).removeClass('hidden');
                $('.otp-input').addClass('border-red-500');

                setTimeout(() => {
                    $('#otp-error').addClass('hidden');
                    $('.otp-input').removeClass('border-red-500');
                }, 3000);
            }

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

            // Focus first input on load
            $('#otp1').focus();

            // Initially disable verify button
            checkOTPComplete();
        });
    </script>
</body>

</html>