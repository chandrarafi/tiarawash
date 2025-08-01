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
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/keuangan') ?>">Keuangan</a></li>
                        <li class="breadcrumb-item active">Laporan PerTahun</li>
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
                            <form method="GET" action="<?= site_url('admin/keuangan/laporan-pertahun') ?>" class="row">
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
                                    <a href="<?= site_url('admin/keuangan/export-pertahun-pdf?tahun=' . $tahun) ?>"
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
                                <h3 class="text-center mb-3"><strong>Laporan Uang Masuk dan<br>Keluar PerTahun</strong></h3>
                            </div>

                            <!-- Filter Info sesuai format gambar -->
                            <div class="mb-3">
                                <p class="mb-1">
                                    <strong>Tahun :</strong> <?= $tahun ?>
                                </p>
                            </div>

                            <!-- Report Table sesuai format gambar -->
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 11px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-center" style="width: 15%;">NO</th>
                                            <th class="text-center" style="width: 25%;">Bulan</th>
                                            <th class="text-center" style="width: 30%;">Uang Masuk</th>
                                            <th class="text-center" style="width: 30%;">uang keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($laporan_detail)): ?>
                                            <?php foreach ($laporan_detail as $index => $item): ?>
                                                <tr>
                                                    <td class="text-center"><?= sprintf('%02d', $index + 1) ?></td>
                                                    <td class="text-center"><?= $item['nama_bulan'] ?></td>
                                                    <td class="text-center">Rp. <?= number_format($item['uang_masuk'], 0, ',', '.') ?></td>
                                                    <td class="text-center">Rp. <?= number_format($item['uang_keluar'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <!-- Total Row -->
                                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                                <td class="text-center" colspan="2"><strong>Total</strong></td>
                                                <td class="text-center">Rp. <?= number_format($total_masuk, 0, ',', '.') ?></td>
                                                <td class="text-center">Rp. <?= number_format($total_keluar, 0, ',', '.') ?></td>
                                            </tr>
                                            <!-- Status Row -->
                                            <tr style="background-color: #e9ecef; font-weight: bold;">
                                                <td class="text-center" colspan="2"><strong>Status</strong></td>
                                                <td class="text-center" colspan="2">
                                                    <?php
                                                    $selisih = $total_masuk - $total_keluar;
                                                    if ($selisih > 0): ?>
                                                        <span class="text-success">Keuntungan: Rp. <?= number_format($selisih, 0, ',', '.') ?></span>
                                                    <?php elseif ($selisih < 0): ?>
                                                        <span class="text-danger">Kerugian: Rp. <?= number_format(abs($selisih), 0, ',', '.') ?></span>
                                                    <?php else: ?>
                                                        <span class="text-info">Break Even</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data keuangan</td>
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
</style>
<?= $this->endSection() ?>