<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 10px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .form-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .form-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
    }

    .form-card .card-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .time-slots {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .time-slot {
        padding: 0.5rem 0.25rem;
        text-align: center;
        border: 1px solid #ced4da;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        background: white;
        font-size: 0.875rem;
    }

    .time-slot:hover {
        border-color: #28a745;
        background: #f8fff9;
    }

    .time-slot.selected {
        border-color: #28a745;
        background: #28a745;
        color: white;
    }

    .layanan-info {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        margin-top: 0.5rem;
        display: none;
    }

    .required {
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .time-slots {
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="fas fa-plus-circle me-2"></i>
                    Tambah Booking Baru
                </h2>
                <p class="mb-0 opacity-75">
                    Buat booking baru untuk pelanggan
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= site_url('admin/booking') ?>" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('admin/booking/store') ?>" method="POST" id="bookingForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-6">
                <!-- Customer Information -->
                <div class="form-card card">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>Informasi Pelanggan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="pelanggan_id" class="form-label">
                                Pelanggan <span class="required">*</span>
                            </label>
                            <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($pelanggan as $p): ?>
                                    <option value="<?= esc($p['kode_pelanggan']) ?>"
                                        <?= old('pelanggan_id') == $p['kode_pelanggan'] ? 'selected' : '' ?>>
                                        <?= esc($p['nama_pelanggan']) ?> - <?= esc($p['no_hp'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="form-card card">
                    <div class="card-header">
                        <i class="fas fa-car me-2"></i>Informasi Kendaraan
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kendaraan" class="form-label">
                                        Jenis Kendaraan <span class="required">*</span>
                                    </label>
                                    <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="motor" <?= old('jenis_kendaraan') == 'motor' ? 'selected' : '' ?>>Motor</option>
                                        <option value="mobil" <?= old('jenis_kendaraan') == 'mobil' ? 'selected' : '' ?>>Mobil</option>
                                        <option value="lainnya" <?= old('jenis_kendaraan') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_plat" class="form-label">
                                        No. Plat <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="no_plat" name="no_plat"
                                        placeholder="Contoh: B 1234 ABC" value="<?= old('no_plat') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="merk_kendaraan" class="form-label">Merk Kendaraan</label>
                            <input type="text" class="form-control" id="merk_kendaraan" name="merk_kendaraan"
                                placeholder="Contoh: Honda Vario, Toyota Avanza" value="<?= old('merk_kendaraan') ?>">
                        </div>
                    </div>
                </div>

                <!-- Service Information -->
                <div class="form-card card">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>Informasi Layanan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="layanan_id" class="form-label">
                                Layanan <span class="required">*</span>
                            </label>
                            <select class="form-select" id="layanan_id" name="layanan_id" required>
                                <option value="">-- Pilih Layanan --</option>
                                <?php foreach ($layanan as $l): ?>
                                    <option value="<?= esc($l['kode_layanan']) ?>"
                                        data-harga="<?= $l['harga'] ?>"
                                        data-durasi="<?= $l['durasi_menit'] ?>"
                                        data-jenis="<?= $l['jenis_kendaraan'] ?>"
                                        <?= old('layanan_id') == $l['kode_layanan'] ? 'selected' : '' ?>>
                                        <?= esc($l['nama_layanan']) ?> - <?= ucfirst($l['jenis_kendaraan']) ?>
                                        (Rp <?= number_format($l['harga'], 0, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="layanan-info" id="layananInfo">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Harga:</strong>
                                    <div id="layananHarga" class="text-success">-</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Durasi:</strong>
                                    <div id="layananDurasi">-</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Jenis:</strong>
                                    <div id="layananJenis">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <!-- Schedule Information -->
                <div class="form-card card">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Booking
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">
                                Tanggal <span class="required">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                min="<?= date('Y-m-d') ?>" value="<?= old('tanggal') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="jam" class="form-label">
                                Jam <span class="required">*</span>
                            </label>
                            <input type="hidden" id="jam" name="jam" value="<?= old('jam') ?>">
                            <div class="time-slots" id="timeSlots">
                                <!-- Time slots will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignment & Notes -->
                <div class="form-card card">
                    <div class="card-header">
                        <i class="fas fa-cog me-2"></i>Pengaturan Lainnya
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="id_karyawan" class="form-label">Karyawan</label>
                            <select class="form-select" id="id_karyawan" name="id_karyawan">
                                <option value="">-- Auto Assign --</option>
                                <?php foreach ($karyawan as $k): ?>
                                    <option value="<?= esc($k['idkaryawan']) ?>"
                                        <?= old('id_karyawan') == $k['idkaryawan'] ? 'selected' : '' ?>>
                                        <?= esc($k['namakaryawan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Kosongkan untuk assign otomatis</small>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                placeholder="Catatan khusus untuk booking ini..."><?= old('catatan') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="form-card card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan Booking
                            </button>
                            <a href="<?= site_url('admin/booking') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const layananSelect = document.getElementById('layanan_id');
        const layananInfo = document.getElementById('layananInfo');
        const jenisKendaraanSelect = document.getElementById('jenis_kendaraan');
        const jamInput = document.getElementById('jam');
        const timeSlotsContainer = document.getElementById('timeSlots');

        // Show layanan info when selected
        layananSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {
                const harga = selectedOption.dataset.harga;
                const durasi = selectedOption.dataset.durasi;
                const jenis = selectedOption.dataset.jenis;

                document.getElementById('layananHarga').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(harga);
                document.getElementById('layananDurasi').textContent = durasi + ' menit';
                document.getElementById('layananJenis').textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);

                layananInfo.style.display = 'block';

                // Auto-select matching vehicle type
                if (jenisKendaraanSelect.value === '' || jenisKendaraanSelect.value !== jenis) {
                    jenisKendaraanSelect.value = jenis;
                }
            } else {
                layananInfo.style.display = 'none';
            }
        });

        // Generate time slots
        function generateTimeSlots() {
            const startHour = 8;
            const endHour = 17;
            const slots = [];

            for (let hour = startHour; hour < endHour; hour++) {
                slots.push(`${hour.toString().padStart(2, '0')}:00`);
                slots.push(`${hour.toString().padStart(2, '0')}:30`);
            }

            return slots;
        }

        // Render time slots
        function renderTimeSlots() {
            const slots = generateTimeSlots();
            timeSlotsContainer.innerHTML = '';

            slots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = 'time-slot';
                slotElement.textContent = slot;
                slotElement.onclick = () => selectTimeSlot(slot, slotElement);
                timeSlotsContainer.appendChild(slotElement);
            });
        }

        // Select time slot
        function selectTimeSlot(time, element) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });

            // Add selection to clicked slot
            element.classList.add('selected');
            jamInput.value = time;
        }

        // Initialize time slots
        renderTimeSlots();

        // Set selected time slot if there's old value
        const oldJam = '<?= old('jam') ?>';
        if (oldJam) {
            const targetSlot = Array.from(document.querySelectorAll('.time-slot')).find(slot => slot.textContent === oldJam);
            if (targetSlot) {
                selectTimeSlot(oldJam, targetSlot);
            }
        }

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!jamInput.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pilih Jam!',
                    text: 'Silakan pilih jam booking terlebih dahulu.',
                    icon: 'warning'
                });
                return false;
            }
        });

        // Auto uppercase no plat
        document.getElementById('no_plat').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Trigger layanan info display if there's old value
        if (layananSelect.value) {
            layananSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
<?= $this->endSection() ?>