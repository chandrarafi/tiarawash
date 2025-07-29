<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <a href="<?= base_url('admin/booking/create'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Booking
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Booking</th>
                                    <th>Tanggal & Jam</th>
                                    <th>Pelanggan</th>
                                    <th>Kendaraan</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($booking as $item) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $item['kode_booking']; ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($item['tanggal'])); ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['jam'])); ?> WIB</small>
                                        </td>
                                        <td><?= $item['nama_pelanggan'] ?? 'Pelanggan Tidak Terdaftar'; ?></td>
                                        <td>
                                            <?= $item['no_plat']; ?><br>
                                            <span class="badge badge-<?= $item['jenis_kendaraan'] == 'motor' ? 'info' : ($item['jenis_kendaraan'] == 'mobil' ? 'primary' : 'secondary'); ?>">
                                                <?= ucfirst($item['jenis_kendaraan']); ?>
                                            </span>
                                            <?php if (!empty($item['merk_kendaraan'])) : ?>
                                                <small class="d-block"><?= $item['merk_kendaraan']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $item['nama_layanan'] ?? '-'; ?></td>
                                        <td>
                                            <?php
                                            switch ($item['status']) {
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
                                        <td>
                                            <a href="<?= base_url('admin/booking/show/' . $item['id']); ?>" class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($item['status'] == 'menunggu') : ?>
                                                <a href="<?= base_url('admin/booking/edit/' . $item['id']); ?>" class="btn btn-warning btn-sm mb-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('admin/booking/cancel/' . $item['id']); ?>" class="btn btn-danger btn-sm mb-1 btn-cancel" data-toggle="modal" data-target="#cancelModal" data-id="<?= $item['id']; ?>" data-kode="<?= $item['kode_booking']; ?>">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
        $('#dataTable').DataTable({
            "order": [
                [2, "desc"]
            ]
        });

        $('.btn-cancel').on('click', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');

            $('#bookingKode').text(kode);
            $('#confirmCancel').attr('href', '<?= base_url('admin/booking/cancel/'); ?>' + id);
        });
    });
</script>
<?= $this->endSection(); ?>