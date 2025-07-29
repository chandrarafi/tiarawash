<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Edit Pelanggan</h1>
        <p class="mb-0 text-secondary">Edit data pelanggan</p>
    </div>
    <a href="<?= site_url('admin/pelanggan') ?>" class="btn btn-secondary d-flex align-items-center">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="pelangganForm" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode_pelanggan" class="form-label">Kode Pelanggan</label>
                                <input type="text" class="form-control" id="kode_pelanggan" name="kode_pelanggan" readonly>
                                <div class="invalid-feedback" id="kode_pelanggan-error"></div>
                            </div>

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
                                            <input class="form-check-input" type="checkbox" id="changeAccount" name="changeAccount">
                                            <label class="form-check-label" for="changeAccount">Ubah Akun Pelanggan</label>
                                        </div>
                                        <small class="text-muted">Aktifkan untuk mengubah informasi akun pelanggan</small>
                                    </div>

                                    <div id="accountOptions" style="display: none;">
                                        <div id="accountFormFields">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email">
                                                <div class="invalid-feedback" id="email-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="username" name="username">
                                                <div class="invalid-feedback" id="username-error"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                                                <div class="invalid-feedback" id="password-error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="existingUserField">
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">Akun Saat Ini</label>
                                            <select class="form-select" id="user_id" name="user_id" disabled>
                                                <option value="">Pilih User</option>
                                            </select>
                                            <div class="invalid-feedback" id="user_id-error"></div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const kode = '<?= $kode_pelanggan ?? '' ?>';

        // Memuat data pelanggan
        loadPelanggan(kode);

        // Memuat data user yang tersedia
        loadUsers();

        // Toggle form akun
        $('#changeAccount').on('change', function() {
            if ($(this).is(':checked')) {
                $('#accountOptions').show();
            } else {
                $('#accountOptions').hide();
                $('#existingUserField').show();
                $('#user_id').prop('disabled', true);
            }
        });

        // Fungsi untuk memuat data pelanggan
        function loadPelanggan(kode) {
            $.ajax({
                url: '<?= site_url('admin/pelanggan/getByKode/') ?>' + kode,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const pelanggan = response.data;

                        // Isi form dengan data pelanggan
                        $('#kode_pelanggan').val(pelanggan.kode_pelanggan);
                        $('#nama_pelanggan').val(pelanggan.nama_pelanggan);
                        $('#no_hp').val(pelanggan.no_hp);
                        $('#alamat').val(pelanggan.alamat);

                        // Isi data user
                        if (pelanggan.user_id && pelanggan.email) {
                            const userOption = `<option value="${pelanggan.user_id}" selected>${pelanggan.email} - ${pelanggan.name || ''}</option>`;
                            $('#user_id').html(userOption);

                            // Isi form akun dengan data yang sudah ada
                            $('#email').val(pelanggan.email);
                            $('#username').val(pelanggan.username);
                            // Password dikosongkan karena tidak perlu diisi ulang
                        } else {
                            $('#user_id').html('<option value="">Tidak ada akun terkait</option>');
                        }
                    } else {
                        showErrorAlert('Data pelanggan tidak ditemukan');
                        setTimeout(() => {
                            window.location.href = '<?= site_url('admin/pelanggan') ?>';
                        }, 1500);
                    }
                },
                error: function(xhr, status, error) {
                    showErrorAlert('Gagal mengambil data pelanggan');
                    setTimeout(() => {
                        window.location.href = '<?= site_url('admin/pelanggan') ?>';
                    }, 1500);
                }
            });
        }

        // Fungsi untuk memuat data user
        function loadUsers() {
            $.ajax({
                url: '<?= site_url('admin/pelanggan/getUsers') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let users = response.data;
                        let options = '<option value="">Pilih User</option>';

                        $.each(users, function(index, user) {
                            options += '<option value="' + user.id + '">' + user.email + ' - ' + user.name + '</option>';
                        });

                        $('#new_user_id').html(options);
                    }
                },
                error: function(xhr, status, error) {
                    showErrorAlert('Gagal memuat data user');
                }
            });
        }

        // Submit form
        $('#pelangganForm').on('submit', function(e) {
            e.preventDefault();

            // Reset error messages
            resetErrors();

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

            // Validasi akun jika diubah
            if ($('#changeAccount').is(':checked')) {
                // Validasi field akun
                if (!$('#email').val()) {
                    $('#email').addClass('is-invalid');
                    $('#email-error').text('Email harus diisi');
                    isValid = false;
                }

                if (!$('#username').val()) {
                    $('#username').addClass('is-invalid');
                    $('#username-error').text('Username harus diisi');
                    isValid = false;
                }

                // Password hanya wajib jika pelanggan belum memiliki akun
                const hasAccount = $('#user_id').val() !== '';
                if (!hasAccount && !$('#password').val()) {
                    $('#password').addClass('is-invalid');
                    $('#password-error').text('Password harus diisi');
                    isValid = false;
                }
            }

            if (!isValid) {
                return;
            }

            // Disable submit button
            $('#btnSave').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            // Siapkan data untuk dikirim
            const formData = new FormData(this);

            // Tambahkan flag untuk mengubah akun
            const changeAccount = $('#changeAccount').is(':checked');
            formData.append('change_account', changeAccount ? '1' : '0');

            // Kirim data ke server
            $.ajax({
                url: '<?= site_url('admin/pelanggan/update/') ?>' + $('#kode_pelanggan').val(),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data pelanggan berhasil disimpan',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '<?= site_url('admin/pelanggan') ?>';
                        });
                    } else {
                        $('#btnSave').prop('disabled', false).text('Simpan');
                        showErrorAlert(response.message || 'Gagal menyimpan data');

                        // Handle validation errors
                        if (response.messages) {
                            handleValidationErrors(response.messages);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#btnSave').prop('disabled', false).text('Simpan');



                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.messages) {
                            handleValidationErrors(response.messages);
                        }
                        errorMessage = response.message || errorMessage;
                    } catch (e) {

                    }

                    showErrorAlert(errorMessage);
                }
            });
        });

        // Fungsi untuk menampilkan error validasi
        function handleValidationErrors(errors) {
            if (typeof errors === 'object') {
                for (const field in errors) {
                    const errorMsg = errors[field];
                    $(`#${field}`).addClass('is-invalid');
                    $(`#${field}-error`).text(errorMsg);
                }
            }
        }

        // Fungsi untuk mereset error
        function resetErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        // Fungsi untuk menampilkan alert error
        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
</script>
<?= $this->endSection() ?>