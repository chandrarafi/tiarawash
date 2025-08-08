<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: white;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }

        .report-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
        }

        .report-header {
            text-align: center;
            padding: 1rem;
            border-bottom: 2px solid #333;
        }

        .company-name {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 0.3rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-subtitle {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.3rem;
        }

        .company-address {
            font-size: 0.7rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .report-title {
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 0.5rem;
        }

        .report-body {
            padding: 1rem;
        }

        .section {
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 0.2rem;
            margin-bottom: 0.5rem;
        }

        .info-table {
            width: 100%;
            margin-bottom: 0.8rem;
        }

        .info-table td {
            padding: 0.2rem 0;
            vertical-align: top;
            font-size: 0.8rem;
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
            margin-bottom: 0.8rem;
        }

        .service-table th,
        .service-table td {
            border: 1px solid #333;
            padding: 0.4rem;
            text-align: left;
            font-size: 0.75rem;
        }

        .service-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.7rem;
        }

        .service-table .text-center {
            text-align: center;
        }

        .service-table .text-right {
            text-align: right;
        }

        .total-section {
            border-top: 1px solid #333;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
            font-size: 0.8rem;
        }

        .total-final {
            font-weight: bold;
            font-size: 0.9rem;
            border-top: 1px solid #333;
            padding-top: 0.3rem;
            margin-top: 0.3rem;
        }

        .status-paid {
            background: #28a745;
            color: white;
            padding: 0.15rem 0.3rem;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.7rem;
        }

        /* Ensure consistent styling */
        h5 {
            margin-top: 0.8rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
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

                <?php if (!empty($bookingDetails)): ?>
                    <?php
                    // Group by vehicle
                    $vehicles = [];
                    foreach ($bookingDetails as $detail) {
                        $vehicleKey = $detail['no_plat'];
                        if (!isset($vehicles[$vehicleKey])) {
                            $vehicles[$vehicleKey] = [
                                'no_plat' => $detail['no_plat'],
                                'merk_kendaraan' => $detail['merk_kendaraan'] ?? '',
                                'services' => []
                            ];
                        }
                        $vehicles[$vehicleKey]['services'][] = $detail;
                    }
                    ?>

                    <?php foreach ($vehicles as $vehicle): ?>
                        <h5 style="margin-top: 0.8rem; margin-bottom: 0.5rem; font-weight: bold; font-size: 0.8rem;">
                            Kendaraan: <?= esc($vehicle['no_plat']) ?>
                            <?php if (!empty($vehicle['merk_kendaraan'])): ?>
                                - <?= esc($vehicle['merk_kendaraan']) ?>
                            <?php endif; ?>
                        </h5>

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
                                <?php foreach ($vehicle['services'] as $service): ?>
                                    <tr>
                                        <td><?= esc($service['nama_layanan']) ?></td>
                                        <td class="text-center"><?= $service['durasi_menit'] ?> menit</td>
                                        <td class="text-center"><?= date('H:i', strtotime($service['jam'])) ?></td>
                                        <td class="text-right">Rp <?= number_format($service['harga'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
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
    </div>
</body>

</html>