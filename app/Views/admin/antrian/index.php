<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <div>
                        <form class="form-inline mr-auto">
                            <input type="date" class="form-control mr-2" id="tanggal" name="tanggal" value="<?= $tanggal; ?>">
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </form>
                    </div>
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

                    <div class="mb-3">
                        <a href="<?= base_url('admin/antrian/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Antrian
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Antrian</th>
                                    <th>Kode Booking</th>
                                    <th>Pelanggan</th>
                                    <th>Kendaraan</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Karyawan</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($antrian as $item) : ?>
                                    <tr class="<?= $item['status'] == 'menunggu' ? 'table-warning' : ($item['status'] == 'diproses' ? 'table-info' : ($item['status'] == 'selesai' ? 'table-success' : 'table-danger')); ?>">
                                        <td><?= $no++; ?></td>
                                        <td><strong><?= $item['nomor_antrian']; ?></strong></td>
                                        <td><?= $item['kode_booking'] ?? '-'; ?></td>
                                        <td><?= $item['nama_pelanggan'] ?? 'Pelanggan Tidak Terdaftar'; ?></td>
                                        <td>
                                            <?= $item['no_plat'] ?? '-'; ?>
                                            <?php if (!empty($item['jenis_kendaraan'])) : ?>
                                                <br>
                                                <span class="badge badge-<?= $item['jenis_kendaraan'] == 'motor' ? 'info' : ($item['jenis_kendaraan'] == 'mobil' ? 'primary' : 'secondary'); ?>">
                                                    <?= ucfirst($item['jenis_kendaraan']); ?>
                                                </span>
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
                                        <td><?= $item['namakaryawan'] ?? '-'; ?></td>
                                        <td>
                                            <?php if (!empty($item['jam_mulai'])) : ?>
                                                <small>Mulai: <?= date('H:i', strtotime($item['jam_mulai'])); ?></small><br>
                                            <?php endif; ?>
                                            <?php if (!empty($item['jam_selesai'])) : ?>
                                                <small>Selesai: <?= date('H:i', strtotime($item['jam_selesai'])); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/antrian/show/' . $item['id']); ?>" class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($item['status'] == 'menunggu') : ?>
                                                <button type="button" class="btn btn-primary btn-sm mb-1 btn-assign" data-toggle="modal" data-target="#assignModal" data-id="<?= $item['id']; ?>" data-nomor="<?= $item['nomor_antrian']; ?>">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php elseif ($item['status'] == 'diproses') : ?>
                                                <button type="button" class="btn btn-success btn-sm mb-1 btn-complete" data-toggle="modal" data-target="#completeModal" data-id="<?= $item['id']; ?>" data-nomor="<?= $item['nomor_antrian']; ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
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

<!-- Assign Karyawan Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Tugaskan Karyawan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assignForm" action="" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p>Pilih karyawan untuk mengerjakan antrian nomor <span id="antrianNomor" class="font-weight-bold"></span>:</p>
                    <div class="form-group">
                        <label for="karyawan_id">Karyawan</label>
                        <select class="form-control" id="karyawan_id" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan ?? [] as $k) : ?>
                                <option value="<?= $k['idkaryawan']; ?>"><?= $k['idkaryawan'] . ' - ' . $k['namakaryawan']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tugaskan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">Konfirmasi Selesai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="completeForm" action="" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin antrian nomor <span id="antrianNomorComplete" class="font-weight-bold"></span> telah selesai?</p>
                    <input type="hidden" name="status" value="selesai">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [0, "asc"]
            ]
        });

        $('.btn-assign').on('click', function() {
            const id = $(this).data('id');
            const nomor = $(this).data('nomor');

            $('#antrianNomor').text(nomor);
            $('#assignForm').attr('action', '<?= base_url('admin/antrian/assignKaryawan/'); ?>' + id);
        });

        $('.btn-complete').on('click', function() {
            const id = $(this).data('id');
            const nomor = $(this).data('nomor');

            $('#antrianNomorComplete').text(nomor);
            $('#completeForm').attr('action', '<?= base_url('admin/antrian/updateStatus/'); ?>' + id);
        });
    });
</script>
<?= $this->endSection(); ?>