<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Uang Masuk dan Keluar PerTahun</title>
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
            font-size: 11px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .data-table td {
            vertical-align: middle;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .status-row {
            background-color: #e9ecef;
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
        Laporan Uang Masuk dan<br>Keluar PerTahun
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Tahun :</strong> <?= $tahun ?>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">NO</th>
                <th style="width: 25%;">Bulan</th>
                <th style="width: 30%;">Uang Masuk</th>
                <th style="width: 30%;">uang keluar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporan_detail)): ?>
                <?php foreach ($laporan_detail as $index => $item): ?>
                    <tr>
                        <td><?= sprintf('%02d', $index + 1) ?></td>
                        <td><?= $item['nama_bulan'] ?></td>
                        <td>Rp. <?= number_format($item['uang_masuk'], 0, ',', '.') ?></td>
                        <td>Rp. <?= number_format($item['uang_keluar'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="2"><strong>Total</strong></td>
                    <td>Rp. <?= number_format($total_masuk, 0, ',', '.') ?></td>
                    <td>Rp. <?= number_format($total_keluar, 0, ',', '.') ?></td>
                </tr>
                <!-- Status Row -->
                <tr class="status-row">
                    <td colspan="2"><strong>Status</strong></td>
                    <td colspan="2">
                        <?php
                        $selisih = $total_masuk - $total_keluar;
                        if ($selisih > 0):
                            echo 'Keuntungan: Rp. ' . number_format($selisih, 0, ',', '.');
                        elseif ($selisih < 0):
                            echo 'Kerugian: Rp. ' . number_format(abs($selisih), 0, ',', '.');
                        else:
                            echo 'Break Even';
                        endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada data keuangan</td>
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