<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <a href="<?= base_url('admin/antrian'); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Antrian</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nomor Antrian</th>
                                            <td><strong><?= $antrian['nomor_antrian']; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td><?= date('d/m/Y', strtotime($antrian['tanggal'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php
                                                switch ($antrian['status']) {
                                                    case 'menunggu':
                                                        echo '<span class="badge badge-warning">Menunggu</span>';
                                                        break;
                                                    case 'diproses':
                                                        echo '<span class="badge badge-info">Diproses</span>';
                                                        break;
                                                    case 'selesai':
                                                        echo '<span class="badge badge-success">Selesai</span>';
                                                        break;
                                                    case 'batal':
                                                        echo '<span class="badge badge-danger">Batal</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Unknown</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($antrian['jam_mulai'])) : ?>
                                            <tr>
                                                <th>Jam Mulai</th>
                                                <td><?= date('H:i', strtotime($antrian['jam_mulai'])); ?> WIB</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($antrian['jam_selesai'])) : ?>
                                            <tr>
                                                <th>Jam Selesai</th>
                                                <td><?= date('H:i', strtotime($antrian['jam_selesai'])); ?> WIB</td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>Karyawan</th>
                                            <td><?= $antrian['namakaryawan'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat pada</th>
                                            <td><?= date('d/m/Y H:i', strtotime($antrian['created_at'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <?php if (!empty($antrian['booking_id'])) : ?>
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Kode Booking</th>
                                                <td>
                                                    <a href="<?= base_url('admin/booking/show/' . $antrian['booking_id']); ?>">
                                                        <?= $antrian['kode_booking']; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal & Jam</th>
                                                <td>
                                                    <?php if (!empty($antrian['tanggal'])) : ?>
                                                        <?= date('d/m/Y', strtotime($antrian['tanggal'])); ?>
                                                        <?php if (!empty($antrian['jam'])) : ?>
                                                            <?= date('H:i', strtotime($antrian['jam'])); ?> WIB
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Catatan</th>
                                                <td><?= $antrian['catatan'] ?? '-'; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan & Kendaraan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nama Pelanggan</th>
                                            <td><?= $antrian['nama_pelanggan'] ?? 'Pelanggan Tidak Terdaftar'; ?></td>
                                        </tr>
                                        <?php if (!empty($antrian['no_plat'])) : ?>
                                            <tr>
                                                <th>Nomor Plat</th>
                                                <td><?= $antrian['no_plat']; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($antrian['jenis_kendaraan'])) : ?>
                                            <tr>
                                                <th>Jenis Kendaraan</th>
                                                <td>
                                                    <?php
                                                    switch ($antrian['jenis_kendaraan']) {
                                                        case 'motor':
                                                            echo '<span class="badge badge-info">Motor</span>';
                                                            break;
                                                        case 'mobil':
                                                            echo '<span class="badge badge-primary">Mobil</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="badge badge-secondary">Lainnya</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($antrian['merk_kendaraan'])) : ?>
                                            <tr>
                                                <th>Merk/Model</th>
                                                <td><?= $antrian['merk_kendaraan']; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Layanan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="20%">Nama Layanan</th>
                                            <td><?= $antrian['nama_layanan'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Harga</th>
                                            <td>Rp <?= number_format($antrian['harga'] ?? 0, 0, ',', '.'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Durasi Estimasi</th>
                                            <td><?= $antrian['durasi_menit'] ?? 0; ?> menit</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($antrian['status'] == 'menunggu') : ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Tugaskan Karyawan</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= base_url('admin/antrian/assignKaryawan/' . $antrian['id']); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <div class="form-group">
                                                <label for="karyawan_id">Pilih Karyawan</label>
                                                <select class="form-control" id="karyawan_id" name="karyawan_id" required>
                                                    <option value="">-- Pilih Karyawan --</option>
                                                    <?php foreach ($karyawan as $k) : ?>
                                                        <option value="<?= $k['idkaryawan']; ?>"><?= $k['idkaryawan'] . ' - ' . $k['namakaryawan']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Tugaskan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($antrian['status'] == 'diproses') : ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= base_url('admin/antrian/updateStatus/' . $antrian['id']); ?>" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="btn btn-success">Tandai Selesai</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>