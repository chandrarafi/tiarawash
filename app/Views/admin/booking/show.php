<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <div>
                        <?php if ($booking['status'] == 'menunggu') : ?>
                            <a href="<?= base_url('admin/booking/edit/' . $booking['id']); ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" class="btn btn-danger btn-sm btn-cancel" data-toggle="modal" data-target="#cancelModal" data-id="<?= $booking['id']; ?>" data-kode="<?= $booking['kode_booking']; ?>">
                                <i class="fas fa-times"></i> Batalkan
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/booking'); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Kode Booking</th>
                                            <td><strong><?= $booking['kode_booking']; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td><?= date('d/m/Y', strtotime($booking['tanggal'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jam</th>
                                            <td><?= date('H:i', strtotime($booking['jam'])); ?> WIB</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php
                                                switch ($booking['status']) {
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
                                        <tr>
                                            <th>Dibuat pada</th>
                                            <td><?= date('d/m/Y H:i', strtotime($booking['created_at'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Catatan</th>
                                            <td><?= $booking['catatan'] ?: '-'; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nama Pelanggan</th>
                                            <td><?= $booking['nama_pelanggan'] ?? 'Pelanggan Tidak Terdaftar'; ?></td>
                                        </tr>
                                        <?php if (!empty($booking['pelanggan_id'])) : ?>
                                            <tr>
                                                <th>Kode Pelanggan</th>
                                                <td><?= $booking['pelanggan_id']; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Informasi Kendaraan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nomor Plat</th>
                                            <td><?= $booking['no_plat']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kendaraan</th>
                                            <td>
                                                <?php
                                                switch ($booking['jenis_kendaraan']) {
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
                                        <?php if (!empty($booking['merk_kendaraan'])) : ?>
                                            <tr>
                                                <th>Merk/Model</th>
                                                <td><?= $booking['merk_kendaraan']; ?></td>
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
                                            <td><?= $booking['nama_layanan'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Harga</th>
                                            <td>Rp <?= number_format($booking['harga'] ?? 0, 0, ',', '.'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Durasi Estimasi</th>
                                            <td><?= $booking['durasi_menit'] ?? 0; ?> menit</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($booking['status'] == 'menunggu') : ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Booking ini belum masuk antrian. Untuk memasukkan ke antrian, klik tombol di bawah ini.
                                </div>
                                <form action="<?= base_url('admin/antrian/store'); ?>" method="post">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="booking_id" value="<?= $booking['id']; ?>">
                                    <input type="hidden" name="tanggal" value="<?= $booking['tanggal']; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Masukkan ke Antrian
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan booking dengan kode <span id="bookingKode" class="font-weight-bold"></span>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="#" id="confirmCancel" class="btn btn-danger">Ya, Batalkan</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('.btn-cancel').on('click', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');

            $('#bookingKode').text(kode);
            $('#confirmCancel').attr('href', '<?= base_url('admin/booking/cancel/'); ?>' + id);
        });
    });
</script>
<?= $this->endSection(); ?>