<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Cuci Pertanggal</title>
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

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
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
                        $data_img = file_get_contents($logoPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
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
        Laporan Transaksi Cuci<br>Pertanggal
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Tanggal :</strong> <?= $tanggal ?>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">kode pelanggan</th>
                <th style="width: 15%;">nama pelanggan</th>
                <th style="width: 15%;">nama karyawan</th>
                <th style="width: 12%;">jenis kendaraan</th>
                <th style="width: 10%;">no plat</th>
                <th style="width: 15%;">jenis jasa</th>
                <th style="width: 10%;">harga</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transaksi)): ?>
                <?php foreach ($transaksi as $index => $item): ?>
                    <tr>
                        <td><?= sprintf('%02d', $index + 1) ?></td>
                        <td><?= esc($item['kode_transaksi'] ?? '-') ?></td>
                        <td style="text-align: left; padding-left: 3px;"><?= esc($item['nama_pelanggan'] ?? '-') ?></td>
                        <td style="text-align: left; padding-left: 3px;"><?= esc($item['nama_karyawan'] ?? '-') ?></td>
                        <td><?= esc($item['jenis_kendaraan'] ?? '-') ?></td>
                        <td><?= esc($item['no_plat'] ?? '-') ?></td>
                        <td><?= esc($item['jenis_jasa'] ?? 'Cuci Mobil') ?></td>
                        <td style="text-align: right; padding-right: 3px;">Rp. <?= number_format($item['harga'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="7"><strong>Total</strong></td>
                    <td style="text-align: right; padding-right: 3px;">Rp. <?= number_format($total_harga, 0, ',', '.') ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="8">Tidak ada data transaksi</td>
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