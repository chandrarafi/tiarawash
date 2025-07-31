<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .form-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border-radius: 15px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .form-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .form-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
    }

    .btn-secondary {
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
    }

    .alert {
        border-radius: 10px;
        border: none;
        padding: 1rem 1.5rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-menunggu {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-dikonfirmasi {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-diproses {
        background-color: #d4edda;
        color: #155724;
    }

    .status-selesai {
        background-color: #cce7ff;
        color: #004085;
    }

    .status-batal {
        background-color: #f8d7da;
        color: #721c24;
    }

    .time-slots {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .time-slot {
        padding: 0.5rem;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .time-slot:hover {
        border-color: #17a2b8;
        background: #f8f9ff;
    }

    .time-slot.selected {
        border-color: #17a2b8;
        background: #17a2b8;
        color: white;
    }

    .required {
        color: #dc3545;
    }

    .current-info {
        background: #e7f3ff;
        border-left: 4px solid #007bff;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
        margin-bottom: 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="form-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="fas fa-edit me-2"></i>
                    Edit Booking
                </h2>
                <p class="mb-0 opacity-75">
                    Ubah data booking dan jadwal
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= site_url('admin/booking') ?>" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
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

    <form action="<?= site_url('admin/booking/update/' . $booking['id']) ?>" method="POST" id="bookingForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Current Booking Info -->
                <div class="current-info">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Kode Booking:</strong> <?= esc($booking['kode_booking']) ?><br>
                            <strong>Status Saat Ini:</strong>
                            <span class="status-badge status-<?= $booking['status'] ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Dibuat:</strong> <?= date('d M Y H:i', strtotime($booking['created_at'])) ?><br>
                            <strong>Terakhir Update:</strong> <?= date('d M Y H:i', strtotime($booking['updated_at'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>Informasi Pelanggan
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="pelanggan_id" class="form-label">
                                        Pelanggan <span class="required">*</span>
                                    </label>
                                    <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                                        <option value="">-- Pilih Pelanggan --</option>
                                        <?php foreach ($pelanggan as $p): ?>
                                            <option value="<?= esc($p['kode_pelanggan']) ?>"
                                                <?= $booking['pelanggan_id'] == $p['kode_pelanggan'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_pelanggan']) ?> - <?= esc($p['no_hp'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="form-card">
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
                                        <option value="motor" <?= $booking['jenis_kendaraan'] == 'motor' ? 'selected' : '' ?>>Motor</option>
                                        <option value="mobil" <?= $booking['jenis_kendaraan'] == 'mobil' ? 'selected' : '' ?>>Mobil</option>
                                        <option value="lainnya" <?= $booking['jenis_kendaraan'] == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_plat" class="form-label">
                                        No. Plat <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="no_plat" name="no_plat"
                                        placeholder="Contoh: B 1234 ABC" value="<?= esc($booking['no_plat']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="merk_kendaraan" class="form-label">Merk Kendaraan</label>
                                    <input type="text" class="form-control" id="merk_kendaraan" name="merk_kendaraan"
                                        placeholder="Contoh: Honda Vario, Toyota Avanza" value="<?= esc($booking['merk_kendaraan'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Information -->
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>Informasi Layanan
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
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
                                                <?= $booking['layanan_id'] == $l['kode_layanan'] ? 'selected' : '' ?>>
                                                <?= esc($l['nama_layanan']) ?> - <?= ucfirst($l['jenis_kendaraan']) ?>
                                                (Rp <?= number_format($l['harga'], 0, ',', '.') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Schedule Information -->
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Booking
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">
                                Tanggal <span class="required">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                value="<?= esc($booking['tanggal']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="jam" class="form-label">
                                Jam <span class="required">*</span>
                            </label>
                            <input type="hidden" id="jam" name="jam" value="<?= esc($booking['jam']) ?>">
                            <div class="time-slots" id="timeSlots">
                                <!-- Time slots will be generated by JavaScript -->
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="id_karyawan" class="form-label">Karyawan</label>
                            <select class="form-select" id="id_karyawan" name="id_karyawan">
                                <option value="">-- Auto Assign --</option>
                                <?php foreach ($karyawan as $k): ?>
                                    <option value="<?= esc($k['idkaryawan']) ?>"
                                        <?= $booking['id_karyawan'] == $k['idkaryawan'] ? 'selected' : '' ?>>
                                        <?= esc($k['namakaryawan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Status & Additional Information -->
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-cog me-2"></i>Status & Catatan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                Status <span class="required">*</span>
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="menunggu" <?= $booking['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="dikonfirmasi" <?= $booking['status'] == 'dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                <option value="diproses" <?= $booking['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="selesai" <?= $booking['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="batal" <?= $booking['status'] == 'batal' ? 'selected' : '' ?>>Batal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="4"
                                placeholder="Catatan khusus untuk booking ini..."><?= esc($booking['catatan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="form-card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Booking
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
        const jamInput = document.getElementById('jam');
        const timeSlotsContainer = document.getElementById('timeSlots');

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

            // Set current selected time
            const currentJam = jamInput.value;
            if (currentJam) {
                const targetSlot = Array.from(document.querySelectorAll('.time-slot')).find(slot => slot.textContent === currentJam);
                if (targetSlot) {
                    selectTimeSlot(currentJam, targetSlot);
                }
            }
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

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!jamInput.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pilih Jam!',
                    text: 'Silakan pilih jam booking terlebih dahulu.',
                    icon: 'warning'
                });
            }
        });

        // Auto uppercase no plat
        document.getElementById('no_plat').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Confirmation for status changes
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const currentStatus = '<?= $booking['status'] ?>';

            if (status !== currentStatus && (status === 'batal' || status === 'selesai')) {
                Swal.fire({
                    title: 'Konfirmasi Perubahan Status',
                    text: `Apakah Anda yakin ingin mengubah status ke "${status.charAt(0).toUpperCase() + status.slice(1)}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        this.value = currentStatus;
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>