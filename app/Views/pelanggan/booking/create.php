<?= $this->extend('pelanggan/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-plus me-3 fs-4"></i>
                        <div>
                            <h4 class="mb-0">Booking Layanan Cuci Kendaraan1</h4>
                            <small class="opacity-75">Pilih layanan dan jadwal yang Anda inginkan</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Informasi Pelanggan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info border-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fs-3 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Pelanggan</h6>
                                        <strong><?= esc($pelanggan['nama_pelanggan']) ?></strong>
                                        <br><small class="text-muted">Kode: <?= esc($pelanggan['kode_pelanggan']) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning border-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fs-3 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Informasi Booking</h6>
                                        <small>Pastikan data yang Anda masukkan sudah benar sebelum melakukan booking.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Booking -->
                    <form id="bookingForm" method="POST">
                        <div class="row">
                            <!-- Pilih Layanan -->
                            <div class="col-md-6 mb-4">
                                <div class="form-section">
                                    <h5 class="form-section-title">
                                        <i class="fas fa-cogs text-primary me-2"></i>
                                        Pilih Layanan
                                    </h5>

                                    <div class="mb-3">
                                        <label for="layanan_id" class="form-label">Jenis Layanan *</label>
                                        <select class="form-select" id="layanan_id" name="layanan_id" required>
                                            <option value="">-- Pilih Layanan --</option>
                                            <?php foreach ($layanan_list as $layanan): ?>
                                                <option value="<?= $layanan['kode_layanan'] ?>"
                                                    data-harga="<?= $layanan['harga'] ?>"
                                                    data-durasi="<?= $layanan['durasi_menit'] ?>"
                                                    data-jenis="<?= $layanan['jenis_kendaraan'] ?>">
                                                    <?= esc($layanan['nama_layanan']) ?> -
                                                    <?= ucfirst($layanan['jenis_kendaraan']) ?> -
                                                    Rp <?= number_format($layanan['harga'], 0, ',', '.') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback" id="layanan_id-error"></div>
                                    </div>

                                    <!-- Info Layanan -->
                                    <div id="layanan-info" class="alert alert-light d-none">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Harga:</small>
                                                <div class="fw-bold text-primary" id="layanan-harga">-</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Durasi:</small>
                                                <div class="fw-bold" id="layanan-durasi">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Kendaraan -->
                            <div class="col-md-12 mb-4" id="kendaraan-section">
                                <div class="form-section">
                                    <h5 class="form-section-title">
                                        <i class="fas fa-car text-success me-2"></i>
                                        Data Kendaraan
                                    </h5>

                                    <!-- Kendaraan Motor Section -->
                                    <div id="motor-section" class="kendaraan-type-section d-none">
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="fas fa-motorcycle me-2"></i>
                                                    Kendaraan Motor
                                                </h6>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="no_plat_motor" class="form-label">Nomor Plat Motor *</label>
                                                <input type="text" class="form-control text-uppercase" id="no_plat_motor" name="no_plat_motor"
                                                    placeholder="Contoh: B 1234 ABC" maxlength="20">
                                                <div class="invalid-feedback" id="no_plat_motor-error"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="merk_motor" class="form-label">Merk Motor</label>
                                                <input type="text" class="form-control" id="merk_motor" name="merk_motor"
                                                    placeholder="Contoh: Honda, Yamaha, Suzuki">
                                                <div class="invalid-feedback" id="merk_motor-error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kendaraan Mobil Section -->
                                    <div id="mobil-section" class="kendaraan-type-section d-none">
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-info mb-3">
                                                    <i class="fas fa-car me-2"></i>
                                                    Kendaraan Mobil
                                                </h6>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="no_plat_mobil" class="form-label">Nomor Plat Mobil *</label>
                                                <input type="text" class="form-control text-uppercase" id="no_plat_mobil" name="no_plat_mobil"
                                                    placeholder="Contoh: B 1234 ABC" maxlength="20">
                                                <div class="invalid-feedback" id="no_plat_mobil-error"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="merk_mobil" class="form-label">Merk Mobil</label>
                                                <input type="text" class="form-control" id="merk_mobil" name="merk_mobil"
                                                    placeholder="Contoh: Toyota, Honda, Mitsubishi">
                                                <div class="invalid-feedback" id="merk_mobil-error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kendaraan Lainnya Section -->
                                    <div id="lainnya-section" class="kendaraan-type-section d-none">
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-warning mb-3">
                                                    <i class="fas fa-truck me-2"></i>
                                                    Kendaraan Lainnya
                                                </h6>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="no_plat_lainnya" class="form-label">Nomor Plat *</label>
                                                <input type="text" class="form-control text-uppercase" id="no_plat_lainnya" name="no_plat_lainnya"
                                                    placeholder="Contoh: B 1234 ABC" maxlength="20">
                                                <div class="invalid-feedback" id="no_plat_lainnya-error"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="merk_lainnya" class="form-label">Merk Kendaraan</label>
                                                <input type="text" class="form-control" id="merk_lainnya" name="merk_lainnya"
                                                    placeholder="Contoh: Isuzu, Hino, dll">
                                                <div class="invalid-feedback" id="merk_lainnya-error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info untuk multiple kendaraan -->
                                    <div id="multi-vehicle-info" class="alert alert-info d-none">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span>Anda dapat mengisi data untuk lebih dari satu jenis kendaraan jika layanan yang dipilih mendukung multiple kendaraan.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Jadwal Booking -->
                            <div class="col-md-6 mb-4">
                                <div class="form-section">
                                    <h5 class="form-section-title">
                                        <i class="fas fa-calendar-alt text-warning me-2"></i>
                                        Jadwal Booking
                                    </h5>

                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal Booking *</label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal"
                                            min="<?= date('Y-m-d') ?>" required>
                                        <div class="invalid-feedback" id="tanggal-error"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="jam" class="form-label">Jam Booking *</label>
                                        <select class="form-select" id="jam" name="jam" required disabled>
                                            <option value="">-- Pilih tanggal dan jenis kendaraan terlebih dahulu --</option>
                                        </select>
                                        <div class="invalid-feedback" id="jam-error"></div>
                                        <small class="text-muted">Jam operasional: 08:00 - 20:00</small>
                                    </div>

                                    <!-- Slot Availability Info -->
                                    <div id="slot-info" class="alert alert-info d-none">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span id="slot-message">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="col-md-6 mb-4">
                                <div class="form-section">
                                    <h5 class="form-section-title">
                                        <i class="fas fa-sticky-note text-info me-2"></i>
                                        Catatan Tambahan
                                    </h5>

                                    <div class="mb-3">
                                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                        <textarea class="form-control" id="catatan" name="catatan" rows="4"
                                            placeholder="Tambahkan catatan khusus untuk booking Anda (opsional)"></textarea>
                                        <div class="invalid-feedback" id="catatan-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?= site_url('pelanggan/dashboard') ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>

                                    <div>
                                        <button type="reset" class="btn btn-outline-warning me-2">
                                            <i class="fas fa-undo me-2"></i>Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            <span id="submitText">Buat Booking</span>
                                        </button>
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bookingForm');
        const layananSelect = document.getElementById('layanan_id');
        const tanggalInput = document.getElementById('tanggal');
        const jamSelect = document.getElementById('jam');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');

        // Handle layanan selection
        layananSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const layananInfo = document.getElementById('layanan-info');

            if (this.value) {
                const harga = selectedOption.dataset.harga;
                const durasi = selectedOption.dataset.durasi;
                const jenisKendaraan = selectedOption.dataset.jenis;

                document.getElementById('layanan-harga').textContent = 'Rp ' + parseInt(harga).toLocaleString('id-ID');
                document.getElementById('layanan-durasi').textContent = durasi + ' menit';

                // Show appropriate vehicle sections based on layanan type
                showVehicleSections(jenisKendaraan);

                layananInfo.classList.remove('d-none');

                // Refresh available slots if date is selected
                if (tanggalInput.value) {
                    loadAvailableSlots();
                }
            } else {
                layananInfo.classList.add('d-none');
                hideAllVehicleSections();
            }
        });

        // Handle date change
        tanggalInput.addEventListener('change', loadAvailableSlots);

        // Auto uppercase untuk semua input no plat
        ['no_plat_motor', 'no_plat_mobil', 'no_plat_lainnya'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });

        // Functions to handle vehicle sections
        function showVehicleSections(jenisKendaraan) {
            hideAllVehicleSections();

            // Get selected layanan name to check if it's a combo package
            const selectedOption = layananSelect.options[layananSelect.selectedIndex];
            const namaLayanan = selectedOption.text.toLowerCase();

            // Check if it's a combo package (contains keywords like "combo", "paket", "motor + mobil", etc.)
            const isComboPackage = namaLayanan.includes('combo') ||
                namaLayanan.includes('paket') ||
                namaLayanan.includes('motor + mobil') ||
                namaLayanan.includes('motor & mobil') ||
                namaLayanan.includes('motor dan mobil') ||
                namaLayanan.includes('all in one') ||
                namaLayanan.includes('lengkap');

            if (isComboPackage) {
                // Show both motor and mobil sections for combo packages
                document.getElementById('motor-section').classList.remove('d-none');
                document.getElementById('mobil-section').classList.remove('d-none');
                document.getElementById('multi-vehicle-info').classList.remove('d-none');
                setRequiredFields('motor', true);
                setRequiredFields('mobil', true);
            } else if (jenisKendaraan === 'motor') {
                document.getElementById('motor-section').classList.remove('d-none');
                setRequiredFields('motor', true);
            } else if (jenisKendaraan === 'mobil') {
                document.getElementById('mobil-section').classList.remove('d-none');
                setRequiredFields('mobil', true);
            } else if (jenisKendaraan === 'lainnya') {
                document.getElementById('lainnya-section').classList.remove('d-none');
                setRequiredFields('lainnya', true);
            }
        }

        function hideAllVehicleSections() {
            document.querySelectorAll('.kendaraan-type-section').forEach(section => {
                section.classList.add('d-none');
            });
            document.getElementById('multi-vehicle-info').classList.add('d-none');

            // Clear all required attributes
            ['motor', 'mobil', 'lainnya'].forEach(type => {
                setRequiredFields(type, false);
            });
        }

        function setRequiredFields(type, required) {
            const noPlatField = document.getElementById(`no_plat_${type}`);
            if (noPlatField) {
                if (required) {
                    noPlatField.setAttribute('required', 'required');
                } else {
                    noPlatField.removeAttribute('required');
                    noPlatField.value = '';
                    // Clear any validation errors
                    noPlatField.classList.remove('is-invalid');
                    const errorDiv = document.getElementById(`no_plat_${type}-error`);
                    if (errorDiv) errorDiv.textContent = '';
                }
            }
        }

        // Load available time slots
        function loadAvailableSlots() {
            const tanggal = tanggalInput.value;
            const layananId = layananSelect.value;

            if (!tanggal || !layananId) {
                jamSelect.disabled = true;
                jamSelect.innerHTML = '<option value="">-- Pilih tanggal dan layanan terlebih dahulu --</option>';
                document.getElementById('slot-info').classList.add('d-none');
                return;
            }

            // Get jenis kendaraan from selected layanan
            const selectedOption = layananSelect.options[layananSelect.selectedIndex];
            const jenisKendaraan = selectedOption.dataset.jenis;

            // Show loading
            jamSelect.disabled = true;
            jamSelect.innerHTML = '<option value="">Loading...</option>';
            document.getElementById('slot-info').classList.remove('d-none');
            document.getElementById('slot-message').textContent = 'Memuat slot yang tersedia...';

            // Fetch available slots
            fetch('<?= site_url('booking/get-available-slots') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `tanggal=${tanggal}&jenis_kendaraan=${jenisKendaraan}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        jamSelect.innerHTML = '<option value="">-- Pilih Jam --</option>';

                        if (data.data.length > 0) {
                            data.data.forEach(slot => {
                                jamSelect.innerHTML += `<option value="${slot}">${slot}</option>`;
                            });
                            jamSelect.disabled = false;
                            document.getElementById('slot-message').textContent = `${data.data.length} slot tersedia`;
                            document.getElementById('slot-info').className = 'alert alert-success';
                        } else {
                            jamSelect.innerHTML = '<option value="">-- Tidak ada slot tersedia --</option>';
                            document.getElementById('slot-message').textContent = 'Maaf, tidak ada slot tersedia untuk tanggal dan jenis kendaraan ini';
                            document.getElementById('slot-info').className = 'alert alert-warning';
                        }
                    } else {
                        jamSelect.innerHTML = '<option value="">-- Error loading slots --</option>';
                        document.getElementById('slot-message').textContent = data.message || 'Gagal memuat slot';
                        document.getElementById('slot-info').className = 'alert alert-danger';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    jamSelect.innerHTML = '<option value="">-- Error loading slots --</option>';
                    document.getElementById('slot-message').textContent = 'Terjadi kesalahan saat memuat slot';
                    document.getElementById('slot-info').className = 'alert alert-danger';
                });
        }

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

            // Show loading state
            submitBtn.disabled = true;
            submitText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

            // Submit form
            const formData = new FormData(form);

            fetch('<?= site_url('pelanggan/booking/store') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Berhasil!',
                            text: data.message,
                            showConfirmButton: true,
                            confirmButtonText: 'Lihat Detail',
                            confirmButtonColor: '#0088cc'
                        }).then((result) => {
                            if (result.isConfirmed && data.data.redirect) {
                                window.location.href = data.data.redirect;
                            } else {
                                window.location.href = '<?= site_url('pelanggan/booking/history') ?>';
                            }
                        });
                    } else {
                        // Show errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById(field);
                                const errorDiv = document.getElementById(field + '-error');
                                if (input && errorDiv) {
                                    input.classList.add('is-invalid');
                                    errorDiv.textContent = data.errors[field];
                                }
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Booking Gagal',
                            text: data.message || 'Terjadi kesalahan saat membuat booking',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                        confirmButtonColor: '#dc3545'
                    });
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitText.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Buat Booking';
                });
        });

        // Form reset handler
        form.addEventListener('reset', function() {
            document.getElementById('layanan-info').classList.add('d-none');
            document.getElementById('slot-info').classList.add('d-none');
            jamSelect.disabled = true;
            jamSelect.innerHTML = '<option value="">-- Pilih tanggal dan layanan terlebih dahulu --</option>';

            // Hide all vehicle sections
            hideAllVehicleSections();

            // Clear errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        });
    });
</script>

<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        margin-bottom: 1rem;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #dee2e6;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0088cc, #00aaff) !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0088cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 136, 204, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0088cc, #00aaff);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0077b6, #0099dd);
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 12px;
    }

    .card {
        border-radius: 16px;
    }

    .kendaraan-type-section {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .kendaraan-type-section:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .kendaraan-type-section h6 {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
</style>

<?= $this->endSection() ?>