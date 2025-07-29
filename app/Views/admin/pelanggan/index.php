<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pelanggan</h1>
        <p class="mb-0 text-secondary">Kelola data pelanggan Tiara Wash</p>
    </div>
    <a href="<?= site_url('admin/pelanggan/create') ?>" class="btn btn-primary d-flex align-items-center">
        <i class="bi bi-person-plus me-2"></i> Tambah Pelanggan
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Pelanggan Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="pelangganTable">
                        <thead>
                            <tr>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
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
                <p>Apakah Anda yakin ingin menghapus pelanggan ini?</p>
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
    #pelangganTable .btn {
        margin: 2px;
        min-width: 80px;
    }

    /* Memastikan kolom aksi cukup lebar */
    #pelangganTable th:last-child,
    #pelangganTable td:last-child {
        min-width: 180px;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 767.98px) {
        #pelangganTable .btn {
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
        let pelangganTable = $('#pelangganTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('admin/pelanggan/getPelanggan') ?>',
                type: 'GET'
            },
            columns: [{
                    data: 'kode_pelanggan'
                },
                {
                    data: 'nama_pelanggan'
                },
                {
                    data: 'email'
                },
                {
                    data: 'no_hp'
                },
                {
                    data: 'alamat'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <a href="<?= site_url('admin/pelanggan/edit/') ?>${row.kode_pelanggan}" class="btn btn-sm btn-info me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${row.kode_pelanggan}">
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
            const id = $(this).data('id');
            $('#deleteId').val(id);
            $('#deleteModal').modal('show');
        });

        // Event klik tombol konfirmasi hapus
        $('#btnConfirmDelete').on('click', function() {
            const id = $('#deleteId').val();

            $.ajax({
                url: '<?= site_url('admin/pelanggan/delete/') ?>' + id,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    pelangganTable.ajax.reload();

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
                    showErrorAlert('Gagal menghapus data pelanggan');
                }
            });
        });

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