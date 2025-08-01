<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Booking PerBulan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header table {
            width: 100%;
        }



        .header .company-info {
            text-align: center;
        }

        .header .company-info h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header .company-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
        }

        .filter-info {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 9px;
        }

        .data-table td {
            vertical-align: middle;
        }

        .signature {
            margin-top: 40px;
        }

        .signature table {
            width: 100%;
        }

        .signature .right {
            text-align: right;
        }

        .badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 15%;">
                    <?php
                    $logoPath = FCPATH . 'images/logo.png';
                    if (file_exists($logoPath)) {
                        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        echo '<img src="' . $base64 . '" alt="Logo" style="width: 100px; height: auto; max-height: 80px;">';
                    } else {
                        echo '<div style="width: 80px; height: 60px; border: 2px solid #333; border-radius: 8px; text-align: center; line-height: 28px; font-weight: bold; font-size: 10px; color: #333;">
                            <div style="margin-top: 8px;">TIARA</div>
                            <div style="margin-top: -2px;">WASH</div>
                        </div>';
                    }
                    ?>
                </td>
                <td style="width: 70%;">
                    <div class="company-info">
                        <h2>Tiara Wash</h2>
                        <p>Alamat : Jl Rawang Jundul, Padang Utara, Kota Padang</p>
                        <p>Sumatera Barat 25127</p>
                        <p>Telp/Fax: 0813-6359-6965</p>
                    </div>
                </td>
                <td style="width: 15%;"></td>
            </tr>
        </table>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        Laporan Data Booking PerBulan
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Bulan :</strong> <?= $nama_bulan[$bulan] ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <strong>Tahun :</strong> <?= $tahun ?>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">kode booking</th>
                <th style="width: 15%;">idpelanggan</th>
                <th style="width: 10%;">tanggal</th>
                <th style="width: 8%;">jam</th>
                <th style="width: 10%;">noplat</th>
                <th style="width: 12%;">jenis kendaraan</th>
                <th style="width: 12%;">merk kendaraan</th>
                <th style="width: 17%;">layanan</th>
                <th style="width: 10%;">status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $index => $booking): ?>
                    <tr>
                        <td><?= sprintf('%02d', $index + 1) ?></td>
                        <td><?= esc($booking['kode_booking']) ?></td>
                        <td style="text-align: left; padding-left: 6px;"><?= esc($booking['nama_pelanggan'] ?? $booking['idpelanggan']) ?></td>
                        <td><?= date('d/m/Y', strtotime($booking['tanggal'])) ?></td>
                        <td><?= date('H:i', strtotime($booking['jam'])) ?></td>
                        <td><?= esc($booking['noplat']) ?></td>
                        <td><?= ucfirst(esc($booking['jenis_kendaraan'])) ?></td>
                        <td><?= esc($booking['merk_kendaraan'] ?? '-') ?></td>
                        <td style="text-align: left; padding-left: 6px;"><?= esc($booking['layanan']) ?></td>
                        <td>
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            switch ($booking['status']) {
                                case 'menunggu_konfirmasi':
                                    $statusClass = 'warning';
                                    $statusText = 'Menunggu';
                                    break;
                                case 'dikonfirmasi':
                                    $statusClass = 'info';
                                    $statusText = 'Dikonfirmasi';
                                    break;
                                case 'selesai':
                                    $statusClass = 'success';
                                    $statusText = 'Selesai';
                                    break;
                                case 'dibatalkan':
                                case 'batal':
                                    $statusClass = 'danger';
                                    $statusText = 'Dibatalkan';
                                    break;
                                default:
                                    $statusText = ucfirst($booking['status']);
                            }
                            ?>
                            <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">Tidak ada data booking</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signature -->
    <div class="signature">
        <table>
            <tr>
                <td style="width: 50%;"></td>
                <td class="right">
                    <p>Padang,<?= date('d-m-Y') ?></p>
                    <br><br><br>
                    <p><strong>Pimpinan</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>