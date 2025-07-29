<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Detail Pembelian Perlengkapan</h1>
        <p class="mb-0 text-secondary">Informasi lengkap pembelian perlengkapan</p>
    </div>
    <a href="<?= site_url('admin/pembelian') ?>" class="btn btn-secondary d-flex align-items-center">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pembelian</h6>
                <div>
                    <a href="<?= site_url('admin/pembelian/edit/' . $pembelian['no_faktur']) ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-no-faktur="<?= $pembelian['no_faktur'] ?>">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>No. Faktur</strong></td>
                                <td>: <?= $pembelian['no_faktur'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: <?= date('d F Y', strtotime($pembelian['tanggal'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier</strong></td>
                                <td>: <?= $pembelian['supplier'] ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Total Harga</strong></td>
                                <td>: Rp <?= number_format($pembelian['total_harga'], 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td><strong>Petugas</strong></td>
                                <td>: <?= $pembelian['user_name'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <?= $pembelian['keterangan'] ? nl2br($pembelian['keterangan']) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Detail Item Perlengkapan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="detailTable">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Perlengkapan</th>
                                <th>Kategori</th>
                                <th width="100">Jumlah</th>
                                <th width="150">Harga Satuan</th>
                                <th width="150">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($details)) : ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data detail pembelian</td>
                                </tr>
                            <?php else : ?>
                                <?php $no = 1;
                                foreach ($details as $detail) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $detail['nama_perlengkapan'] ?></td>
                                        <td><?= $detail['kategori'] ?></td>
                                        <td><?= $detail['jumlah'] ?></td>
                                        <td>Rp <?= number_format($detail['harga_satuan'], 2, ',', '.') ?></td>
                                        <td>Rp <?= number_format($detail['subtotal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Total</th>
                                <th>Rp <?= number_format($pembelian['total_harga'], 2, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pembelian ini?</p>
                <p class="text-danger">Perhatian: Stok perlengkapan akan dikurangi sesuai dengan jumlah pembelian.</p>
                <input type="hidden" id="deleteId" value="<?= $pembelian['no_faktur'] ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Event klik tombol hapus
        $('.btn-delete').on('click', function() {
            const noFaktur = $(this).data('no-faktur');
            $('#deleteId').val(noFaktur);
            $('#deleteModal').modal('show');
        });

        // Event klik tombol konfirmasi hapus
        $('#btnConfirmDelete').on('click', function() {
            const noFaktur = $('#deleteId').val();

            $.ajax({
                url: '<?= site_url('admin/pembelian/delete/') ?>' + noFaktur,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    $('#deleteModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '<?= site_url('admin/pembelian') ?>';
                    });
                },
                error: function(xhr, status, error) {
                    $('#deleteModal').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal menghapus data pembelian'
                    });
                }
            });
        });

        // Inisialisasi DataTable
        $('#detailTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
            }
        });
    });
</script>
<?= $this->endSection() ?>