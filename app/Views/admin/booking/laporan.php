<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/booking') ?>">Booking</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Filter Form -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filter Laporan</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?= site_url('admin/booking/laporan') ?>" class="row">
                                <div class="col-md-3">
                                    <label for="tanggal">Tanggal Spesifik:</label>
                                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= esc($tanggal_filter) ?>">
                                </div>
                                <!-- <div class="col-md-2">
                                    <label for="tahun">Tahun:</label>
                                    <select id="tahun" name="tahun" class="form-control">
                                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div> -->
                                <div class="col-md-4">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <!-- <button type="button" onclick="printReport()" class="btn btn-success">
                                        <i class="fas fa-print"></i> Cetak Browser
                                    </button> -->
                                    <a href="<?= site_url('admin/booking/export-pdf?' . http_build_query(['tahun' => $tahun, 'tanggal' => $tanggal_filter])) ?>"
                                        class="btn btn-danger" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card" id="printable-content">
                        <div class="card-body">
                            <!-- Report Header -->
                            <div class="text-center mb-4" id="report-header">
                                <div class="row align-items-center">
                                    <div class="col-2">
                                        <img src="<?= base_url('images/logo.png') ?>" alt="Logo" style="max-width: 100px; height: auto;" class="img-fluid">
                                    </div>
                                    <div class="col-8">
                                        <h2 class="mb-1"><strong>Tiara Wash</strong></h2>
                                        <p class="mb-1">Alamat : Jl Rawang Jundul, Padang Utara, Kota Padang</p>
                                        <p class="mb-1">Sumatera Barat 25127</p>
                                        <p class="mb-0">Telp/Fax: 0813-6359-6965</p>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <hr style="border-top: 2px solid #000; margin: 20px 0;">
                                <h3 class="text-center mb-3"><strong>Laporan Booking Pertanggal</strong></h3>
                            </div>

                            <!-- Filter Info -->
                            <div class="mb-3">
                                <p class="mb-1">
                                    <strong>Tanggal:</strong>
                                    <?php if ($tanggal_filter): ?>
                                        <?= date('d/m/Y', strtotime($tanggal_filter)) ?>
                                    <?php else: ?>
                                        Tahun <?= $tahun ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <!-- Report Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th class="text-center" style="width: 12%;">Kode Booking</th>
                                            <th class="text-center" style="width: 15%;">ID Pelanggan</th>
                                            <th class="text-center" style="width: 8%;">Jam</th>
                                            <th class="text-center" style="width: 10%;">No Plat</th>
                                            <th class="text-center" style="width: 12%;">Jenis Kendaraan</th>
                                            <th class="text-center" style="width: 12%;">Merk Kendaraan</th>
                                            <th class="text-center" style="width: 16%;">Layanan</th>
                                            <th class="text-center" style="width: 10%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($bookings)): ?>
                                            <?php foreach ($bookings as $index => $booking): ?>
                                                <tr>
                                                    <td class="text-center"><?= $index + 1 ?></td>
                                                    <td class="text-center"><?= esc($booking['kode_booking']) ?></td>
                                                    <td><?= esc($booking['nama_pelanggan'] ?? $booking['pelanggan_id']) ?></td>
                                                    <td class="text-center"><?= date('H:i', strtotime($booking['jam'])) ?></td>
                                                    <td class="text-center"><?= esc($booking['no_plat']) ?></td>
                                                    <td class="text-center"><?= ucfirst(esc($booking['jenis_kendaraan'])) ?></td>
                                                    <td class="text-center"><?= esc($booking['merk_kendaraan'] ?? '-') ?></td>
                                                    <td><?= esc($booking['layanan']) ?></td>
                                                    <td class="text-center">
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
                                                <td colspan="9" class="text-center">Tidak ada data booking</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary and Signature -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Total Booking: <?= $total_booking ?></strong></p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p>Padang, <?= date('d-m-Y') ?></p>
                                    <br><br><br>
                                    <p><strong>Pimpinan</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    @media print {
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 15px !important;
        }

        /* Hide elements not needed in print */
        .main-sidebar,
        .main-header,
        .content-header,
        .card:not(#printable-content),
        .btn,
        .breadcrumb,
        .no-print {
            display: none !important;
        }

        /* Adjust table for better printing */
        .table {
            font-size: 10px !important;
        }

        .table th,
        .table td {
            padding: 4px !important;
            border: 1px solid #000 !important;
        }

        /* Ensure proper page layout */
        body {
            font-family: Arial, sans-serif !important;
            color: #000 !important;
            background: white !important;
        }

        /* Header styling for print */
        #report-header h2 {
            font-size: 18px !important;
            margin-bottom: 5px !important;
        }

        #report-header p {
            font-size: 12px !important;
            margin-bottom: 2px !important;
        }

        #report-header h3 {
            font-size: 16px !important;
            margin: 10px 0 !important;
        }
    }

    /* Screen styles */
    .table th {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75em;
    }
</style>

<script>
    function printReport() {
        window.print();
    }

    // Auto-clear tanggal when tahun changed
    document.getElementById('tahun').addEventListener('change', function() {
        document.getElementById('tanggal').value = '';
    });

    // Auto-clear tahun when tanggal changed
    document.getElementById('tanggal').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('tahun').selectedIndex = 0;
        }
    });
</script>
<?= $this->endSection() ?>