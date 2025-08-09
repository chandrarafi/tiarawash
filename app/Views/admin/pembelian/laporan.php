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
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/pembelian') ?>">Pembelian</a></li>
                        <li class="breadcrumb-item active">Laporan Pembelian Alat</li>
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
                            <form method="GET" action="<?= site_url('admin/pembelian/laporan') ?>" class="row" id="formFilter">
                                <div class="col-md-3">
                                    <label for="bulan">Bulan:</label>
                                    <select id="bulan" name="bulan" class="form-control">
                                        <option value="">Pilih Bulan</option>
                                        <option value="01" <?= (isset($bulan) && $bulan == '01') ? 'selected' : '' ?>>Januari</option>
                                        <option value="02" <?= (isset($bulan) && $bulan == '02') ? 'selected' : '' ?>>Februari</option>
                                        <option value="03" <?= (isset($bulan) && $bulan == '03') ? 'selected' : '' ?>>Maret</option>
                                        <option value="04" <?= (isset($bulan) && $bulan == '04') ? 'selected' : '' ?>>April</option>
                                        <option value="05" <?= (isset($bulan) && $bulan == '05') ? 'selected' : '' ?>>Mei</option>
                                        <option value="06" <?= (isset($bulan) && $bulan == '06') ? 'selected' : '' ?>>Juni</option>
                                        <option value="07" <?= (isset($bulan) && $bulan == '07') ? 'selected' : '' ?>>Juli</option>
                                        <option value="08" <?= (isset($bulan) && $bulan == '08') ? 'selected' : '' ?>>Agustus</option>
                                        <option value="09" <?= (isset($bulan) && $bulan == '09') ? 'selected' : '' ?>>September</option>
                                        <option value="10" <?= (isset($bulan) && $bulan == '10') ? 'selected' : '' ?>>Oktober</option>
                                        <option value="11" <?= (isset($bulan) && $bulan == '11') ? 'selected' : '' ?>>November</option>
                                        <option value="12" <?= (isset($bulan) && $bulan == '12') ? 'selected' : '' ?>>Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="tahun">Tahun:</label>
                                    <select id="tahun" name="tahun" class="form-control">
                                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                            <option value="<?= $y ?>" <?= (isset($tahun) && $tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="<?= site_url('admin/pembelian/export-pdf?bulan=' . urlencode($bulan ?? '') . '&tahun=' . urlencode($tahun ?? '')) ?>"
                                        class="btn btn-danger" target="_blank" id="btnExportPDF">
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
                                        <p class="mb-1">Alamat : Jl. Rawang Jundul, Padang Utara, Kota Padang</p>
                                        <p class="mb-1">Sumatera Barat 25127</p>
                                        <p class="mb-0">Telp/Fax: 0813-6359-6965</p>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <hr style="border-top: 2px solid #000; margin: 20px 0;">
                                <h3 class="text-center mb-3"><strong>Laporan Pembelian Alat</strong></h3>
                            </div>

                            <!-- Filter Info sesuai format gambar -->
                            <div class="mb-3">
                                <p class="mb-1">
                                    <strong>Bulan :</strong>
                                    <?php
                                    $nama_bulan = [
                                        '01' => 'Januari ',
                                        '02' => 'Februari ',
                                        '03' => 'Maret ',
                                        '04' => 'April ',
                                        '05' => 'Mei ',
                                        '06' => 'Juni ',
                                        '07' => 'Juli ',
                                        '08' => 'Agustus ',
                                        '09' => 'September ',
                                        '10' => 'Oktober ',
                                        '11' => 'November ',
                                        '12' => 'Desember '
                                    ];
                                    echo (isset($bulan) && $bulan && isset($nama_bulan[$bulan])) ? $nama_bulan[$bulan] . (isset($tahun) ? $tahun : date('Y')) : 'Semua Data';
                                    ?>
                                </p>
                            </div>

                            <!-- Report Table sesuai format gambar -->
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 11px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">NO</th>
                                            <th class="text-center" style="width: 15%;">No Faktur</th>
                                            <th class="text-center" style="width: 12%;">Tanggal</th>
                                            <th class="text-center" style="width: 25%;">Supplier</th>
                                            <th class="text-center" style="width: 15%;">Total</th>
                                            <th class="text-center" style="width: 28%;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pembelian)): ?>
                                            <?php foreach ($pembelian as $index => $item): ?>
                                                <tr>
                                                    <td class="text-center"><?= sprintf('%02d', $index + 1) ?></td>
                                                    <td class="text-center"><?= esc($item['no_faktur']) ?></td>
                                                    <td class="text-center"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                                    <td><?= esc($item['supplier']) ?></td>
                                                    <td class="text-center">Rp. <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                                    <td><?= esc($item['keterangan'] ?? '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <!-- Total Row -->
                                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                                <td class="text-center" colspan="4"><strong>Total</strong></td>
                                                <td class="text-center">Rp. <?= number_format($total_harga, 0, ',', '.') ?></td>
                                                <td></td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data pembelian</td>
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