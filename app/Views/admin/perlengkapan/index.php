<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Manajemen Perlengkapan</h1>
        <p class="mb-0 text-secondary">Kelola data perlengkapan pencucian Tiara Wash</p>
    </div>
    <a href="<?= site_url('admin/perlengkapan/create') ?>" class="btn btn-primary d-flex align-items-center">
        <i class="bi bi-plus-lg me-2"></i> Tambah Perlengkapan
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="col-form-label">Filter Kategori:</label>
                    </div>
                    <div class="col-auto">
                        <select class="form-select" id="filterKategori">
                            <option value="semua">Semua Kategori</option>
                            <option value="alat">Alat</option>
                            <option value="bahan">Bahan</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Perlengkapan Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="perlengkapanTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Perlengkapan</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
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
                <p>Apakah Anda yakin ingin menghapus perlengkapan ini?</p>
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
    #perlengkapanTable .btn {
        margin: 2px;
        min-width: 80px;
    }

    /* Memastikan kolom aksi cukup lebar */
    #perlengkapanTable th:last-child,
    #perlengkapanTable td:last-child {
        min-width: 180px;
    }

    /* Badge kategori */
    .badge-alat {
        background-color: #4e73df;
        color: white;
    }

    .badge-bahan {
        background-color: #1cc88a;
        color: white;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 767.98px) {
        #perlengkapanTable .btn {
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
        let perlengkapanTable = $('#perlengkapanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('admin/perlengkapan/getPerlengkapan') ?>',
                type: 'GET',
                data: function(d) {
                    d.kategori = $('#filterKategori').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'kategori',
                    render: function(data, type, row) {
                        let badge = '';
                        if (data === 'alat') {
                            badge = '<span class="badge badge-alat">Alat</span>';
                        } else if (data === 'bahan') {
                            badge = '<span class="badge badge-bahan">Bahan</span>';
                        }
                        return badge;
                    }
                },
                {
                    data: 'stok',
                    render: function(data, type, row) {
                        return data + ' unit';
                    }
                },
                {
                    data: 'harga',
                    render: function(data, type, row) {
                        return 'Rp ' + formatRupiah(data);
                    }
                },
                {
                    data: 'deskripsi',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <a href="<?= site_url('admin/perlengkapan/edit/') ?>${row.id}" class="btn btn-sm btn-info me-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${row.id}">
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

        // Filter berdasarkan kategori
        $('#filterKategori').on('change', function() {
            perlengkapanTable.ajax.reload();
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
                url: '<?= site_url('admin/perlengkapan/delete/') ?>' + id,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    perlengkapanTable.ajax.reload();

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
                    showErrorAlert('Gagal menghapus data perlengkapan');
                }
            });
        });

        // Fungsi untuk format rupiah
        function formatRupiah(angka) {
            return parseFloat(angka).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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