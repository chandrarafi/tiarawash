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

    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .navbar-custom {
            background: white;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #333 !important;
        }

        .report-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .report-header {
            text-align: center;
            padding: 2rem;
            border-bottom: 3px solid #333;
        }

        .company-name {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .company-subtitle {
            font-size: 1rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .company-address {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .report-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 1rem;
        }

        .report-body {
            padding: 2rem;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #333;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .info-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .info-table td {
            padding: 0.5rem 0;
            vertical-align: top;
        }

        .info-table .label {
            width: 30%;
            font-weight: bold;
        }

        .info-table .separator {
            width: 5%;
            text-align: center;
        }

        .info-table .value {
            width: 65%;
        }

        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .service-table th,
        .service-table td {
            border: 1px solid #333;
            padding: 0.75rem;
            text-align: left;
        }

        .service-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .service-table .text-center {
            text-align: center;
        }

        .service-table .text-right {
            text-align: right;
        }

        .total-section {
            border-top: 2px solid #333;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .total-final {
            font-weight: bold;
            font-size: 1.1rem;
            border-top: 1px solid #333;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }



        .action-buttons {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #ddd;
            background: #f8f9fa;
        }

        .btn-report {
            margin: 0 0.5rem;
            padding: 0.75rem 1.5rem;
            border: 2px solid #333;
            background: white;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-report:hover {
            background: #333;
            color: white;
        }

        .btn-primary-report {
            background: #333;
            color: white;
        }

        .btn-primary-report:hover {
            background: #555;
            color: white;
        }

        .status-paid {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        @media print {

            .navbar-custom,
            .action-buttons {
                display: none !important;
            }

            .report-container {
                box-shadow: none;
                border: none;
                margin: 0;
                max-width: 100%;
            }

            body {
                background: white !important;
            }

            .btn-report {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .report-container {
                margin: 1rem;
            }

            .report-header,
            .report-body {
                padding: 1rem;
            }



            .action-buttons {
                padding: 1rem;
            }

            .btn-report {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
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
                <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Beranda
                </a>
            </div>
        </div>
    </nav>

    <!-- Report Content -->
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="company-name">TiaraWash</div>
            <div class="company-subtitle">Layanan Cuci Kendaraan Premium</div>
            <div class="company-address">
                Jl. Rawang Jundul, Padang Utara, kota Padang<br>
                Telp: +62 21 1234 5678 | Email: info@tiarawash.com
            </div>
            <div class="report-title">Faktur Pembayaran</div>
        </div>

        <!-- Body -->
        <div class="report-body">
            <!-- Transaction Information -->
            <div class="section">
                <div class="section-title">I. Informasi Transaksi</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Nomor Transaksi</td>
                        <td class="separator">:</td>
                        <td class="value"><?= esc($transaksi['no_transaksi']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Transaksi</td>
                        <td class="separator">:</td>
                        <td class="value"><?= date('d F Y', strtotime($transaksi['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Waktu Transaksi</td>
                        <td class="separator">:</td>
                        <td class="value"><?= date('H:i:s', strtotime($transaksi['created_at'])) ?> WIB</td>
                    </tr>
                    <tr>
                        <td class="label">Status Pembayaran</td>
                        <td class="separator">:</td>
                        <td class="value">
                            <span class="status-paid"><?= strtoupper($transaksi['status_pembayaran']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Metode Pembayaran</td>
                        <td class="separator">:</td>
                        <td class="value"><?= ucfirst($transaksi['metode_pembayaran']) ?></td>
                    </tr>

                </table>
            </div>

            <!-- Customer Information -->
            <div class="section">
                <div class="section-title">II. Informasi Pelanggan</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Nama Pelanggan</td>
                        <td class="separator">:</td>
                        <td class="value"><?= esc($transaksi['nama_pelanggan'] ?? 'Guest') ?></td>
                    </tr>
                    <?php if (!empty($transaksi['no_hp'])): ?>
                        <tr>
                            <td class="label">Nomor Telepon</td>
                            <td class="separator">:</td>
                            <td class="value"><?= esc($transaksi['no_hp']) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Service Details -->
            <div class="section">
                <div class="section-title">III. Rincian Layanan</div>

                <?php if (!empty($booking_details)): ?>
                    <!-- Display vehicle information once (from first booking since all have same combined data) -->
                    <?php if (count($booking_details) > 0): ?>
                        <h5 style="margin-top: 1.5rem; margin-bottom: 1rem; font-weight: bold;">
                            Kendaraan: <?= esc($booking_details[0]['booking']['no_plat']) ?>
                            <?php if (!empty($booking_details[0]['booking']['merk_kendaraan'])): ?>
                                <br><span style="font-weight: normal; font-size: 0.9em;">Merk: <?= esc($booking_details[0]['booking']['merk_kendaraan']) ?></span>
                            <?php endif; ?>
                        </h5>
                    <?php endif; ?>

                    <!-- Services table -->
                    <table class="service-table">
                        <thead>
                            <tr>
                                <th style="width: 40%">Nama Layanan</th>
                                <th style="width: 15%" class="text-center">Durasi</th>
                                <th style="width: 15%" class="text-center">Waktu</th>
                                <th style="width: 30%" class="text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking_details as $detail): ?>
                                <tr>
                                    <td><?= esc($detail['layanan']['nama_layanan']) ?></td>
                                    <td class="text-center"><?= $detail['layanan']['durasi_menit'] ?> menit</td>
                                    <td class="text-center"><?= date('H:i', strtotime($detail['booking']['jam'])) ?></td>
                                    <td class="text-right">Rp <?= number_format($detail['layanan']['harga'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <table class="service-table">
                        <thead>
                            <tr>
                                <th style="width: 70%">Layanan</th>
                                <th style="width: 30%" class="text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Layanan Cuci Kendaraan</td>
                                <td class="text-right">Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Total Section -->
                <div class="total-section">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></span>
                    </div>
                    <div class="total-row total-final">
                        <span>TOTAL PEMBAYARAN:</span>
                        <span>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>


        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?= site_url('receipt/pdf/' . $transaksi['no_transaksi']) ?>" target="_blank" class="btn-report">
                <i class="fas fa-print me-2"></i>Cetak
            </a>
            <a href="<?= site_url('/') ?>" class="btn-report btn-primary-report">
                <i class="fas fa-home me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

</html>

</html>