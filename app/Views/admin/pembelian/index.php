<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pembelian Perlengkapan</h1>
        <p class="mb-0 text-secondary">Kelola data pembelian perlengkapan Tiara Wash</p>
    </div>
    <a href="<?= site_url('admin/pembelian/create') ?>" class="btn btn-primary d-flex align-items-center">
        <i class="bi bi-plus-lg me-2"></i> Tambah Pembelian
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Pembelian Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="pembelianTable">
                        <thead>
                            <tr>
                                <th>No Faktur</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total Harga</th>
                                <th>Petugas</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded by DataTables AJAX -->
                        </tbody>
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
                <input type="hidden" id="deleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS untuk tombol aksi -->
<style>
    /* Memperbaiki tampilan tombol aksi */
    #pembelianTable .btn {
        margin: 2px;
        min-width: 80px;
    }

    /* Memastikan kolom aksi cukup lebar */
    #pembelianTable th:last-child,
    #pembelianTable td:last-child {
        min-width: 180px;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 767.98px) {
        #pembelianTable .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            min-width: 60px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        let pembelianTable = $('#pembelianTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('admin/pembelian/getPembelian') ?>',
                type: 'GET'
            },
            columns: [{
                    data: 'no_faktur'
                },
                {
                    data: 'tanggal',
                    render: function(data, type, row) {
                        return formatDate(data);
                    }
                },
                {
                    data: 'supplier'
                },
                {
                    data: 'total_harga',
                    render: function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    data: 'user_name',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <a href="<?= site_url('admin/pembelian/detail/') ?>${row.no_faktur}" class="btn btn-sm btn-info me-2">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="<?= site_url('admin/pembelian/edit/') ?>${row.no_faktur}" class="btn btn-sm btn-warning me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-no-faktur="${row.no_faktur}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
            },
            columnDefs: [{
                targets: -1, // Kolom aksi (terakhir)
                className: 'text-nowrap' // Mencegah tombol terpotong
            }]
        });

        // Event klik tombol hapus
        $(document).on('click', '.btn-delete', function() {
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
                    pembelianTable.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(xhr, status, error) {
                    $('#deleteModal').modal('hide');
                    showErrorAlert('Gagal menghapus data pembelian');
                }
            });
        });

        // Format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        // Format rupiah
        function formatRupiah(angka) {
            if (!angka || isNaN(angka)) return 'Rp 0';
            return 'Rp ' + parseFloat(angka).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Fungsi untuk menampilkan alert error
        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        }
    });
</script>
<?= $this->endSection() ?>