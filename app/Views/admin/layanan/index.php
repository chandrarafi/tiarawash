<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <a href="<?= base_url('admin/layanan/create'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Layanan
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Foto</th>
                                    <th width="15%">Kode Layanan</th>
                                    <th width="20%">Nama Layanan</th>
                                    <th width="8%">Jenis Kendaraan</th>
                                    <th width="12%">Harga</th>
                                    <th width="10%">Durasi</th>
                                    <th width="8%">Status</th>
                                    <th width="200%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($layanan)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle mb-2"></i><br>
                                            Belum ada data layanan
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($layanan as $item) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td class="text-center">
                                                <?php if (!empty($item['foto'])) : ?>
                                                    <img src="<?= base_url('uploads/layanan/' . $item['foto']); ?>"
                                                        alt="<?= $item['nama_layanan']; ?>"
                                                        class="img-thumbnail"
                                                        style="width: 60px; height: 60px; object-fit: cover;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <?php else : ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                                        style="width: 60px; height: 60px; border-radius: 4px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="text-bold"><?= $item['kode_layanan'] ?? '-'; ?></span></td>
                                            <td class="font-weight-bold"><?= $item['nama_layanan'] ?? '-'; ?></td>
                                            <td>
                                                <?php
                                                $jenisKendaraan = $item['jenis_kendaraan'] ?? '';
                                                switch ($jenisKendaraan) {
                                                    case 'motor':
                                                        echo '<span class="badge bg-info"><i class="fas fa-motorcycle me-1"></i>Motor</span>';
                                                        break;
                                                    case 'mobil':
                                                        echo '<span class="badge bg-primary"><i class="fas fa-car me-1"></i>Mobil</span>';
                                                        break;
                                                    case 'lainnya':
                                                        echo '<span class="badge bg-secondary"><i class="fas fa-truck me-1"></i>Lainnya</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-light text-muted">-</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($item['harga']) && $item['harga'] > 0): ?>
                                                    <span class="text-success font-weight-bold">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($item['durasi_menit']) && $item['durasi_menit'] > 0): ?>
                                                    <span class="badge bg-info"><?= $item['durasi_menit']; ?> menit</span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item['status'] == 'aktif') : ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else : ?>
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/layanan/edit/' . $item['kode_layanan']); ?>" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                    data-toggle="modal" data-target="#deleteModal"
                                                    data-kode="<?= $item['kode_layanan']; ?>"
                                                    data-name="<?= $item['nama_layanan']; ?>" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus layanan <span id="layananName" class="font-weight-bold"></span>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        $('.btn-delete').on('click', function() {
            const kode = $(this).data('kode');
            const name = $(this).data('name');

            $('#layananName').text(name);
            $('#confirmDelete').attr('href', '<?= base_url('admin/layanan/delete/'); ?>' + kode);
        });
    });
</script>
<?= $this->endSection(); ?>