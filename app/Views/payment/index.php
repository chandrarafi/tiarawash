<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0088cc;
            --secondary-color: #00aaff;
            --accent-color: #ff6b35;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fc;
            --gradient-primary: linear-gradient(135deg, #0088cc, #00aaff);
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .payment-container {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .payment-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .payment-header h2 {
            margin: 0;
            font-weight: 700;
        }

        .payment-body {
            padding: 2rem;
        }

        .booking-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .booking-summary h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .service-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .service-item:last-child {
            border-bottom: none;
        }

        .service-details h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .service-details small {
            color: #666;
        }

        .service-price {
            font-weight: 700;
            color: var(--primary-color);
        }

        .total-amount {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: center;
        }

        .total-amount h3 {
            margin: 0;
            font-weight: 800;
            color: var(--primary-color);
            font-size: 2rem;
        }

        .payment-methods {
            margin-top: 2rem;
        }

        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background: rgba(0, 136, 204, 0.05);
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background: rgba(0, 136, 204, 0.1);
        }

        .payment-method.selected::after {
            content: '✓';
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 24px;
            height: 24px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .payment-method input[type="radio"] {
            display: none;
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .payment-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .payment-info small {
            color: #666;
        }

        .btn-pay {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 2rem;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 136, 204, 0.3);
        }

        .btn-pay:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        .customer-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .countdown-timer {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .countdown-timer h6 {
            margin: 0;
            color: #721c24;
            font-weight: 600;
        }

        .countdown {
            font-size: 1.5rem;
            font-weight: 800;
            color: #721c24;
            margin-top: 0.5rem;
        }

        .payment-security {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .payment-security i {
            color: #0c5460;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .payment-security small {
            color: #0c5460;
        }

        /* Modern Countdown Timer Styles */
        .countdown-timer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .countdown-timer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%, transparent 75%, rgba(255, 255, 255, 0.1) 75%),
                linear-gradient(-45deg, rgba(255, 255, 255, 0.1) 25%, transparent 25%, transparent 75%, rgba(255, 255, 255, 0.1) 75%);
            background-size: 30px 30px;
            animation: moveStripes 2s linear infinite;
            opacity: 0.3;
        }

        .countdown-timer h6 {
            margin: 0 0 1.5rem 0;
            font-weight: 700;
            color: white;
            font-size: 1.2rem;
            position: relative;
            z-index: 2;
        }

        .countdown-container {
            position: relative;
            z-index: 2;
        }

        .countdown {
            font-size: 3.5rem;
            font-weight: 900;
            font-family: 'Arial', sans-serif;
            background: rgba(255, 255, 255, 0.15);
            padding: 1.5rem 3rem;
            border-radius: 15px;
            display: inline-block;
            letter-spacing: 0.1em;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.4s ease;
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .countdown::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            border-radius: 15px;
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .timer-label {
            font-size: 0.9rem;
            margin-top: 1rem;
            opacity: 0.9;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* Warning State */
        .countdown-timer.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            animation: shake-warning 3s ease-in-out infinite;
        }

        .countdown-warning {
            background: rgba(255, 255, 255, 0.25) !important;
            animation: pulse-warning 1.5s infinite;
            border-color: rgba(255, 255, 255, 0.4) !important;
        }

        /* Danger State */
        .countdown-timer.danger {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            animation: shake-danger 1s ease-in-out infinite;
        }

        .countdown-danger {
            background: rgba(220, 53, 69, 0.3) !important;
            animation: pulse-danger 0.8s infinite;
            border-color: #dc3545 !important;
            color: #dc3545 !important;
            text-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }

        /* Animations */
        @keyframes moveStripes {
            0% {
                background-position: 0 0, 0 0;
            }

            100% {
                background-position: 30px 30px, -30px -30px;
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                opacity: 0.3;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        @keyframes pulse-warning {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }
        }

        @keyframes pulse-danger {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 15px rgba(220, 53, 69, 0);
            }
        }

        @keyframes shake-warning {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-2px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(2px);
            }
        }

        @keyframes shake-danger {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Progress Bar */
        .timer-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 0 0 20px 20px;
            transition: width 1s linear;
        }

        .timer-progress.warning {
            background: #ffc107;
        }

        .timer-progress.danger {
            background: #dc3545;
        }

        .upload-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .upload-area {
            position: relative;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(0, 136, 204, 0.05);
        }

        .upload-area.drag-over {
            border-color: var(--primary-color);
            background: rgba(0, 136, 204, 0.1);
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .upload-placeholder {
            pointer-events: none;
        }

        .upload-preview {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 6px;
            padding: 1rem;
        }

        .file-preview-container {
            text-align: center;
        }

        .image-preview {
            max-width: 100%;
        }

        .preview-img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .pdf-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            max-width: 200px;
            margin: 0 auto;
        }

        .pdf-icon {
            font-size: 2rem;
            color: #dc3545;
            margin-right: 0.5rem;
        }

        .pdf-info {
            text-align: left;
        }

        .file-info {
            flex: 1;
        }

        #fileIcon.fa-file-image {
            color: #28a745;
        }

        #fileIcon.fa-file-pdf {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <i class="fas fa-car-wash me-2"></i>TiaraWash
            </a>

            <div class="ms-auto">
                <a href="<?= site_url('/') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Beranda
                </a>
            </div>
        </div>
    </nav>

    <!-- Payment Content -->
    <div class="payment-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="payment-card">
                        <div class="payment-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h2>
                                        <i class="fas fa-credit-card me-2"></i>
                                        Pembayaran Booking
                                    </h2>
                                    <p class="mb-0 opacity-75">Kode Booking: <strong><?= esc($kode_booking) ?></strong></p>
                                </div>
                                <div>
                                    <?php if (isset($booking_details[0]['booking']['status'])): ?>
                                        <?php
                                        $status = $booking_details[0]['booking']['status'];
                                        $statusColors = [
                                            'menunggu_konfirmasi' => 'warning',
                                            'dikonfirmasi' => 'info',
                                            'selesai' => 'success',
                                            'dibatalkan' => 'danger',
                                            'batal' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                            'dikonfirmasi' => 'Dikonfirmasi',
                                            'selesai' => 'Selesai',
                                            'dibatalkan' => 'Dibatalkan',
                                            'batal' => 'Dibatalkan'
                                        ];
                                        $statusColor = $statusColors[$status] ?? 'secondary';
                                        $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?> fs-6 px-3 py-2">
                                            <?= $statusLabel ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="payment-body">
                            <!-- Countdown Timer -->
                            <div class="countdown-timer" id="countdownTimer">
                                <h6><i class="fas fa-hourglass-half me-2"></i>Batas Waktu Pembayaran</h6>
                                <div class="countdown-container">
                                    <div class="countdown" id="countdown">30:00</div>
                                    <div class="timer-label">Selesaikan pembayaran sebelum waktu habis</div>
                                </div>
                                <div class="timer-progress" id="timerProgress" style="width: 100%;"></div>
                            </div>

                            <!-- Customer Info -->
                            <?php if ($pelanggan): ?>
                                <div class="customer-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-warning me-2"></i>
                                        <div>
                                            <strong>Pelanggan:</strong> <?= esc($pelanggan['nama_pelanggan']) ?>
                                            <?php if ($pelanggan['no_hp']): ?>
                                                | <i class="fas fa-phone me-1"></i><?= esc($pelanggan['no_hp']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Booking Summary -->
                            <div class="booking-summary">
                                <h5><i class="fas fa-clipboard-list me-2"></i>Ringkasan Booking</h5>

                                <?php foreach ($booking_details as $detail): ?>
                                    <div class="service-item">
                                        <div class="service-details">
                                            <h6><?= esc($detail['layanan']['nama_layanan']) ?></h6>
                                            <small>
                                                <i class="fas fa-clock me-1"></i><?= esc($detail['booking']['jam']) ?>
                                                | <i class="fas fa-calendar me-1"></i><?= date('d/m/Y', strtotime($detail['booking']['tanggal'])) ?>
                                                <br>
                                                <i class="fas fa-car me-1"></i><?= esc($detail['booking']['no_plat']) ?>
                                                <?php if (!empty($detail['booking']['merk_kendaraan'])): ?>
                                                    <br><small class="text-muted">Merk: <?= esc($detail['booking']['merk_kendaraan']) ?></small>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="service-price">
                                            Rp <?= number_format($detail['layanan']['harga'], 0, ',', '.') ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="total-amount">
                                    <h3>Total: Rp <?= number_format($total_harga, 0, ',', '.') ?></h3>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="payment-methods">
                                <h5><i class="fas fa-wallet me-2"></i>Pilih Metode Pembayaran</h5>

                                <form id="paymentForm">
                                    <input type="hidden" name="kode_booking" value="<?= esc($kode_booking) ?>">

                                    <?php foreach ($payment_methods as $key => $method): ?>
                                        <label for="payment_<?= $key ?>" class="payment-method">
                                            <input type="radio" id="payment_<?= $key ?>" name="metode_pembayaran" value="<?= $key ?>" <?= $key === 'transfer' ? 'checked' : '' ?>>
                                            <div class="d-flex align-items-center">
                                                <div class="payment-icon">
                                                    <i class="<?= $method['icon'] ?>"></i>
                                                </div>
                                                <div class="payment-info flex-grow-1">
                                                    <h6><?= esc($method['name']) ?></h6>
                                                    <small><?= esc($method['description']) ?></small>
                                                    <?php if (isset($method['account_info'])): ?>
                                                        <div class="account-info mt-2 p-3 bg-light rounded">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <strong>Bank:</strong><br>
                                                                    <?= esc($method['account_info']['bank']) ?>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>No. Rekening:</strong><br>
                                                                    <span class="text-primary fw-bold"><?= esc($method['account_info']['account_number']) ?></span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <strong>Atas Nama:</strong><br>
                                                                    <?= esc($method['account_info']['account_name']) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>

                                    <!-- Upload Bukti Pembayaran -->
                                    <div class="upload-section mt-4" id="uploadSection">
                                        <h6><i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran <span class="text-muted">(Opsional)</span></h6>
                                        <p class="text-muted small mb-3">
                                            Silahkan transfer sesuai nominal di atas ke rekening yang tertera. Anda dapat mengupload bukti pembayaran sekarang atau nanti melalui dashboard.
                                        </p>

                                        <div class="upload-area" id="uploadArea">
                                            <input type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                                                accept=".jpg,.jpeg,.png,.pdf" class="file-input">
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt text-primary" style="font-size: 2rem;"></i>
                                                <p class="mb-1"><strong>Klik atau drag & drop file di sini</strong></p>
                                                <p class="text-muted small mb-0">
                                                    Format: JPG, PNG, PDF (Max: 2MB)
                                                </p>
                                            </div>
                                            <div class="upload-preview" id="uploadPreview" style="display: none;">
                                                <!-- File Preview Area -->
                                                <div class="file-preview-container mb-3">
                                                    <div id="imagePreview" class="image-preview" style="display: none;">
                                                        <img id="previewImage" src="" alt="Preview" class="preview-img">
                                                    </div>
                                                    <div id="pdfPreview" class="pdf-preview" style="display: none;">
                                                        <div class="pdf-icon">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </div>
                                                        <div class="pdf-info">
                                                            <strong>PDF Document</strong>
                                                            <div class="text-muted small">Ready to upload</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- File Info -->
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="file-info">
                                                        <div class="d-flex align-items-center">
                                                            <i id="fileIcon" class="fas fa-file text-success me-2"></i>
                                                            <div>
                                                                <div id="fileName" class="fw-bold"></div>
                                                                <div id="fileSize" class="text-muted small"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                                        <i class="fas fa-times"></i> Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-pay" id="payBtn" disabled>
                                        <i class="fas fa-lock me-2"></i>Bayar Sekarang
                                    </button>
                                </form>
                            </div>

                            <!-- Payment Security Info -->
                            <div class="payment-security">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <strong>Pembayaran Aman</strong>
                                </div>
                                <small>Transaksi Anda dilindungi dengan enkripsi SSL 256-bit</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            const payBtn = document.getElementById('payBtn');

            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove selected class from all methods
                    paymentMethods.forEach(m => m.classList.remove('selected'));

                    // Add selected class to clicked method
                    this.classList.add('selected');

                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Show upload section (always shown for transfer)
                    const uploadSection = document.getElementById('uploadSection');
                    uploadSection.style.display = 'block';

                    // Enable pay button
                    payBtn.disabled = false;
                });
            });

            // Countdown timer (15 minutes)
            let timeLeft = 15 * 60; // 15 minutes in seconds
            const countdownElement = document.getElementById('countdown');

            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;

                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Waktu Habis',
                        text: 'Batas waktu pembayaran telah habis. Silakan buat booking baru.',
                        confirmButtonText: 'Ke Beranda',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = '<?= site_url('/') ?>';
                    });
                    return;
                }

                timeLeft--;
            }

            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);

            // File upload handling
            const fileInput = document.getElementById('bukti_pembayaran');
            const uploadArea = document.getElementById('uploadArea');
            const uploadPlaceholder = uploadArea.querySelector('.upload-placeholder');
            const uploadPreview = document.getElementById('uploadPreview');
            const fileName = document.getElementById('fileName');
            const removeFile = document.getElementById('removeFile');

            // Click to upload
            uploadArea.addEventListener('click', function(e) {
                // Prevent double click if clicking directly on file input
                if (e.target === fileInput) {
                    return;
                }
                e.preventDefault();
                fileInput.click();
            });

            // Prevent file input click from bubbling up
            fileInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });

            // Remove file
            removeFile.addEventListener('click', function() {
                // Reset file input
                fileInput.value = '';

                // Reset preview elements
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('pdfPreview').style.display = 'none';
                document.getElementById('previewImage').src = '';
                document.getElementById('fileName').textContent = '';
                document.getElementById('fileSize').textContent = '';

                // Show upload placeholder again
                uploadPlaceholder.style.display = 'block';
                uploadPreview.style.display = 'none';
            });

            function handleFileSelect(file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire('Error', 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.', 'error');
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error', 'Ukuran file terlalu besar. Maksimal 2MB.', 'error');
                    return;
                }

                // Update file info
                fileName.textContent = file.name;
                document.getElementById('fileSize').textContent = formatFileSize(file.size);

                // Update file icon based on type
                const fileIcon = document.getElementById('fileIcon');
                const imagePreview = document.getElementById('imagePreview');
                const pdfPreview = document.getElementById('pdfPreview');
                const previewImage = document.getElementById('previewImage');

                // Reset previews
                imagePreview.style.display = 'none';
                pdfPreview.style.display = 'none';

                if (file.type.startsWith('image/')) {
                    // Handle image preview
                    fileIcon.className = 'fas fa-file-image text-success me-2';

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);

                } else if (file.type === 'application/pdf') {
                    // Handle PDF preview
                    fileIcon.className = 'fas fa-file-pdf text-danger me-2';
                    pdfPreview.style.display = 'block';
                }

                // Show preview section
                uploadPlaceholder.style.display = 'none';
                uploadPreview.style.display = 'block';
            }

            // Format file size helper
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Form submission
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const selectedMethod = document.querySelector('input[name="metode_pembayaran"]:checked');
                if (!selectedMethod) {
                    Swal.fire('Error', 'Silakan pilih metode pembayaran', 'error');
                    return;
                }

                payBtn.disabled = true;
                payBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses Pembayaran...';

                const formData = new FormData(this);

                fetch('<?= site_url('payment/process') ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: data.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Lihat Struk',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                window.location.href = data.data.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: data.message || 'Terjadi kesalahan saat memproses pembayaran',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                            confirmButtonColor: '#dc3545'
                        });
                    })
                    .finally(() => {
                        payBtn.disabled = false;
                        payBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Bayar Sekarang';
                    });
            });

            // Auto-select transfer method and show upload section on page load
            const transferMethod = document.querySelector('.payment-method');
            if (transferMethod) {
                transferMethod.classList.add('selected');
                const radio = transferMethod.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }

                // Show upload section immediately
                const uploadSection = document.getElementById('uploadSection');
                if (uploadSection) {
                    uploadSection.style.display = 'block';
                }

                // Enable pay button
                const payBtn = document.getElementById('payBtn');
                if (payBtn) {
                    payBtn.disabled = false;
                }
            }

            // Initialize countdown timer
            <?php if (isset($payment_info['time_remaining']) && $payment_info['time_remaining'] > 0): ?>
                initCountdownTimer(<?= $payment_info['time_remaining'] ?>);
            <?php endif; ?>
        });

        // Enhanced Countdown Timer Functions
        function initCountdownTimer(timeRemaining) {
            const countdownElement = document.getElementById('countdown');
            const timerContainer = document.getElementById('countdownTimer');
            const progressBar = document.getElementById('timerProgress');
            const totalTime = timeRemaining;
            let secondsLeft = timeRemaining;

            function updateCountdown() {
                const minutes = Math.floor(secondsLeft / 60);
                const seconds = secondsLeft % 60;

                const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                countdownElement.textContent = display;

                // Update progress bar
                const progressPercentage = (secondsLeft / totalTime) * 100;
                progressBar.style.width = progressPercentage + '%';

                // Reset classes
                countdownElement.className = 'countdown';
                timerContainer.className = 'countdown-timer';
                progressBar.className = 'timer-progress';

                // Change style based on time remaining
                if (secondsLeft <= 300 && secondsLeft > 60) { // 5 minutes to 1 minute
                    countdownElement.classList.add('countdown-warning');
                    timerContainer.classList.add('warning');
                    progressBar.classList.add('warning');
                } else if (secondsLeft <= 60) { // Last minute
                    countdownElement.classList.add('countdown-danger');
                    timerContainer.classList.add('danger');
                    progressBar.classList.add('danger');
                }

                // Check if time is up
                if (secondsLeft <= 0) {
                    clearInterval(timer);
                    progressBar.style.width = '0%';
                    handleTimeout();
                    return;
                }

                secondsLeft--;
            }

            // Update immediately and then every second
            updateCountdown();
            const timer = setInterval(updateCountdown, 1000);
        }

        function handleTimeout() {
            Swal.fire({
                icon: 'error',
                title: 'Waktu Pembayaran Habis',
                text: 'Batas waktu pembayaran telah berakhir. Booking akan dibatalkan secara otomatis.',
                confirmButtonText: 'Kembali ke Beranda',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = '<?= site_url('/') ?>';
            });
        }
    </script>
</body>

</html>