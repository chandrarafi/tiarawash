<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Manajemen Karyawan</h1>
        <p class="mb-0 text-secondary">Kelola data karyawan Tiara Wash</p>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center" id="btnAddKaryawan">
        <i class="bi bi-person-plus me-2"></i> Tambah Karyawan
    </button>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Karyawan Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="karyawanTable">
                        <thead>
                            <tr>
                                <th>ID Karyawan</th>
                                <th>Nama Karyawan</th>
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

<!-- Modal Tambah/Edit Karyawan -->
<div class="modal fade" id="karyawanModal" tabindex="-1" aria-labelledby="karyawanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="karyawanModalLabel">Tambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="karyawanForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="idkaryawan" class="form-label">ID Karyawan</label>
                        <input type="text" class="form-control" id="idkaryawan" name="idkaryawan">
                        <div class="invalid-feedback" id="idkaryawan-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="namakaryawan" class="form-label">Nama Karyawan</label>
                        <input type="text" class="form-control" id="namakaryawan" name="namakaryawan">
                        <div class="invalid-feedback" id="namakaryawan-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nohp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="nohp" name="nohp">
                        <div class="invalid-feedback" id="nohp-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        <div class="invalid-feedback" id="alamat-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </form>
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
                <p>Apakah Anda yakin ingin menghapus karyawan ini?</p>
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
    #karyawanTable .btn {
        margin: 2px;
        min-width: 80px;
    }

    /* Memastikan kolom aksi cukup lebar */
    #karyawanTable th:last-child,
    #karyawanTable td:last-child {
        min-width: 180px;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 767.98px) {
        #karyawanTable .btn {
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
        let karyawanTable = $('#karyawanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('admin/karyawan/getKaryawan') ?>',
                type: 'GET'
            },
            columns: [{
                    data: 'idkaryawan'
                },
                {
                    data: 'namakaryawan'
                },
                {
                    data: 'nohp'
                },
                {
                    data: 'alamat'
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
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

        // Variabel untuk menyimpan mode form (tambah/edit)
        let formMode = 'add';
        let selectedId = '';

        // Event klik tombol tambah karyawan
        $('#btnAddKaryawan').on('click', function() {
            formMode = 'add';
            resetForm();
            $('#karyawanModalLabel').text('Tambah Karyawan');
            getNewId();
            $('#karyawanModal').modal('show');
        });

        // Fungsi untuk mendapatkan ID karyawan baru
        function getNewId() {
            $.ajax({
                url: '<?= site_url('admin/karyawan/getNewId') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#idkaryawan').val(response.id);
                    }
                },
                error: function(xhr, status, error) {
                    showErrorAlert('Gagal mendapatkan ID baru');
                }
            });
        }

        // Event klik tombol edit
        $(document).on('click', '.btn-edit', function() {
            formMode = 'edit';
            resetForm();
            selectedId = $(this).data('id');

            $('#karyawanModalLabel').text('Edit Karyawan');

            // Ambil data karyawan berdasarkan ID
            $.ajax({
                url: '<?= site_url('admin/karyawan/getById/') ?>' + selectedId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let karyawan = response.data;
                        $('#idkaryawan').val(karyawan.idkaryawan);
                        $('#namakaryawan').val(karyawan.namakaryawan);
                        $('#nohp').val(karyawan.nohp);
                        $('#alamat').val(karyawan.alamat);

                        $('#karyawanModal').modal('show');
                    } else {
                        showErrorAlert('Data karyawan tidak ditemukan');
                    }
                },
                error: function(xhr, status, error) {
                    showErrorAlert('Gagal mengambil data karyawan');
                }
            });
        });

        // Event submit form
        $('#karyawanForm').on('submit', function(e) {
            e.preventDefault();

            // Reset error messages
            resetErrors();

            // Ambil data form
            const formData = {
                idkaryawan: $('#idkaryawan').val(),
                namakaryawan: $('#namakaryawan').val(),
                nohp: $('#nohp').val(),
                alamat: $('#alamat').val()
            };

            // URL dan method berdasarkan mode form
            let url = '<?= site_url('admin/karyawan/store') ?>';
            let method = 'POST';

            if (formMode === 'edit') {
                url = '<?= site_url('admin/karyawan/update/') ?>' + selectedId;
                method = 'PUT';
            }

            // Kirim data ke server
            $.ajax({
                url: url,
                type: method,
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#karyawanModal').modal('hide');
                        karyawanTable.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 400) {
                        // Validasi error
                        const errors = xhr.responseJSON.messages;
                        displayErrors(errors);
                    } else {
                        // Server error
                        showErrorAlert('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });

        // Event klik tombol hapus
        $(document).on('click', '.btn-delete', function() {
            selectedId = $(this).data('id');
            $('#deleteModal').modal('show');
        });

        // Event klik tombol konfirmasi hapus
        $('#btnConfirmDelete').on('click', function() {
            $.ajax({
                url: '<?= site_url('admin/karyawan/delete/') ?>' + selectedId,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    karyawanTable.ajax.reload();

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
                    showErrorAlert('Gagal menghapus data karyawan');
                }
            });
        });

        // Fungsi untuk reset form
        function resetForm() {
            $('#karyawanForm')[0].reset();
            resetErrors();
        }

        // Fungsi untuk reset pesan error
        function resetErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        // Fungsi untuk menampilkan pesan error validasi
        function displayErrors(errors) {
            $.each(errors, function(field, message) {
                $('#' + field).addClass('is-invalid');
                $('#' + field + '-error').text(message);
            });
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