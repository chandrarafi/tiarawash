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
            --success-color: #28a745;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fc;
            --gradient-primary: linear-gradient(135deg, #0088cc, #00aaff);
            --gradient-success: linear-gradient(135deg, #28a745, #20c997);
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

        .success-container {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }

        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .success-header {
            background: var(--gradient-success);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .success-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
        }

        .receipt-body {
            padding: 2rem;
        }

        .receipt-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .receipt-section h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: var(--dark-color);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            background: var(--gradient-success);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge i {
            margin-right: 0.5rem;
        }

        .service-detail {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
        }

        .service-detail:last-child {
            margin-bottom: 0;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-custom {
            flex: 1;
            min-width: 200px;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 136, 204, 0.3);
        }

        .receipt-footer {
            background: #f8f9fa;
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            background: #e9ecef;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 3rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .btn-custom {
                min-width: 100%;
            }
        }

        @media print {

            .navbar-custom,
            .action-buttons {
                display: none !important;
            }

            .success-container {
                padding: 0;
            }

            .success-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
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

    <!-- Success Content -->
    <div class="success-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="success-card">
                        <div class="success-header">
                            <div class="success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <h2>Pembayaran Berhasil!</h2>
                            <p class="mb-0 opacity-75">Terima kasih atas pembayaran Anda</p>
                        </div>

                        <div class="receipt-body">
                            <!-- Transaction Info -->
                            <div class="receipt-section">
                                <h5><i class="fas fa-receipt me-2"></i>Informasi Transaksi</h5>
                                <div class="info-row">
                                    <span class="info-label">Nomor Transaksi</span>
                                    <span class="info-value"><?= esc($transaksi['no_transaksi']) ?></span>
                                </div>
                                <?php if (!empty($transaksi['nama_karyawan'])): ?>
                                    <div class="info-row">
                                        <span class="info-label">Dilayani Oleh</span>
                                        <span class="info-value">
                                            <i class="fas fa-user-tie me-1"></i><?= esc($transaksi['nama_karyawan']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="info-row">
                                    <span class="info-label">Tanggal Transaksi</span>
                                    <span class="info-value"><?= date('d F Y H:i', strtotime($transaksi['created_at'])) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Status Pembayaran</span>
                                    <span class="status-badge">
                                        <i class="fas fa-check-circle"></i>
                                        <?= ucfirst($transaksi['status_pembayaran']) ?>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Metode Pembayaran</span>
                                    <span class="info-value"><?= ucfirst($transaksi['metode_pembayaran']) ?></span>
                                </div>
                                <?php if (!empty($transaksi['bukti_pembayaran'])): ?>
                                    <div class="info-row">
                                        <span class="info-label">Bukti Pembayaran</span>
                                        <span class="info-value">
                                            <a href="<?= base_url($transaksi['bukti_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Lihat Bukti
                                            </a>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Customer Info -->
                            <div class="receipt-section">
                                <h5><i class="fas fa-user me-2"></i>Informasi Pelanggan</h5>
                                <div class="info-row">
                                    <span class="info-label">Nama</span>
                                    <span class="info-value"><?= esc($transaksi['nama_pelanggan'] ?? 'Guest') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Nomor Plat</span>
                                    <span class="info-value"><?= esc($transaksi['no_plat']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Jenis Kendaraan</span>
                                    <span class="info-value"><?= ucfirst($transaksi['jenis_kendaraan']) ?></span>
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="receipt-section">
                                <h5><i class="fas fa-cogs me-2"></i>Detail Layanan</h5>
                                <?php foreach ($details as $detail): ?>
                                    <div class="service-detail">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= esc($detail['nama_item']) ?></strong>
                                                <div class="text-muted small">
                                                    Qty: <?= $detail['jumlah'] ?> × Rp <?= number_format($detail['harga'], 0, ',', '.') ?>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <strong>Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="info-row mt-3">
                                    <span class="info-label"><strong>Total Pembayaran</strong></span>
                                    <span class="info-value"><strong>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></strong></span>
                                </div>
                            </div>

                            <!-- QR Code for Reference -->
                            <div class="receipt-section text-center">
                                <h5><i class="fas fa-qrcode me-2"></i>QR Code Transaksi</h5>
                                <?php if (isset($qr_code) && $qr_code): ?>
                                    <div class="qr-code">
                                        <img src="<?= $qr_code ?>" alt="QR Code Transaksi" class="img-fluid" style="max-width: 200px;">
                                    </div>
                                    <small class="text-muted">Scan QR code untuk detail transaksi dan info pembayaran</small>
                                <?php else: ?>
                                    <div class="qr-code">
                                        <i class="fas fa-qrcode text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <small class="text-muted">QR code tidak tersedia</small>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button onclick="window.print()" class="btn btn-custom btn-outline-custom">
                                    <i class="fas fa-print me-2"></i>Cetak Struk
                                </button>
                                <a href="<?= site_url('/') ?>" class="btn btn-custom btn-primary-custom">
                                    <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                </a>
                            </div>
                        </div>

                        <div class="receipt-footer">
                            <p class="mb-1"><strong>TiaraWash</strong></p>
                            <small class="text-muted">Layanan Cuci Kendaraan Premium</small><br>
                            <small class="text-muted">Jl. Raya Cuci No. 123, Jakarta Selatan</small><br>
                            <small class="text-muted">Telp: +62 21 1234 5678 | Email: info@tiarawash.com</small>
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
        // Auto-focus for better user experience
        document.addEventListener('DOMContentLoaded', function() {
            // Show success animation
            const successCard = document.querySelector('.success-card');
            successCard.style.opacity = '0';
            successCard.style.transform = 'translateY(30px)';

            setTimeout(() => {
                successCard.style.transition = 'all 0.6s ease';
                successCard.style.opacity = '1';
                successCard.style.transform = 'translateY(0)';
            }, 100);

            // Show celebration effect
            setTimeout(() => {
                // You can add confetti or other celebration effects here
                console.log('Payment successful! 🎉');
            }, 500);
        });

        // Print function enhancement
        function printReceipt() {
            window.print();
        }

        // Auto-redirect after 5 minutes (optional)
        setTimeout(() => {
            if (confirm('Anda akan diarahkan ke beranda dalam 30 detik. Klik OK untuk tetap di halaman ini.')) {
                // User chose to stay
            } else {
                window.location.href = '<?= site_url('/') ?>';
            }
        }, 300000); // 5 minutes
    </script>
</body>

</html>