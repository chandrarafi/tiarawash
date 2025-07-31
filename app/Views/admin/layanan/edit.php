<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
            <p class="mb-0 text-muted">Edit informasi layanan cuci kendaraan</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/layanan'); ?>">Layanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Layanan</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-warning text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-white text-warning me-3">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Edit Layanan</h5>
                            <small class="text-white-50">Perbarui informasi layanan cuci kendaraan</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5">
                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="layananEditForm" action="<?= base_url('admin/layanan/update/' . $layanan['kode_layanan']); ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Informasi Dasar -->
                        <div class="form-section mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-gray-800">Informasi Dasar</h5>
                                    <small class="text-muted">Data utama layanan cuci kendaraan</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="kode_layanan" class="form-label fw-bold">
                                            <i class="fas fa-barcode me-2 text-warning"></i>Kode Layanan
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" id="kode_layanan" name="kode_layanan"
                                                readonly value="<?= old('kode_layanan', $layanan['kode_layanan'] ?? ''); ?>" style="background-color: #f8f9fa;">
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Kode layanan tidak dapat diubah
                                        </small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="nama_layanan" class="form-label fw-bold">
                                            <i class="fas fa-tag me-2 text-warning"></i>Nama Layanan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nama_layanan" name="nama_layanan"
                                            required value="<?= old('nama_layanan', $layanan['nama_layanan'] ?? ''); ?>" placeholder="Contoh: Cuci Motor Reguler">
                                        <div class="invalid-feedback">
                                            Nama layanan wajib diisi
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="jenis_kendaraan" class="form-label fw-bold">
                                            <i class="fas fa-car me-2 text-warning"></i>Jenis Kendaraan <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan" required>
                                            <option value="">Pilih Jenis Kendaraan</option>
                                            <option value="motor" <?= old('jenis_kendaraan', $layanan['jenis_kendaraan'] ?? '') == 'motor' ? 'selected' : ''; ?>>
                                                🏍️ Motor
                                            </option>
                                            <option value="mobil" <?= old('jenis_kendaraan', $layanan['jenis_kendaraan'] ?? '') == 'mobil' ? 'selected' : ''; ?>>
                                                🚗 Mobil
                                            </option>
                                            <option value="lainnya" <?= old('jenis_kendaraan', $layanan['jenis_kendaraan'] ?? '') == 'lainnya' ? 'selected' : ''; ?>>
                                                🚚 Lainnya
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Jenis kendaraan wajib dipilih
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="harga" class="form-label fw-bold">
                                            <i class="fas fa-money-bill-wave me-2 text-warning"></i>Harga <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="number" class="form-control" id="harga" name="harga"
                                                required value="<?= old('harga', $layanan['harga'] ?? 0); ?>" placeholder="0" min="0">
                                        </div>
                                        <div class="invalid-feedback">
                                            Harga layanan wajib diisi
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="durasi_menit" class="form-label fw-bold">
                                            <i class="fas fa-clock me-2 text-warning"></i>Durasi <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="durasi_menit" name="durasi_menit"
                                                required value="<?= old('durasi_menit', $layanan['durasi_menit'] ?? 60); ?>" min="1">
                                            <span class="input-group-text bg-light">Menit</span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Durasi layanan wajib diisi
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="status" class="form-label fw-bold">
                                            <i class="fas fa-toggle-on me-2 text-warning"></i>Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="aktif" <?= old('status', $layanan['status'] ?? 'aktif') == 'aktif' ? 'selected' : ''; ?>>
                                                ✅ Aktif
                                            </option>
                                            <option value="nonaktif" <?= old('status', $layanan['status'] ?? 'aktif') == 'nonaktif' ? 'selected' : ''; ?>>
                                                ❌ Nonaktif
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Status layanan wajib dipilih
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media & Deskripsi -->
                        <div class="form-section mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="fas fa-image"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-gray-800">Media & Deskripsi</h5>
                                    <small class="text-muted">Foto dan deskripsi layanan</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="deskripsi" class="form-label fw-bold">
                                            <i class="fas fa-align-left me-2 text-warning"></i>Deskripsi Layanan
                                        </label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                            placeholder="Deskripsikan layanan cuci kendaraan ini secara detail..."><?= old('deskripsi', $layanan['deskripsi'] ?? ''); ?></textarea>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Jelaskan detail layanan, proses cuci, dan keunggulan
                                        </small>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="foto" class="form-label fw-bold">
                                            <i class="fas fa-camera me-2 text-warning"></i>Foto Layanan
                                        </label>
                                        <input type="file" class="form-control" id="foto" name="foto"
                                            accept="image/*" onchange="previewImage(this)">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Format: JPG, JPEG, PNG. Maksimal 2MB
                                        </small>
                                    </div>

                                    <!-- Current Photo -->
                                    <div id="current-foto" <?= empty($layanan['foto']) ? 'class="d-none"' : '' ?>>
                                        <label class="form-label fw-bold text-info">
                                            <i class="fas fa-image me-2"></i>Foto Saat Ini
                                        </label>
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-0">
                                                <?php if (!empty($layanan['foto'])) : ?>
                                                    <img id="current-image"
                                                        src="<?= base_url('uploads/layanan/' . $layanan['foto']); ?>"
                                                        alt="<?= $layanan['nama_layanan'] ?? 'Foto Layanan'; ?>"
                                                        class="img-fluid w-100"
                                                        style="height: 150px; object-fit: cover; border-radius: 8px;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display: none; height: 150px;" class="d-flex align-items-center justify-content-center bg-light rounded">
                                                        <div class="text-center">
                                                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                                                            <br><small class="text-muted">Foto tidak dapat dimuat</small>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="height: 150px;" class="d-flex align-items-center justify-content-center bg-light rounded">
                                                        <div class="text-center">
                                                            <i class="fas fa-image text-muted fa-2x mb-2"></i>
                                                            <br><small class="text-muted">Tidak ada foto</small>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer bg-light border-0 text-center py-2">
                                                <small class="text-muted">
                                                    <?php if (!empty($layanan['foto'])): ?>
                                                        Foto: <?= $layanan['foto']; ?>
                                                    <?php else: ?>
                                                        Belum ada foto
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Preview New Photo -->
                                    <div id="preview-container" class="preview-container d-none">
                                        <label class="form-label fw-bold text-success">
                                            <i class="fas fa-eye me-2"></i>Preview Foto Baru
                                        </label>
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-0">
                                                <img id="foto-preview" src="" alt="Preview" class="img-fluid w-100" style="height: 150px; object-fit: cover; border-radius: 8px;">
                                            </div>
                                            <div class="card-footer bg-light border-0 text-center py-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger me-2" onclick="cancelPreview()">
                                                    <i class="fas fa-times me-1"></i>Batal
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearPreview()">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Placeholder when no current photo -->
                                    <div id="no-photo-placeholder" <?= !empty($layanan['foto']) ? 'class="d-none"' : '' ?>>
                                        <label class="form-label fw-bold text-muted">
                                            <i class="fas fa-image me-2"></i>Foto Layanan
                                        </label>
                                        <div class="card border-2 border-dashed border-light" style="min-height: 150px;">
                                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                                                <div class="placeholder-icon mb-2">
                                                    <i class="fas fa-image fa-2x text-light"></i>
                                                </div>
                                                <small class="text-muted">Belum ada foto</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-actions mt-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-4">
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                                <div class="d-flex align-items-center text-muted mb-3 mb-md-0">
                                                    <div class="me-3">
                                                        <i class="fas fa-edit text-warning fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark">Update Data</h6>
                                                        <small>Pastikan semua perubahan sudah benar sebelum menyimpan</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="<?= base_url('admin/layanan'); ?>" class="btn btn-outline-secondary">
                                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                                    </a>
                                                    <button type="button" class="btn btn-outline-info" onclick="resetForm()">
                                                        <i class="fas fa-undo me-2"></i>Reset
                                                    </button>
                                                    <button type="submit" class="btn btn-warning px-4">
                                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .upload-area {
        min-height: 140px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f6 100%);
        border: 2px dashed #cbd5e0 !important;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .upload-area::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 193, 7, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .upload-area:hover::before {
        left: 100%;
    }

    .upload-area:hover {
        border-color: #ffc107 !important;
        background: linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255, 193, 7, 0.15);
    }

    .upload-label:hover .upload-icon i {
        color: #ffc107 !important;
        transform: scale(1.2) rotateY(180deg);
    }

    .upload-icon i {
        transition: all 0.5s ease;
    }

    .preview-container .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .form-control-lg,
    .form-select-lg {
        border-radius: 12px;
        border: 2px solid #e8ecef;
        padding: 15px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fafbfc;
    }

    .form-control-lg:focus,
    .form-select-lg:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.15);
        background: #ffffff;
        transform: translateY(-1px);
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .btn-lg {
        border-radius: 12px;
        padding: 15px 30px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        text-transform: none;
        letter-spacing: 0.5px;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
    }

    .btn-outline-secondary:hover,
    .btn-outline-info:hover {
        transform: translateY(-2px);
    }

    .card {
        border-radius: 20px;
    }

    .card-header {
        border-radius: 20px 20px 0 0 !important;
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800);
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
        font-size: 1.2em;
    }

    .form-section {
        position: relative;
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f3f4;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border-radius: 15px 15px 0 0;
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .d-sm-flex {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .btn-group {
            flex-direction: column;
            width: 100%;
        }

        .btn-group .btn {
            margin-bottom: 8px;
            margin-right: 0 !important;
        }
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Form validation and initialization
    document.addEventListener('DOMContentLoaded', function() {
        // AJAX Form Submit
        $('#layananEditForm').on('submit', function(e) {
            e.preventDefault();

            // Reset previous validation
            $(this).removeClass('was-validated');
            $('.invalid-feedback').hide();
            $('.form-control, .form-select').removeClass('is-invalid');

            // Check form validity
            if (!this.checkValidity()) {
                $(this).addClass('was-validated');
                return;
            }

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...').prop('disabled', true);

            // Prepare form data
            const formData = new FormData(this);

            // AJAX request
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.status) {
                        // Success
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#ffc107',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url('admin/layanan') ?>';
                        });
                    } else {
                        // Error
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message,
                            confirmButtonColor: '#ffc107'
                        });

                        // Show validation errors
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                const inputField = $('[name="' + field + '"]');
                                inputField.addClass('is-invalid');
                                inputField.siblings('.invalid-feedback').text(message).show();
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan koneksi: ' + error,
                        confirmButtonColor: '#ffc107'
                    });
                },
                complete: function() {
                    // Reset button state
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Bootstrap form validation for real-time feedback
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('input', event => {
                if (event.target.checkValidity()) {
                    event.target.classList.remove('is-invalid');
                    event.target.classList.add('is-valid');
                } else {
                    event.target.classList.remove('is-valid');
                    event.target.classList.add('is-invalid');
                }
            }, false);
        });
    });

    // Preview foto sebelum upload
    function previewImage(input) {
        const file = input.files[0];
        const previewContainer = document.getElementById('preview-container');
        const previewImg = document.getElementById('foto-preview');
        const currentFoto = document.getElementById('current-foto');
        const placeholder = document.getElementById('no-photo-placeholder');

        if (file) {
            // Validasi ukuran file (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB. Silakan pilih file yang lebih kecil.',
                    confirmButtonColor: '#ffc107'
                });
                clearPreview();
                return;
            }

            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Didukung',
                    text: 'Harap pilih file dengan format JPG, JPEG, atau PNG.',
                    confirmButtonColor: '#ffc107'
                });
                clearPreview();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('d-none');
                currentFoto.classList.add('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            clearPreview();
        }
    }

    // Batalkan preview dan kembali ke foto asli
    function cancelPreview() {
        const input = document.getElementById('foto');
        const previewContainer = document.getElementById('preview-container');
        const currentFoto = document.getElementById('current-foto');
        const placeholder = document.getElementById('no-photo-placeholder');
        const hasCurrentPhoto = <?= !empty($layanan['foto']) ? 'true' : 'false' ?>;

        input.value = '';
        previewContainer.classList.add('d-none');

        if (hasCurrentPhoto) {
            currentFoto.classList.remove('d-none');
            placeholder.classList.add('d-none');
        } else {
            currentFoto.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    }

    // Clear preview foto
    function clearPreview() {
        const input = document.getElementById('foto');
        const previewContainer = document.getElementById('preview-container');
        const currentFoto = document.getElementById('current-foto');
        const placeholder = document.getElementById('no-photo-placeholder');
        const hasCurrentPhoto = <?= !empty($layanan['foto']) ? 'true' : 'false' ?>;

        input.value = '';
        previewContainer.classList.add('d-none');

        if (hasCurrentPhoto) {
            currentFoto.classList.remove('d-none');
            placeholder.classList.add('d-none');
        } else {
            currentFoto.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    }

    // Reset form ke nilai awal
    function resetForm() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua perubahan akan dibatalkan dan form akan kembali ke nilai semula. Apakah Anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reset form ke nilai original
                document.getElementById('nama_layanan').value = '<?= addslashes($layanan['nama_layanan'] ?? ''); ?>';
                document.getElementById('jenis_kendaraan').value = '<?= $layanan['jenis_kendaraan'] ?? ''; ?>';
                document.getElementById('harga').value = '<?= $layanan['harga'] ?? 0; ?>';
                document.getElementById('durasi_menit').value = '<?= $layanan['durasi_menit'] ?? 60; ?>';
                document.getElementById('status').value = '<?= $layanan['status'] ?? 'aktif'; ?>';
                document.getElementById('deskripsi').value = '<?= addslashes($layanan['deskripsi'] ?? ''); ?>';

                // Reset validation state
                document.querySelector('form').classList.remove('was-validated');

                // Reset photo preview
                cancelPreview();

                Swal.fire({
                    icon: 'success',
                    title: 'Form direset!',
                    text: 'Semua data sudah dikembalikan ke nilai semula.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    // Format currency untuk input harga
    document.getElementById('harga').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value) {
            e.target.value = value;
            const formatted = new Intl.NumberFormat('id-ID').format(value);
            e.target.title = `Rp ${formatted}`;
        }
    });
</script>
<?= $this->endSection(); ?>