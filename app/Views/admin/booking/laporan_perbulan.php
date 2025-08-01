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
                        <li class="breadcrumb-item active">Laporan PerBulan</li>
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
                            <form method="GET" action="<?= site_url('admin/booking/laporan-perbulan') ?>" class="row">
                                <div class="col-md-3">
                                    <label for="bulan">Bulan:</label>
                                    <select id="bulan" name="bulan" class="form-control">
                                        <?php foreach ($nama_bulan as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="tahun">Tahun:</label>
                                    <select id="tahun" name="tahun" class="form-control">
                                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="<?= site_url('admin/booking/export-perbulan-pdf?bulan=' . $bulan . '&tahun=' . $tahun) ?>"
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
                            <!-- Report Header sesuai gambar -->
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
                                <h3 class="text-center mb-3"><strong>Laporan Data Booking PerBulan</strong></h3>
                            </div>

                            <!-- Filter Info sesuai format gambar -->
                            <div class="mb-3">
                                <p class="mb-1">
                                    <strong>Bulan :</strong> <?= $nama_bulan[$bulan] ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>Tahun :</strong> <?= $tahun ?>
                                </p>
                            </div>

                            <!-- Report Table sesuai format gambar -->
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 11px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th class="text-center" style="width: 12%;">kode booking</th>
                                            <th class="text-center" style="width: 15%;">idpelanggan</th>
                                            <th class="text-center" style="width: 10%;">tanggal</th>
                                            <th class="text-center" style="width: 8%;">jam</th>
                                            <th class="text-center" style="width: 10%;">noplat</th>
                                            <th class="text-center" style="width: 12%;">jenis kendaraan</th>
                                            <th class="text-center" style="width: 12%;">merk kendaraan</th>
                                            <th class="text-center" style="width: 16%;">layanan</th>
                                            <th class="text-center" style="width: 10%;">status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($bookings)): ?>
                                            <?php foreach ($bookings as $index => $booking): ?>
                                                <tr>
                                                    <td class="text-center"><?= sprintf('%02d', $index + 1) ?></td>
                                                    <td class="text-center"><?= esc($booking['kode_booking']) ?></td>
                                                    <td><?= esc($booking['nama_pelanggan'] ?? $booking['idpelanggan']) ?></td>
                                                    <td class="text-center"><?= date('d/m/Y', strtotime($booking['tanggal'])) ?></td>
                                                    <td class="text-center"><?= date('H:i', strtotime($booking['jam'])) ?></td>
                                                    <td class="text-center"><?= esc($booking['noplat']) ?></td>
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
                                                <td colspan="10" class="text-center">Tidak ada data booking</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary and Signature sesuai format gambar -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <!-- Kosong untuk sesuai layout -->
                                </div>
                                <div class="col-md-6 text-right">
                                    <p>Padang,<?= date('d-m-Y') ?></p>
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
    /* Screen styles */
    .table th {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
<?= $this->endSection() ?>