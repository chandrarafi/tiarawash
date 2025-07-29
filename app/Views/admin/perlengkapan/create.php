<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tambah Perlengkapan</h1>
            <p class="mb-0 text-secondary">Tambah data perlengkapan baru</p>
        </div>
        <a href="<?= site_url('admin/perlengkapan') ?>" class="btn btn-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="form-perlengkapan" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Perlengkapan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                    <div class="invalid-feedback" id="nama-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="alat">Alat</option>
                                        <option value="bahan">Bahan</option>
                                    </select>
                                    <div class="invalid-feedback" id="kategori-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="stok" name="stok" min="0" required>
                                        <span class="input-group-text">unit</span>
                                    </div>
                                    <div class="invalid-feedback" id="stok-error"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga" name="harga" min="0" step="0.01" required>
                                    </div>
                                    <div class="invalid-feedback" id="harga-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"></textarea>
                                    <div class="invalid-feedback" id="deskripsi-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <a href="<?= site_url('admin/perlengkapan') ?>" class="btn btn-secondary me-2">Batal</a>
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
        // Form submission
        $('#form-perlengkapan').submit(function(e) {
            e.preventDefault();

            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Validasi form
            let isValid = true;

            // Validasi nama
            if (!$('#nama').val()) {
                $('#nama').addClass('is-invalid');
                $('#nama-error').text('Nama perlengkapan harus diisi');
                isValid = false;
            }

            // Validasi kategori
            if (!$('#kategori').val()) {
                $('#kategori').addClass('is-invalid');
                $('#kategori-error').text('Kategori harus dipilih');
                isValid = false;
            }

            // Validasi stok
            if (!$('#stok').val()) {
                $('#stok').addClass('is-invalid');
                $('#stok-error').text('Stok awal harus diisi');
                isValid = false;
            }

            // Validasi harga
            if (!$('#harga').val()) {
                $('#harga').addClass('is-invalid');
                $('#harga-error').text('Harga harus diisi');
                isValid = false;
            }

            if (!isValid) {
                return false;
            }

            // Kirim data ke server
            $.ajax({
                url: '<?= site_url('admin/perlengkapan/save') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data perlengkapan berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = '<?= site_url('admin/perlengkapan') ?>';
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
                            showErrorAlert(response.message || 'Gagal menyimpan data perlengkapan');
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