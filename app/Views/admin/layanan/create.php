<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= $title; ?></h1>
            <p class="mb-0 text-muted">Buat layanan cuci kendaraan baru dengan informasi lengkap</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/layanan'); ?>">Layanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Layanan</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-white text-primary me-3">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white">Tambah Layanan Baru</h5>
                            <small class="text-white-50">Lengkapi informasi layanan cuci kendaraan</small>
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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form id="layananForm" action="<?= base_url('admin/layanan/store'); ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <?= csrf_field(); ?>

                        <!-- Informasi Dasar -->
                        <div class="form-section mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon-circle bg-primary text-white me-3">
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
                                            <i class="fas fa-barcode me-2 text-warning"></i>Kode Layanan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="kode_layanan" name="kode_layanan"
                                            readonly value="Loading..." placeholder="LYN-YYYYMMDD-XXXXX">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Kode otomatis akan digenerate saat form dibuka
                                        </small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="nama_layanan" class="form-label fw-bold">
                                            <i class="fas fa-tag me-2 text-warning"></i>Nama Layanan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nama_layanan" name="nama_layanan"
                                            required placeholder="Contoh: Cuci Motor Reguler">
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
                                            <option value="motor">🏍️ Motor</option>
                                            <option value="mobil">🚗 Mobil</option>
                                            <option value="lainnya">🚚 Lainnya</option>
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
                                                required placeholder="0" min="0">
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
                                                required min="1" placeholder="60">
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
                                            <option value="aktif">✅ Aktif</option>
                                            <option value="nonaktif">❌ Nonaktif</option>
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
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-image"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 text-gray-800">Media & Deskripsi</h5>
                                    <small class="text-muted">Foto dan deskripsi layanan</small>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="deskripsi" class="form-label fw-bold">
                                            <i class="fas fa-align-left me-2 text-warning"></i>Deskripsi
                                        </label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                            placeholder="Deskripsikan layanan cuci kendaraan ini secara detail..."></textarea>
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

                                    <!-- Preview Area -->
                                    <div id="preview-container" class="preview-container d-none">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-0">
                                                <img id="foto-preview" src="" alt="Preview" class="img-fluid w-100" style="height: 150px; object-fit: cover; border-radius: 8px;">
                                            </div>
                                            <div class="card-footer bg-light border-0 text-center py-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPreview()">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Placeholder -->
                                    <div id="photo-placeholder" class="card border-2 border-dashed border-light" style="min-height: 150px;">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                                            <div class="placeholder-icon mb-2">
                                                <i class="fas fa-image fa-2x text-light"></i>
                                            </div>
                                            <small class="text-muted">Pilih foto untuk preview</small>
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
                                                <i class="fas fa-shield-alt text-success fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark">Validasi Data</h6>
                                                <small>Pastikan semua informasi sudah benar sebelum menyimpan</small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="<?= base_url('admin/layanan'); ?>" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Kembali
                                            </a>
                                            <button type="button" class="btn btn-outline-info" onclick="resetForm()">
                                                <i class="fas fa-undo me-2"></i>Reset
                                            </button>
                                            <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                                                <i class="fas fa-save me-2"></i>Simpan Layanan
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
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .upload-area:hover::before {
        left: 100%;
    }

    .upload-area:hover {
        border-color: #667eea !important;
        background: linear-gradient(135deg, #f0f2ff 0%, #e8ecff 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
    }

    .upload-label:hover .upload-icon i {
        color: #667eea !important;
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
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
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

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-outline-secondary:hover,
    .btn-outline-warning:hover {
        transform: translateY(-2px);
    }

    .card {
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1);
    }

    .card-header {
        border-radius: 20px 20px 0 0 !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    // Auto-generate kode layanan saat halaman dimuat
    function generateKodeLayanan() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const random = String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');

        const kode = `LYN-${year}${month}${day}-${random}`;
        document.getElementById('kode_layanan').value = kode;
    }

    // Generate kode saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        generateKodeLayanan();

        // AJAX Form Submit
        const layananForm = document.getElementById('layananForm');
        if (!layananForm) {
            console.error('Form not found');
            console.log('Available forms:', document.querySelectorAll('form'));
            return;
        }

        console.log('Form found:', layananForm);
        console.log('Submit buttons in form:', layananForm.querySelectorAll('button[type="submit"]'));

        layananForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Reset previous validation state
            layananForm.classList.remove('was-validated');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Show loading - use specific ID first
            let submitBtn = document.getElementById('submitBtn');
            if (!submitBtn) {
                console.error('Submit button with ID not found, trying other selectors...');
                submitBtn = document.querySelector('button[type="submit"]');
            }
            if (!submitBtn) {
                submitBtn = layananForm.querySelector('button[type="submit"]');
            }

            if (!submitBtn) {
                console.error('Submit button not found with any selector');
                console.log('Available buttons:', document.querySelectorAll('button'));
                console.log('Form HTML contains submit button:', layananForm.innerHTML.includes('type="submit"'));
                return;
            }

            console.log('Submit button found:', submitBtn);

            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            submitBtn.disabled = true;

            // Prepare FormData
            const formData = new FormData(layananForm);

            // Debug: Log form data
            console.log('Form data being sent:');
            console.log('File input:', document.getElementById('foto').files[0]);
            console.log('Form fields:', {
                kode_layanan: formData.get('kode_layanan'),
                nama_layanan: formData.get('nama_layanan'),
                jenis_kendaraan: formData.get('jenis_kendaraan'),
                harga: formData.get('harga'),
                durasi_menit: formData.get('durasi_menit'),
                status: formData.get('status'),
                deskripsi: formData.get('deskripsi'),
                foto: formData.get('foto') ? formData.get('foto').name : 'no file'
            });

            fetch('<?= base_url('admin/layanan/store'); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);

                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url('admin/layanan'); ?>';
                        });
                    } else {
                        if (data.errors && Object.keys(data.errors).length > 0) {
                            // Display validation errors
                            Object.keys(data.errors).forEach(field => {
                                const input = document.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    // Look for feedback in parent or sibling elements
                                    let feedback = input.parentNode.querySelector('.invalid-feedback');
                                    if (!feedback) {
                                        feedback = input.closest('.form-group')?.querySelector('.invalid-feedback');
                                    }
                                    if (feedback) {
                                        feedback.textContent = data.errors[field];
                                        feedback.style.display = 'block';
                                    }
                                }
                            });
                            layananForm.classList.add('was-validated');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan saat menyimpan data',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan jaringan: ' + error.message,
                    });
                })
                .finally(() => {
                    // Reset button
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
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

        // Format currency untuk input harga
        const hargaInput = document.getElementById('harga');
        if (hargaInput) {
            hargaInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value) {
                    e.target.value = value;
                    // Update label atau display jika diperlukan
                    const formatted = new Intl.NumberFormat('id-ID').format(value);
                    e.target.title = `Rp ${formatted}`;
                }
            });
        }

        // Auto-generate nama layanan berdasarkan jenis kendaraan (opsional)
        const jenisKendaraanSelect = document.getElementById('jenis_kendaraan');
        if (jenisKendaraanSelect) {
            jenisKendaraanSelect.addEventListener('change', function(e) {
                const namaInput = document.getElementById('nama_layanan');
                if (!namaInput.value) {
                    const jenis = e.target.value;
                    const suggestions = {
                        'motor': 'Cuci Motor Reguler',
                        'mobil': 'Cuci Mobil Reguler',
                        'lainnya': 'Cuci Kendaraan Reguler'
                    };
                    if (suggestions[jenis]) {
                        namaInput.placeholder = `Contoh: ${suggestions[jenis]}`;
                    }
                }
            });
        }
    });

    // Preview foto sebelum upload
    function previewImage(input) {
        const file = input.files[0];
        const previewContainer = document.getElementById('preview-container');
        const previewImg = document.getElementById('foto-preview');
        const placeholder = document.getElementById('photo-placeholder');

        if (file) {
            // Validasi ukuran file (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB. Silakan pilih file yang lebih kecil.',
                    confirmButtonColor: '#4e73df'
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
                    confirmButtonColor: '#4e73df'
                });
                clearPreview();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('d-none');
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            clearPreview();
        }
    }

    // Clear preview foto
    function clearPreview() {
        const input = document.getElementById('foto');
        const previewContainer = document.getElementById('preview-container');
        const placeholder = document.getElementById('photo-placeholder');

        input.value = '';
        previewContainer.classList.add('d-none');
        placeholder.style.display = 'block';
    }

    // Reset seluruh form
    function resetForm() {
        Swal.fire({
            title: 'Reset Form?',
            text: 'Semua data yang sudah diisi akan dihapus. Apakah Anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('form').reset();
                document.querySelector('form').classList.remove('was-validated');
                clearPreview();
                generateKodeLayanan();

                // Reset semua select ke default
                document.getElementById('jenis_kendaraan').selectedIndex = 0;
                document.getElementById('status').selectedIndex = 0;

                Swal.fire({
                    icon: 'success',
                    title: 'Form direset!',
                    text: 'Semua data sudah dibersihkan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>