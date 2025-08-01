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
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .time-slot {
        padding: 0.75rem 0.5rem;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background: white;
        font-size: 0.9rem;
        font-weight: 500;
        position: relative;
    }

    .time-slot:hover {
        border-color: #28a745;
        background: #f8fff9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
    }

    .time-slot.selected {
        border-color: #28a745;
        background: #28a745;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .time-slot.disabled {
        border-color: #e9ecef;
        background: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .time-slot.disabled:hover {
        transform: none;
        box-shadow: none;
        border-color: #e9ecef;
        background: #f8f9fa;
    }

    /* Simple CSS for Selected Services */
    #selectedServices,
    .selected-items {
        margin-top: 0.75rem !important;
    }

    /* Simple Table for Selected Services */
    #selectedServices .selected-services-table,
    .selected-items .selected-services-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0 !important;
        background: white !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 4px !important;
    }

    #selectedServices .selected-services-table th,
    .selected-items .selected-services-table th {
        background: #f8f9fa !important;
        padding: 0.5rem !important;
        border: 1px solid #dee2e6 !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #495057 !important;
        text-align: left !important;
    }

    #selectedServices .selected-services-table td,
    .selected-items .selected-services-table td {
        padding: 0.5rem !important;
        border: 1px solid #dee2e6 !important;
        font-size: 0.875rem !important;
        vertical-align: middle !important;
    }

    #selectedServices .selected-services-table .btn-sm,
    .selected-items .selected-services-table .btn-sm {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
        border-radius: 3px !important;
    }

    .empty-state {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 2rem 1rem;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
    }

    /* Service Selection Modal Styles */
    .service-photo {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .service-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .service-img:hover {
        border-color: #0d6efd;
        transform: scale(1.05);
    }

    .service-placeholder {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        font-size: 1.2rem;
    }

    .service-info {
        max-width: 200px;
    }

    .service-name {
        color: #2c3e50;
        font-size: 0.95rem;
        line-height: 1.3;
    }

    .service-desc {
        font-size: 0.8rem;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .price-display {
        text-align: right;
    }

    /* Modal table row hover effect */
    #layananModal .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
        cursor: pointer;
    }

    /* Checkbox styling */
    .layanan-checkbox {
        transform: scale(1.2);
        cursor: pointer;
    }

    /* Badge styling improvements */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .badge i {
        margin-right: 0.25rem;
    }

    .summary-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .summary-item {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .summary-item:last-child {
        border-bottom: none;
        font-weight: 600;
        color: #28a745;
    }

    .required {
        color: #dc3545;
    }

    .btn-select {
        background: #f8f9fa;
        border: 1px solid #ced4da;
        color: #495057;
        text-align: left;
        position: relative;
        padding-right: 40px;
    }

    .btn-select:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .btn-select::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Manual Time Slot Container CSS */
    #timeSlotContainer,
    .time-slot-container {
        max-height: 500px !important;
        overflow-y: auto !important;
        border: 2px solid #dee2e6 !important;
        border-radius: 12px !important;
        padding: 1.5rem !important;
        background: linear-gradient(135deg, #f8f9fa, #ffffff) !important;
        margin-top: 1rem !important;
    }

    /* Force Grid Layout */
    #timeSlotContainer .time-slot-grid,
    .time-slot-container .time-slot-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        gap: 1.25rem !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    /* Force Time Slot Item Styling */
    #timeSlotContainer .time-slot-item,
    .time-slot-container .time-slot-item,
    .time-slot-item {
        background: linear-gradient(135deg, #ffffff, #f8f9fa) !important;
        border: 2px solid #e9ecef !important;
        border-radius: 12px !important;
        padding: 1.5rem 1rem !important;
        text-align: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1) !important;
        min-height: 140px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        user-select: none !important;
    }

    /* Hover State */
    #timeSlotContainer .time-slot-item:hover,
    .time-slot-container .time-slot-item:hover,
    .time-slot-item:hover {
        border-color: #0d6efd !important;
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.2) !important;
        transform: translateY(-3px) scale(1.02) !important;
        background: linear-gradient(135deg, #ffffff, #e3f2fd) !important;
    }

    /* Selected State */
    #timeSlotContainer .time-slot-item.selected,
    .time-slot-container .time-slot-item.selected,
    .time-slot-item.selected {
        border-color: #0d6efd !important;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;
        color: white !important;
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4) !important;
        transform: translateY(-3px) scale(1.02) !important;
    }

    /* Disabled State */
    #timeSlotContainer .time-slot-item.disabled,
    .time-slot-container .time-slot-item.disabled,
    .time-slot-item.disabled {
        background: #f1f3f4 !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
        box-shadow: none !important;
    }

    #timeSlotContainer .time-slot-item.disabled:hover,
    .time-slot-container .time-slot-item.disabled:hover,
    .time-slot-item.disabled:hover {
        transform: none !important;
        box-shadow: none !important;
        border-color: #dee2e6 !important;
        background: #f1f3f4 !important;
    }

    /* Time Slot Elements - Manual Styling */
    #timeSlotContainer .time-slot-time,
    .time-slot-container .time-slot-time,
    .time-slot-time {
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        margin-bottom: 0.75rem !important;
        color: #2c3e50 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
    }

    #timeSlotContainer .time-slot-time::before,
    .time-slot-container .time-slot-time::before,
    .time-slot-time::before {
        content: "🕐" !important;
        font-size: 1.25rem !important;
    }

    #timeSlotContainer .time-slot-duration,
    .time-slot-container .time-slot-duration,
    .time-slot-duration {
        font-size: 0.85rem !important;
        opacity: 0.8 !important;
        margin-bottom: 0.5rem !important;
        line-height: 1.3 !important;
        color: #6c757d !important;
    }

    #timeSlotContainer .time-slot-karyawan,
    .time-slot-container .time-slot-karyawan,
    .time-slot-karyawan {
        display: inline-block !important;
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: white !important;
        padding: 0.4rem 0.8rem !important;
        border-radius: 20px !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3) !important;
        margin-top: 0.5rem !important;
    }

    /* Selected State Elements */
    #timeSlotContainer .time-slot-item.selected .time-slot-time,
    .time-slot-container .time-slot-item.selected .time-slot-time,
    .time-slot-item.selected .time-slot-time {
        color: white !important;
    }

    #timeSlotContainer .time-slot-item.selected .time-slot-duration,
    .time-slot-container .time-slot-item.selected .time-slot-duration,
    .time-slot-item.selected .time-slot-duration {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    #timeSlotContainer .time-slot-item.selected .time-slot-karyawan,
    .time-slot-container .time-slot-item.selected .time-slot-karyawan,
    .time-slot-item.selected .time-slot-karyawan {
        background: rgba(255, 255, 255, 0.25) !important;
        box-shadow: 0 3px 6px rgba(255, 255, 255, 0.2) !important;
    }

    /* Disabled State Elements */
    #timeSlotContainer .time-slot-item.disabled .time-slot-time,
    .time-slot-container .time-slot-item.disabled .time-slot-time,
    .time-slot-item.disabled .time-slot-time {
        color: #6c757d !important;
    }

    #timeSlotContainer .time-slot-item.disabled .time-slot-karyawan,
    .time-slot-container .time-slot-item.disabled .time-slot-karyawan,
    .time-slot-item.disabled .time-slot-karyawan {
        background: #6c757d !important;
        box-shadow: none !important;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .time-slot-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.5rem;
        }

        .time-slot-item {
            padding: 0.5rem;
        }

        .time-slot-time {
            font-size: 1rem;
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

    <form id="bookingForm">
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
                            <input type="hidden" id="pelanggan_id" name="pelanggan_id" value="<?= old('pelanggan_id') ?>">
                            <button type="button" class="btn btn-select form-control" data-bs-toggle="modal" data-bs-target="#pelangganModal">
                                <span id="pelanggan_text">-- Pilih Pelanggan --</span>
                            </button>
                        </div>
                        <div id="pelanggan_info" style="display: none;">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-1" id="pelanggan_nama">-</h6>
                                <small class="text-muted">
                                    <i class="fas fa-phone me-1"></i><span id="pelanggan_hp">-</span> |
                                    <i class="fas fa-map-marker-alt me-1"></i><span id="pelanggan_alamat">-</span>
                                </small>
                            </div>
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
                        <i class="fas fa-list me-2"></i>Layanan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Pilih Layanan <span class="required">*</span>
                            </label>
                            <button type="button" class="btn btn-select form-control" data-bs-toggle="modal" data-bs-target="#layananModal">
                                <span>-- Pilih Layanan --</span>
                            </button>
                        </div>
                        <div class="selected-items" id="selectedServices">
                            <div class="empty-state">Belum ada layanan dipilih</div>
                        </div>
                        <div class="summary-section" id="serviceSummary" style="display: none;">
                            <div class="summary-item">
                                <span>Total Durasi:</span>
                                <span id="totalDurasi">0 menit</span>
                            </div>
                            <div class="summary-item">
                                <span>Total Harga:</span>
                                <span id="totalHarga">Rp 0</span>
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
                            <input type="hidden" id="jam" name="jam" value="<?= old('jam') ?>" required>
                            <div class="alert alert-info mb-2" id="timeSlotInfo" style="display: none;">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>Pilih salah satu slot waktu yang tersedia</small>
                            </div>
                            <div id="timeSlotContainer" class="time-slot-container">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-alt me-2 fs-3"></i>
                                    <div class="mt-2">
                                        <strong>Pilih Tanggal dan Layanan</strong>
                                        <br><small>untuk melihat slot waktu yang tersedia</small>
                                    </div>
                                </div>
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
                        <!-- Karyawan akan di-assign otomatis oleh sistem -->
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

<!-- Pelanggan Modal -->
<div class="modal fade" id="pelangganModal" tabindex="-1" aria-labelledby="pelangganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pelangganModalLabel">
                    <i class="fas fa-users me-2"></i>Pilih Pelanggan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchPelanggan" placeholder="Cari nama atau no HP...">
                </div>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pelangganList">
                            <?php foreach ($pelanggan as $p): ?>
                                <tr data-nama="<?= esc(strtolower($p['nama_pelanggan'])) ?>"
                                    data-hp="<?= esc(strtolower($p['no_hp'] ?? '')) ?>">
                                    <td><?= esc($p['nama_pelanggan']) ?></td>
                                    <td><?= esc($p['no_hp'] ?? '-') ?></td>
                                    <td><?= esc($p['alamat'] ?? '-') ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            onclick="selectPelanggan('<?= esc($p['kode_pelanggan']) ?>', '<?= esc($p['nama_pelanggan']) ?>', '<?= esc($p['no_hp'] ?? '') ?>', '<?= esc($p['alamat'] ?? '') ?>')">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Layanan Modal -->
<div class="modal fade" id="layananModal" tabindex="-1" aria-labelledby="layananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="layananModalLabel">
                    <i class="fas fa-list-check me-2"></i>Pilih Layanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchLayanan" placeholder="Cari nama layanan...">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterJenis">
                                <option value="">Semua Jenis</option>
                                <option value="motor">Motor</option>
                                <option value="mobil">Mobil</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50px">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th width="80px">Foto</th>
                                <th>Layanan</th>
                                <th width="100px">Jenis</th>
                                <th width="100px">Durasi</th>
                                <th width="120px">Harga</th>
                            </tr>
                        </thead>
                        <tbody id="layananList">
                            <?php foreach ($layanan as $l): ?>
                                <tr data-nama="<?= esc(strtolower($l['nama_layanan'])) ?>"
                                    data-jenis="<?= esc($l['jenis_kendaraan']) ?>">
                                    <td>
                                        <input type="checkbox" class="form-check-input layanan-checkbox"
                                            value="<?= esc($l['kode_layanan']) ?>"
                                            data-nama="<?= esc($l['nama_layanan']) ?>"
                                            data-harga="<?= $l['harga'] ?>"
                                            data-durasi="<?= $l['durasi_menit'] ?>"
                                            data-jenis="<?= $l['jenis_kendaraan'] ?>">
                                    </td>
                                    <td>
                                        <div class="service-photo">
                                            <?php if (!empty($l['foto'])): ?>
                                                <img src="<?= base_url('uploads/layanan/' . $l['foto']) ?>"
                                                    alt="<?= esc($l['nama_layanan']) ?>"
                                                    class="img-thumbnail service-img">
                                            <?php else: ?>
                                                <div class="service-placeholder">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="service-info">
                                            <strong class="service-name"><?= esc($l['nama_layanan']) ?></strong>
                                            <?php if (!empty($l['deskripsi'])): ?>
                                                <br><small class="text-muted service-desc"><?= esc($l['deskripsi']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $l['jenis_kendaraan'] === 'motor' ? 'info' : ($l['jenis_kendaraan'] === 'mobil' ? 'primary' : 'secondary') ?>">
                                            <i class="fas fa-<?= $l['jenis_kendaraan'] === 'motor' ? 'motorcycle' : ($l['jenis_kendaraan'] === 'mobil' ? 'car' : 'cog') ?>"></i>
                                            <?= ucfirst($l['jenis_kendaraan']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-clock"></i> <?= $l['durasi_menit'] ?>m
                                        </span>
                                    </td>
                                    <td>
                                        <div class="price-display">
                                            <span class="fw-bold text-success">
                                                Rp <?= number_format($l['harga'], 0, ',', '.') ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="applySelectedServices()">Terapkan Pilihan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let selectedServices = [];

    // Define global functions first
    window.showEmptyTimeSlots = function(message) {
        const container = document.getElementById('timeSlotContainer');
        if (!container) return;

        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-calendar-times me-2 fs-3"></i>
                <div class="mt-2">
                    <strong>${message}</strong>
                </div>
            </div>
        `;
        const timeSlotInfo = document.getElementById('timeSlotInfo');
        if (timeSlotInfo) timeSlotInfo.style.display = 'none';
    }

    // Load available time slots - hybrid approach
    function loadAvailableSlots() {
        const tanggal = document.getElementById('tanggal').value;

        if (!tanggal || selectedServices.length === 0) {
            showEmptyTimeSlots('Pilih tanggal dan layanan untuk melihat slot waktu');
            return;
        }

        // Show loading
        showLoadingTimeSlots();

        // Fetch available slots using EXACT same logic as pelanggan
        // Use jenis_kendaraan from selected services if available
        const jenisKendaraan = selectedServices.length > 0 ? selectedServices[0].jenis_kendaraan : 'mobil';

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
                    if (data.data && data.data.length > 0) {
                        // Now we have both slots and karyawan data in one response
                        renderSimpleTimeSlots(data.data, data);
                    } else {
                        showEmptyTimeSlots('Tidak ada slot tersedia untuk tanggal ini');
                    }
                } else {
                    showEmptyTimeSlots(data.message || 'Gagal memuat slot waktu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showEmptyTimeSlots('Terjadi kesalahan saat memuat slot waktu');
            });
    }



    // Generate simple slots from complex data
    function generateSimpleSlots(data) {
        const startHour = 8;
        const endHour = 17;
        const existingBookings = data.existing_bookings || [];
        const totalKaryawan = data.total_karyawan || 1;
        const isToday = data.is_today || false;
        const currentTime = data.current_time || null;

        // Generate 30-minute slots
        const allSlots = [];
        for (let hour = startHour; hour < endHour; hour++) {
            allSlots.push(`${hour.toString().padStart(2, '0')}:00`);
            allSlots.push(`${hour.toString().padStart(2, '0')}:30`);
        }

        // Filter available slots
        const availableSlots = allSlots.filter(slot => {
            // Skip past times for today
            if (isToday && currentTime && slot <= currentTime) {
                return false;
            }

            // Count busy employees at this time
            const busyKaryawanSet = new Set();
            existingBookings.forEach(booking => {
                const bookingStart = booking.jam;
                const bookingEnd = addMinutesToTime(bookingStart, booking.durasi || 60);

                if (timesOverlap(slot, addMinutesToTime(slot, 60), bookingStart, bookingEnd)) {
                    busyKaryawanSet.add(booking.id_karyawan);
                }
            });

            const availableKaryawan = totalKaryawan - busyKaryawanSet.size;
            return availableKaryawan > 0;
        });

        return availableSlots;
    }

    // Helper function to add minutes to time
    function addMinutesToTime(timeStr, minutes) {
        const [hours, mins] = timeStr.split(':').map(Number);
        const date = new Date();
        date.setHours(hours, mins, 0, 0);
        date.setMinutes(date.getMinutes() + minutes);
        return date.getHours().toString().padStart(2, '0') + ':' +
            date.getMinutes().toString().padStart(2, '0');
    }

    // Helper function to check if times overlap
    function timesOverlap(start1, end1, start2, end2) {
        return start1 < end2 && end1 > start2;
    }

    // Get available karyawan count for a specific slot
    function getAvailableKaryawanCount(slot, data) {
        if (!data || !data.existing_bookings || !data.total_karyawan) {
            return 'Tersedia'; // Fallback when no detailed data
        }

        const existingBookings = data.existing_bookings || [];
        const totalKaryawan = parseInt(data.total_karyawan) || 1;

        // Debug: console.log(`Calculating for slot ${slot}:`, { totalKaryawan, existingBookingsCount: existingBookings.length });

        // Count busy employees at this time
        const busyKaryawanSet = new Set();
        existingBookings.forEach(booking => {
            if (!booking.jam || !booking.id_karyawan) {
                // Debug: console.log('Skipping invalid booking:', booking);
                return;
            }

            const bookingStart = booking.jam;
            const bookingEnd = addMinutesToTime(bookingStart, parseInt(booking.durasi) || 60);
            const slotEnd = addMinutesToTime(slot, 60);

            if (timesOverlap(slot, slotEnd, bookingStart, bookingEnd)) {
                busyKaryawanSet.add(booking.id_karyawan);
                // Debug: console.log(`Slot ${slot} conflicts with booking at ${bookingStart} for karyawan ${booking.id_karyawan}`);
            }
        });

        const availableCount = totalKaryawan - busyKaryawanSet.size;
        // Debug: console.log(`Slot ${slot} result: ${busyKaryawanSet.size} busy, ${availableCount} available`);

        return availableCount + ' Karyawan';
    }

    // Render simple time slots - like pelanggan but with grid display
    function renderSimpleTimeSlots(slots, data = null) {
        const container = document.getElementById('timeSlotContainer');
        const timeSlotInfo = document.getElementById('timeSlotInfo');

        if (timeSlotInfo) timeSlotInfo.style.display = 'block';

        // Clear container
        container.innerHTML = '';

        // Create grid
        const gridDiv = document.createElement('div');
        gridDiv.className = 'time-slot-grid';
        gridDiv.style.cssText = `
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
            gap: 1rem !important;
            margin: 0 !important;
        `;

        // Add each slot
        slots.forEach(slot => {
            const slotDiv = document.createElement('div');
            slotDiv.className = 'time-slot-item';
            slotDiv.style.cssText = `
                background: linear-gradient(135deg, #ffffff, #f8f9fa) !important;
                border: 2px solid #e9ecef !important;
                border-radius: 8px !important;
                padding: 1rem !important;
                cursor: pointer !important;
                transition: all 0.2s ease !important;
                text-align: center !important;
                min-height: 80px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1) !important;
            `;

            // Get available karyawan count for this slot
            const availableCount = getAvailableKaryawanCount(slot, data);

            slotDiv.innerHTML = `
                <div class="time-slot-time" style="font-weight: 600; font-size: 1rem; color: #2c3e50;">
                    🕐 ${slot}
                </div>
                <div class="time-slot-karyawan" style="font-size: 0.8rem; color: #6c757d; margin-top: 0.5rem; background: linear-gradient(135deg, #28a745, #20c997); padding: 0.25rem 0.5rem; border-radius: 4px; color: white; box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);">
                    ${availableCount}
                </div>
            `;

            // Add click handler
            slotDiv.addEventListener('click', function() {
                selectTimeSlot(slot, this);
            });

            // Add hover effect
            slotDiv.addEventListener('mouseenter', function() {
                if (!this.classList.contains('selected')) {
                    this.style.borderColor = '#0088cc';
                    this.style.background = 'linear-gradient(135deg, #f0f8ff, #e6f3ff)';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 136, 204, 0.15)';
                }
            });

            slotDiv.addEventListener('mouseleave', function() {
                if (!this.classList.contains('selected')) {
                    this.style.borderColor = '#e9ecef';
                    this.style.background = 'linear-gradient(135deg, #ffffff, #f8f9fa)';
                    this.style.transform = 'none';
                    this.style.boxShadow = '0 3px 8px rgba(0, 0, 0, 0.1)';
                }
            });

            gridDiv.appendChild(slotDiv);
        });

        container.appendChild(gridDiv);
    }

    function showLoadingTimeSlots() {
        const container = document.getElementById('timeSlotContainer');
        if (!container) return;

        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-spinner fa-spin me-2 fs-3"></i>
                <div class="mt-2">
                    <strong>Memuat Slot Waktu...</strong>
                    <br><small>Sedang mengecek ketersediaan karyawan</small>
                </div>
            </div>
        `;
    }

    // Generate time slot grid
    function generateTimeSlotGrid(data, totalDurasi) {
        const startHour = 8;
        const endHour = 17;
        const existingBookings = data.existing_bookings || [];
        const totalKaryawan = data.total_karyawan || 1;
        const isToday = data.is_today || false;
        const currentTime = data.current_time || null;

        // Generate 30-minute slots
        const allSlots = [];
        for (let hour = startHour; hour < endHour; hour++) {
            allSlots.push(`${hour.toString().padStart(2, '0')}:00`);
            allSlots.push(`${hour.toString().padStart(2, '0')}:30`);
        }

        // Calculate available karyawan for each slot
        const slotsWithAvailability = allSlots.map(slot => {
            // Skip past times for today
            if (isToday && currentTime && slot <= currentTime) {
                return null;
            }

            const slotEndTime = addMinutesToTime(slot, totalDurasi);

            // Count busy employees at this time
            const busyKaryawanSet = new Set();
            existingBookings.forEach(booking => {
                const bookingStart = booking.jam;
                const bookingEnd = addMinutesToTime(bookingStart, booking.durasi || 60);

                if (timesOverlap(slot, slotEndTime, bookingStart, bookingEnd)) {
                    if (booking.id_karyawan) {
                        busyKaryawanSet.add(booking.id_karyawan);
                    }
                }
            });

            const availableKaryawan = totalKaryawan - busyKaryawanSet.size;

            return {
                time: slot,
                endTime: slotEndTime,
                availableKaryawan: availableKaryawan,
                isAvailable: availableKaryawan > 0
            };
        }).filter(slot => slot !== null);

        renderTimeSlotGrid(slotsWithAvailability, totalDurasi);
    }

    // Render time slot grid - Manual Implementation
    window.renderTimeSlotGrid = function(slots, totalDurasi) {
        const container = document.getElementById('timeSlotContainer');
        const timeSlotInfo = document.getElementById('timeSlotInfo');

        console.log('renderTimeSlotGrid called with:', {
            slots: slots.length,
            totalDurasi
        });

        if (timeSlotInfo) timeSlotInfo.style.display = 'block';

        if (slots.length === 0) {
            showEmptyTimeSlots('Tidak ada slot waktu tersedia untuk hari ini');
            return;
        }

        // Force clear any existing content
        container.innerHTML = '';

        // Create grid container
        const gridDiv = document.createElement('div');
        gridDiv.className = 'time-slot-grid';
        gridDiv.style.cssText = `
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
            gap: 1.25rem !important;
            margin: 0 !important;
            padding: 0 !important;
        `;

        // Create each slot
        slots.forEach(slot => {
            const slotDiv = document.createElement('div');
            slotDiv.className = `time-slot-item ${slot.isAvailable ? '' : 'disabled'}`;
            slotDiv.setAttribute('data-time', slot.time);

            // Manual styling
            slotDiv.style.cssText = `
                background: linear-gradient(135deg, #ffffff, #f8f9fa) !important;
                border: 2px solid #e9ecef !important;
                border-radius: 12px !important;
                padding: 1.5rem 1rem !important;
                text-align: center !important;
                cursor: ${slot.isAvailable ? 'pointer' : 'not-allowed'} !important;
                transition: all 0.3s ease !important;
                position: relative !important;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1) !important;
                min-height: 140px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                user-select: none !important;
            `;

            if (slot.isAvailable) {
                slotDiv.onclick = function() {
                    selectTimeSlot(slot.time, this);
                };
                slotDiv.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                    this.style.boxShadow = '0 6px 16px rgba(13, 110, 253, 0.2)';
                    this.style.borderColor = '#0d6efd';
                });
                slotDiv.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('selected')) {
                        this.style.transform = 'none';
                        this.style.boxShadow = '0 3px 8px rgba(0, 0, 0, 0.1)';
                        this.style.borderColor = '#e9ecef';
                    }
                });
            }

            slotDiv.innerHTML = `
                <div class="time-slot-time" style="
                    font-size: 1.5rem !important;
                    font-weight: 800 !important;
                    margin-bottom: 0.75rem !important;
                    color: #2c3e50 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    gap: 0.5rem !important;
                ">🕐 ${slot.time}</div>
                <div class="time-slot-duration" style="
                    font-size: 0.85rem !important;
                    opacity: 0.8 !important;
                    margin-bottom: 0.5rem !important;
                    line-height: 1.3 !important;
                    color: #6c757d !important;
                ">sampai ${slot.endTime}</div>
                <div class="time-slot-duration" style="
                    font-size: 0.85rem !important;
                    opacity: 0.8 !important;
                    margin-bottom: 0.5rem !important;
                    line-height: 1.3 !important;
                    color: #6c757d !important;
                ">${totalDurasi} menit</div>
                <div class="time-slot-karyawan" style="
                    display: inline-block !important;
                    background: linear-gradient(135deg, #28a745, #20c997) !important;
                    color: white !important;
                    padding: 0.4rem 0.8rem !important;
                    border-radius: 20px !important;
                    font-size: 0.8rem !important;
                    font-weight: 700 !important;
                    box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3) !important;
                    margin-top: 0.5rem !important;
                ">${slot.availableKaryawan} Karyawan</div>
            `;

            gridDiv.appendChild(slotDiv);
        });

        container.appendChild(gridDiv);
        console.log('Time slot grid rendered successfully');
    }

    // Helper functions
    function addMinutesToTime(timeStr, minutes) {
        const [hours, mins] = timeStr.split(':').map(Number);
        const totalMinutes = hours * 60 + mins + minutes;
        const newHours = Math.floor(totalMinutes / 60);
        const newMins = totalMinutes % 60;
        return `${newHours.toString().padStart(2, '0')}:${newMins.toString().padStart(2, '0')}`;
    }

    function timesOverlap(start1, end1, start2, end2) {
        return start1 < end2 && end1 > start2;
    }

    // Select time slot - Manual Implementation
    window.selectTimeSlot = function(time, element) {
        if (element.classList.contains('disabled')) {
            return;
        }

        console.log('selectTimeSlot called:', time);

        // Remove previous selection
        document.querySelectorAll('.time-slot-item').forEach(slot => {
            slot.classList.remove('selected');
            // Reset to default styling
            slot.style.background = 'linear-gradient(135deg, #ffffff, #f8f9fa)';
            slot.style.borderColor = '#e9ecef';
            slot.style.color = 'inherit';
            slot.style.transform = 'none';
            slot.style.boxShadow = '0 3px 8px rgba(0, 0, 0, 0.1)';

            // Reset child element colors
            const timeEl = slot.querySelector('.time-slot-time');
            const durationEls = slot.querySelectorAll('.time-slot-duration');
            const karyawanEl = slot.querySelector('.time-slot-karyawan');

            if (timeEl) timeEl.style.color = '#2c3e50';
            durationEls.forEach(el => el.style.color = '#6c757d');
            if (karyawanEl) {
                karyawanEl.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
                karyawanEl.style.boxShadow = '0 3px 6px rgba(40, 167, 69, 0.3)';
            }
        });

        // Add selection to clicked slot
        element.classList.add('selected');

        // Apply selected styling manually
        element.style.background = 'linear-gradient(135deg, #0d6efd, #0b5ed7)';
        element.style.borderColor = '#0d6efd';
        element.style.color = 'white';
        element.style.transform = 'translateY(-3px) scale(1.02)';
        element.style.boxShadow = '0 8px 20px rgba(13, 110, 253, 0.4)';

        // Update child element colors for selected state
        const timeEl = element.querySelector('.time-slot-time');
        const durationEls = element.querySelectorAll('.time-slot-duration');
        const karyawanEl = element.querySelector('.time-slot-karyawan');

        if (timeEl) timeEl.style.color = 'white';
        durationEls.forEach(el => el.style.color = 'rgba(255, 255, 255, 0.9)');
        if (karyawanEl) {
            karyawanEl.style.background = 'rgba(255, 255, 255, 0.25)';
            karyawanEl.style.boxShadow = '0 3px 6px rgba(255, 255, 255, 0.2)';
        }

        // Set input value
        const jamInput = document.getElementById('jam');
        if (jamInput) jamInput.value = time;

        console.log('Time slot selected successfully:', time);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const jenisKendaraanSelect = document.getElementById('jenis_kendaraan');
        const jamInput = document.getElementById('jam');
        const timeSlotsContainer = document.getElementById('timeSlots');
        const tanggalInput = document.getElementById('tanggal');

        // Pelanggan search
        document.getElementById('searchPelanggan').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('#pelangganList tr');

            rows.forEach(row => {
                const nama = row.dataset.nama || '';
                const hp = row.dataset.hp || '';
                const visible = nama.includes(search) || hp.includes(search);
                row.style.display = visible ? '' : 'none';
            });
        });

        // Layanan search and filter
        document.getElementById('searchLayanan').addEventListener('input', filterLayanan);
        document.getElementById('filterJenis').addEventListener('change', filterLayanan);

        // Select all layanan
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.layanan-checkbox:not([style*="display: none"])');
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                if (row.style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
        });

        // Load time slots when date changes
        tanggalInput.addEventListener('change', function() {
            if (this.value && selectedServices.length > 0) {
                loadAvailableSlots();
            } else if (this.value) {
                // Show empty state if no services selected yet
                showEmptyTimeSlots('Pilih layanan untuk melihat slot waktu');
            }
        });

        // Initialize empty time slots on page load
        if (selectedServices.length > 0 && tanggalInput.value) {
            loadAvailableSlots();
        } else {
            showEmptyTimeSlots('Pilih tanggal dan layanan untuk melihat slot waktu');
        }

        // Make table rows clickable in service modal
        document.querySelectorAll('#layananModal tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.type !== 'checkbox') {
                    const checkbox = this.querySelector('.layanan-checkbox');
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        });

        // AJAX Form Submit
        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            if (!$('#pelanggan_id').val()) {
                Swal.fire('Error', 'Pilih pelanggan terlebih dahulu', 'error');
                return;
            }

            if (selectedServices.length === 0) {
                Swal.fire('Error', 'Pilih minimal satu layanan', 'error');
                return;
            }

            if (!$('#tanggal').val()) {
                Swal.fire('Error', 'Pilih tanggal booking', 'error');
                return;
            }

            if (!$('#jam').val()) {
                Swal.fire('Error', 'Pilih jam booking', 'error');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            formData.append('pelanggan_id', $('#pelanggan_id').val());
            formData.append('tanggal', $('#tanggal').val());
            formData.append('jam', $('#jam').val());
            formData.append('no_plat', $('#no_plat').val());
            formData.append('jenis_kendaraan', $('#jenis_kendaraan').val());
            formData.append('merk_kendaraan', $('#merk_kendaraan').val());
            formData.append('catatan', $('#catatan').val());

            // Add selected services
            selectedServices.forEach((service, index) => {
                formData.append(`layanan_ids[${index}]`, service.kode);
            });

            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang memproses booking',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit via AJAX
            $.ajax({
                url: '<?= site_url('admin/booking/store') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '<?= site_url('admin/booking') ?>';
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan sistem';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            errorMessage = 'Terjadi kesalahan: ' + xhr.status;
                        }
                    }

                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        });







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
            if (!document.getElementById('pelanggan_id').value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pilih Pelanggan!',
                    text: 'Silakan pilih pelanggan terlebih dahulu.',
                    icon: 'warning'
                });
                return false;
            }

            if (selectedServices.length === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pilih Layanan!',
                    text: 'Silakan pilih minimal satu layanan.',
                    icon: 'warning'
                });
                return false;
            }

            if (!jamInput.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Pilih Jam!',
                    text: 'Silakan pilih jam booking terlebih dahulu.',
                    icon: 'warning'
                });
                return false;
            }

            // Add selected services to form
            selectedServices.forEach((service, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `layanan_ids[${index}]`;
                input.value = service.kode;
                this.appendChild(input);
            });
        });

        // Auto uppercase no plat
        document.getElementById('no_plat').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    function filterLayanan() {
        const search = document.getElementById('searchLayanan').value.toLowerCase();
        const jenis = document.getElementById('filterJenis').value;
        const rows = document.querySelectorAll('#layananList tr');

        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const jenisRow = row.dataset.jenis || '';
            const matchSearch = nama.includes(search);
            const matchJenis = !jenis || jenisRow === jenis;
            const visible = matchSearch && matchJenis;
            row.style.display = visible ? '' : 'none';
        });
    }

    function selectPelanggan(kode, nama, hp, alamat) {
        document.getElementById('pelanggan_id').value = kode;
        document.getElementById('pelanggan_text').textContent = nama;

        document.getElementById('pelanggan_nama').textContent = nama;
        document.getElementById('pelanggan_hp').textContent = hp || '-';
        document.getElementById('pelanggan_alamat').textContent = alamat || '-';
        document.getElementById('pelanggan_info').style.display = 'block';

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('pelangganModal'));
        modal.hide();
    }

    function applySelectedServices() {
        const checkboxes = document.querySelectorAll('.layanan-checkbox:checked');
        selectedServices = [];

        checkboxes.forEach(cb => {
            selectedServices.push({
                kode: cb.value,
                nama: cb.dataset.nama,
                harga: parseInt(cb.dataset.harga),
                durasi: parseInt(cb.dataset.durasi),
                jenis: cb.dataset.jenis
            });
        });

        updateSelectedServicesDisplay();

        // Close modal and fix accessibility
        const modalElement = document.getElementById('layananModal');
        const modal = bootstrap.Modal.getInstance(modalElement);

        // Remove aria-hidden before hiding to fix accessibility warning
        modalElement.addEventListener('hide.bs.modal', function() {
            modalElement.removeAttribute('aria-hidden');
        });

        modalElement.addEventListener('hidden.bs.modal', function() {
            modalElement.setAttribute('aria-hidden', 'true');
        });

        modal.hide();
    }

    function updateSelectedServicesDisplay() {
        const container = document.getElementById('selectedServices');
        const summary = document.getElementById('serviceSummary');

        if (selectedServices.length === 0) {
            container.innerHTML = '<div class="empty-state">Belum ada layanan dipilih</div>';
            summary.style.display = 'none';
            // Reset time slots when no services
            showEmptyTimeSlots('Pilih layanan untuk melihat slot waktu');
            return;
        }

        // Display selected services as simple table
        if (selectedServices.length === 0) {
            container.innerHTML = '<div class="empty-state">Belum ada layanan dipilih</div>';
            return;
        }

        const table = document.createElement('table');
        table.className = 'selected-services-table table table-sm';

        // Table header
        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr>
                <th>Layanan</th>
                <th width="80px">Durasi</th>
                <th width="120px">Harga</th>
                <th width="60px">Aksi</th>
            </tr>
        `;
        table.appendChild(thead);

        // Table body
        const tbody = document.createElement('tbody');
        selectedServices.forEach(service => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${service.nama}</td>
                <td>${service.durasi} menit</td>
                <td>Rp ${new Intl.NumberFormat('id-ID').format(service.harga)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeService('${service.kode}')" title="Hapus layanan">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
        table.appendChild(tbody);

        container.innerHTML = '';
        container.appendChild(table);

        // Calculate totals
        const totalDurasi = selectedServices.reduce((sum, service) => sum + service.durasi, 0);
        const totalHarga = selectedServices.reduce((sum, service) => sum + service.harga, 0);

        document.getElementById('totalDurasi').textContent = totalDurasi + ' menit';
        document.getElementById('totalHarga').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga);

        summary.style.display = 'block';

        // Reload time slots when services change
        const tanggal = document.getElementById('tanggal').value;
        if (tanggal) {
            loadAvailableSlots();
        }
    }

    function removeService(kode) {
        selectedServices = selectedServices.filter(service => service.kode !== kode);

        // Uncheck the checkbox
        const checkbox = document.querySelector(`.layanan-checkbox[value="${kode}"]`);
        if (checkbox) checkbox.checked = false;

        updateSelectedServicesDisplay();
    }

    // Initialize with old values if any
    <?php if (old('pelanggan_id')): ?>
        // Find and select the pelanggan
        <?php
        $selectedPelanggan = null;
        foreach ($pelanggan as $p) {
            if ($p['kode_pelanggan'] == old('pelanggan_id')) {
                $selectedPelanggan = $p;
                break;
            }
        }
        ?>
        <?php if ($selectedPelanggan): ?>
            selectPelanggan(
                '<?= esc($selectedPelanggan['kode_pelanggan']) ?>',
                '<?= esc($selectedPelanggan['nama_pelanggan']) ?>',
                '<?= esc($selectedPelanggan['no_hp'] ?? '') ?>',
                '<?= esc($selectedPelanggan['alamat'] ?? '') ?>'
            );
        <?php endif; ?>
    <?php endif; ?>
</script>
<?= $this->endSection() ?>