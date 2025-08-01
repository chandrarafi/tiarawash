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
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/pelanggan') ?>">Pelanggan</a></li>
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
                            <form method="GET" action="<?= site_url('admin/pelanggan/laporan') ?>" class="row">
                                <div class="col-md-3">
                                    <label for="tanggal_cetak">Tanggal:</label>
                                    <input type="text" id="tanggal_cetak" name="tanggal_cetak"
                                        class="form-control" value="<?= $tanggal_cetak ?>"
                                        placeholder="DD/MM/YYYY">
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="<?= site_url('admin/pelanggan/export-pdf?tanggal_cetak=' . urlencode($tanggal_cetak)) ?>"
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
                                <h3 class="text-center mb-3"><strong>Laporan Data Pelanggan</strong></h3>
                            </div>

                            <!-- Filter Info sesuai format gambar -->
                            <div class="mb-3">
                                <p class="mb-1">
                                    <strong>Tanggal :</strong> <?= $tanggal_cetak ?>
                                </p>
                            </div>

                            <!-- Report Table sesuai format gambar -->
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 11px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-center" style="width: 8%;">NO</th>
                                            <th class="text-center" style="width: 20%;">Kode Pelanggan</th>
                                            <th class="text-center" style="width: 30%;">Nama Pelanggan</th>
                                            <th class="text-center" style="width: 32%;">Alamat</th>
                                            <th class="text-center" style="width: 10%;">No Hp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pelanggan)): ?>
                                            <?php foreach ($pelanggan as $index => $item): ?>
                                                <tr>
                                                    <td class="text-center"><?= sprintf('%02d', $index + 1) ?></td>
                                                    <td class="text-center"><?= esc($item['kode_pelanggan']) ?></td>
                                                    <td><?= esc($item['nama_pelanggan']) ?></td>
                                                    <td><?= esc($item['alamat'] ?? '-') ?></td>
                                                    <td class="text-center"><?= esc($item['no_hp'] ?? '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data pelanggan</td>
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