<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tambah Pelanggan</h1>
            <p class="mb-0 text-secondary">Tambah data pelanggan baru</p>
        </div>
        <a href="<?= site_url('admin/pelanggan') ?>" class="btn btn-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="form-pelanggan" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_pelanggan" class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                                    <div class="invalid-feedback" id="nama_pelanggan-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                                    <div class="invalid-feedback" id="no_hp-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="4" required></textarea>
                                    <div class="invalid-feedback" id="alamat-error"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">Informasi Akun</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="create_account" name="create_account" value="1">
                                                <label class="form-check-label" for="create_account">Buat Akun untuk Pelanggan</label>
                                            </div>
                                            <small class="text-muted">Aktifkan untuk membuat akun login bagi pelanggan</small>
                                        </div>

                                        <div id="account-fields" style="display: none;">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username">
                                                <div class="invalid-feedback" id="username-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email">
                                                <div class="invalid-feedback" id="email-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password" name="password">
                                                <div class="invalid-feedback" id="password-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <a href="<?= site_url('admin/pelanggan') ?>" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Toggle account fields
        $('#create_account').change(function() {
            if ($(this).is(':checked')) {
                $('#account-fields').slideDown();
                $('#username, #email, #password').prop('required', true);
            } else {
                $('#account-fields').slideUp();
                $('#username, #email, #password').prop('required', false);
            }
        });

        // Form submission
        $('#form-pelanggan').submit(function(e) {
            e.preventDefault();

            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Validasi form
            let isValid = true;

            // Validasi nama pelanggan
            if (!$('#nama_pelanggan').val()) {
                $('#nama_pelanggan').addClass('is-invalid');
                $('#nama_pelanggan-error').text('Nama pelanggan harus diisi');
                isValid = false;
            }

            // Validasi no_hp
            if (!$('#no_hp').val()) {
                $('#no_hp').addClass('is-invalid');
                $('#no_hp-error').text('No HP harus diisi');
                isValid = false;
            }

            // Validasi alamat
            if (!$('#alamat').val()) {
                $('#alamat').addClass('is-invalid');
                $('#alamat-error').text('Alamat harus diisi');
                isValid = false;
            }

            if ($('#create_account').is(':checked')) {
                // Validasi username
                if (!$('#username').val()) {
                    $('#username').addClass('is-invalid');
                    $('#username-error').text('Username harus diisi');
                    isValid = false;
                }

                // Validasi email
                if (!$('#email').val()) {
                    $('#email').addClass('is-invalid');
                    $('#email-error').text('Email harus diisi');
                    isValid = false;
                }

                // Validasi password
                if (!$('#password').val()) {
                    $('#password').addClass('is-invalid');
                    $('#password-error').text('Password harus diisi');
                    isValid = false;
                }
            }

            if (!isValid) {
                return false;
            }

            // Kirim data ke server
            $.ajax({
                url: '<?= base_url('admin/pelanggan/save') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data pelanggan berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = '<?= base_url('admin/pelanggan') ?>';
                        });
                    } else {
                        if (response.messages) {
                            // Display validation errors
                            $.each(response.messages, function(field, message) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '-error').text(message);
                            });
                        } else {
                            // Display general error
                            showErrorAlert(response.message || 'Gagal menyimpan data pelanggan');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.messages) {
                            // Display validation errors
                            $.each(response.messages, function(field, message) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '-error').text(message);
                            });
                        } else {
                            // Display general error
                            showErrorAlert('Terjadi kesalahan saat menyimpan data');
                        }
                    } catch (e) {
                        showErrorAlert('Terjadi kesalahan saat menyimpan data');
                    }
                }
            });
        });

        // Helper function to show error alert
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