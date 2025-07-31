<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Booking Layanan - TiaraWash' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0088cc;
            --secondary-color: #00aaff;
            --accent-color: #ff6b35;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fc;
            --gradient-primary: linear-gradient(135deg, #0088cc, #00aaff);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .booking-container {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }

        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .booking-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .booking-header h2 {
            margin: 0;
            font-weight: 700;
        }

        .booking-body {
            padding: 2rem;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 1rem;
            position: relative;
            font-weight: 600;
        }

        .step.active {
            background: var(--primary-color);
            color: white;
        }

        .step.completed {
            background: #28a745;
            color: white;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 2rem;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
        }

        .step.completed:not(:last-child)::after {
            background: #28a745;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .form-section h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .form-section h5 i {
            margin-right: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 136, 204, 0.25);
        }

        /* Time Slot Styles */
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 1rem;
        }

        .time-slot {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .time-slot:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 136, 204, 0.15);
        }

        .time-slot.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color), #1976d2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 136, 204, 0.3);
        }

        .time-slot.unavailable {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .time-slot.unavailable:hover {
            transform: none;
            box-shadow: none;
        }

        .time-slot-time {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .time-slot-info {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .time-slot-duration {
            font-size: 0.8rem;
            margin-top: 4px;
            opacity: 0.7;
        }

        .time-slot.selected .time-slot-info,
        .time-slot.selected .time-slot-duration {
            opacity: 0.9;
        }

        .time-slot-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #28a745;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
        }

        .time-slot.unavailable .time-slot-badge {
            background: #dc3545;
        }

        @media (max-width: 768px) {
            .time-slots-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 8px;
            }

            .time-slot {
                padding: 12px;
            }
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 136, 204, 0.3);
        }

        .service-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .service-card:hover,
        .service-card.selected {
            border-color: var(--primary-color);
            background: rgba(0, 136, 204, 0.05);
        }

        .service-card.selected {
            box-shadow: 0 0 0 0.2rem rgba(0, 136, 204, 0.25);
        }

        .service-image {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .service-placeholder {
            width: 100%;
            height: 80px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 1.5rem;
            border: 2px dashed #dee2e6;
        }

        .guest-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <i class="fas fa-car-wash me-2"></i>TiaraWash
            </a>

            <div class="ms-auto">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= site_url('pelanggan/dashboard') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('auth') ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="<?= site_url('auth/register') ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Booking Form -->
    <div class="booking-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="booking-card">
                        <div class="booking-header">
                            <h2>
                                <i class="fas fa-calendar-plus me-2"></i>
                                Booking Layanan Cuci Kendaraan
                            </h2>
                            <p class="mb-0 opacity-75">Pilih layanan dan jadwal yang Anda inginkan</p>
                        </div>

                        <div class="booking-body">
                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <div class="step active" id="step1">1</div>
                                <div class="step" id="step2">2</div>
                                <div class="step" id="step3">3</div>
                            </div>

                            <!-- Guest Info Alert (if not logged in) -->
                            <?php if (!$isLoggedIn): ?>
                                <div class="guest-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-warning me-2"></i>
                                        <div>
                                            <strong>Booking sebagai Tamu</strong>
                                            <p class="mb-0">Anda dapat melakukan booking tanpa registrasi, namun untuk fitur lengkap seperti tracking antrian dan riwayat, silakan <a href="<?= site_url('auth/register') ?>">daftar akun</a>.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Booking Form -->
                            <form id="bookingForm">
                                <!-- Step 1: Pilih Layanan -->
                                <div class="form-step active" id="formStep1">
                                    <div class="form-section">
                                        <h5><i class="fas fa-cogs"></i>Pilih Layanan</h5>
                                        <p class="text-muted small mb-3">Anda dapat memilih lebih dari satu layanan. Total durasi akan dihitung otomatis.</p>

                                        <div class="row">
                                            <?php if (!empty($grouped_services)): ?>
                                                <?php foreach ($grouped_services as $jenis => $services): ?>
                                                    <div class="col-12 mb-3">
                                                        <h6 class="text-capitalize text-primary"><?= esc($jenis) ?></h6>
                                                        <?php foreach ($services as $service): ?>
                                                            <div class="service-card" data-service='<?= json_encode($service) ?>'>
                                                                <div class="row align-items-center">
                                                                    <div class="col-1">
                                                                        <input type="checkbox" class="form-check-input service-checkbox"
                                                                            id="service_<?= $service['kode_layanan'] ?>"
                                                                            value="<?= $service['kode_layanan'] ?>">
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <?php if (!empty($service['foto'])): ?>
                                                                            <img src="<?= base_url('uploads/layanan/' . $service['foto']); ?>"
                                                                                alt="<?= esc($service['nama_layanan']) ?>"
                                                                                class="img-fluid service-image rounded">
                                                                        <?php else: ?>
                                                                            <div class="service-placeholder">
                                                                                <i class="fas fa-car-wash"></i>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <label for="service_<?= $service['kode_layanan'] ?>" class="mb-1 fw-semibold"><?= esc($service['nama_layanan']) ?></label>
                                                                        <p class="mb-1 text-muted small"><?= esc($service['deskripsi']) ?></p>
                                                                        <small class="text-muted">Durasi: <?= $service['durasi_menit'] ?> menit</small>
                                                                    </div>
                                                                    <div class="col-md-4 text-end">
                                                                        <strong class="text-primary fs-5">Rp <?= number_format($service['harga'], 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Selected Services Summary -->
                                        <div id="selectedServicesSummary" class="mt-3" style="display: none;">
                                            <div class="alert alert-primary">
                                                <h6><i class="fas fa-list me-2"></i>Layanan Dipilih:</h6>
                                                <div id="servicesList"></div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Total Durasi: <span id="totalDuration">0</span> menit</strong>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <strong>Total Harga: <span id="totalPrice">Rp 0</span></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Data Kendaraan -->
                                <div class="form-step" id="formStep2" style="display: none;">
                                    <div class="form-section">
                                        <h5><i class="fas fa-car"></i>Data Kendaraan</h5>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nomor Plat *</label>
                                                <input type="text" class="form-control" name="no_plat" placeholder="Contoh: B 1234 ABC" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Merk Kendaraan</label>
                                                <input type="text" class="form-control" name="merk_kendaraan" placeholder="Contoh: Honda, Toyota">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Jadwal -->
                                <div class="form-step" id="formStep3" style="display: none;">
                                    <div class="form-section">
                                        <h5><i class="fas fa-calendar-alt"></i>Pilih Jadwal</h5>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tanggal *</label>
                                                <input type="date" class="form-control" name="tanggal" min="<?= date('Y-m-d') ?>" required>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Pilih Jam Mulai *</label>
                                                <div id="timeSlotContainer">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        Pilih tanggal terlebih dahulu untuk melihat slot waktu yang tersedia
                                                    </div>
                                                </div>
                                                <input type="hidden" name="jam" required>
                                            </div>
                                        </div>

                                        <?php if (!$isLoggedIn): ?>
                                            <!-- Data Pelanggan - Hanya untuk guest -->
                                            <hr class="my-4">
                                            <h6><i class="fas fa-user"></i> Data Pelanggan</h6>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Lengkap *</label>
                                                    <input type="text" class="form-control" name="nama_pelanggan" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nomor HP *</label>
                                                    <input type="tel" class="form-control" name="no_hp" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Catatan Tambahan</label>
                                                    <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan khusus untuk booking Anda (opsional)"></textarea>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- User sudah login - tampilkan info dan catatan saja -->
                                            <hr class="my-4">
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-check text-primary me-2"></i>
                                                    <div>
                                                        <strong>Data Pelanggan</strong>
                                                        <p class="mb-0">Data pelanggan akan diambil dari akun Anda yang sudah login: <strong><?= esc($user['name'] ?? 'User') ?></strong></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">Catatan Tambahan</label>
                                                    <textarea class="form-control" name="catatan" rows="3" placeholder="Catatan khusus untuk booking Anda (opsional)"></textarea>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Booking Summary -->
                                    <div class="form-section">
                                        <h5><i class="fas fa-clipboard-list"></i>Ringkasan Booking</h5>
                                        <div id="bookingSummary">
                                            <!-- Will be filled by JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                                        <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                                    </button>
                                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                                        Selanjutnya<i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                        <i class="fas fa-check me-2"></i>Konfirmasi Booking
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentStep = 1;
        let selectedServices = []; // Changed from single service to array
        let maxSteps = 3;
        let isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Service selection with checkboxes
            document.querySelectorAll('.service-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const serviceCard = this.closest('.service-card');
                    const serviceData = JSON.parse(serviceCard.dataset.service);

                    if (this.checked) {
                        // Add service to selection
                        selectedServices.push(serviceData);
                        serviceCard.classList.add('selected');
                    } else {
                        // Remove service from selection
                        selectedServices = selectedServices.filter(s => s.kode_layanan !== serviceData.kode_layanan);
                        serviceCard.classList.remove('selected');
                    }

                    updateSelectedServicesSummary();
                    updateNextButton();

                    // If date is already selected, reload slots with new duration
                    const tanggalInput = document.querySelector('input[name="tanggal"]');
                    if (tanggalInput.value && selectedServices.length > 0) {
                        loadAvailableSlots(tanggalInput.value);
                    }
                });
            });

            // Date change handler
            document.querySelector('input[name="tanggal"]').addEventListener('change', function() {
                if (selectedServices.length > 0) {
                    loadAvailableSlots(this.value);
                }
            });

            // Auto-uppercase no plat
            document.querySelector('input[name="no_plat"]').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            updateNextButton();
        });

        function updateSelectedServicesSummary() {
            const summaryDiv = document.getElementById('selectedServicesSummary');
            const servicesListDiv = document.getElementById('servicesList');
            const totalDurationSpan = document.getElementById('totalDuration');
            const totalPriceSpan = document.getElementById('totalPrice');

            if (selectedServices.length === 0) {
                summaryDiv.style.display = 'none';
                return;
            }

            summaryDiv.style.display = 'block';

            // Calculate totals
            let totalDuration = 0;
            let totalPrice = 0;
            let servicesHtml = '';

            selectedServices.forEach(service => {
                totalDuration += parseInt(service.durasi_menit);
                totalPrice += parseFloat(service.harga);
                servicesHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span><i class="fas fa-check-circle text-success me-1"></i>${service.nama_layanan}</span>
                        <span class="text-muted small">${service.durasi_menit} mnt - Rp ${parseInt(service.harga).toLocaleString('id-ID')}</span>
                    </div>
                `;
            });

            servicesListDiv.innerHTML = servicesHtml;
            totalDurationSpan.textContent = totalDuration;
            totalPriceSpan.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        }

        function getTotalDuration() {
            return selectedServices.reduce((total, service) => total + parseInt(service.durasi_menit), 0);
        }

        function getTotalPrice() {
            return selectedServices.reduce((total, service) => total + parseFloat(service.harga), 0);
        }

        function getMainVehicleType() {
            if (selectedServices.length === 0) return null;
            // Use the vehicle type from the first selected service
            return selectedServices[0].jenis_kendaraan;
        }

        function changeStep(direction) {
            if (direction === 1) {
                if (!validateCurrentStep()) return;
                if (currentStep < maxSteps) {
                    currentStep++;
                } else {
                    return;
                }
            } else {
                if (currentStep > 1) {
                    currentStep--;
                }
            }

            updateStepDisplay();
            updateButtons();

            if (currentStep === maxSteps) {
                updateBookingSummary();
            }
        }

        function validateCurrentStep() {
            switch (currentStep) {
                case 1:
                    if (selectedServices.length === 0) {
                        Swal.fire('Error', 'Silakan pilih minimal satu layanan', 'error');
                        return false;
                    }
                    break;
                case 2:
                    const noPlat = document.querySelector('input[name="no_plat"]').value;
                    if (!noPlat.trim()) {
                        Swal.fire('Error', 'Nomor plat harus diisi', 'error');
                        return false;
                    }
                    break;
                case 3:
                    // Validate schedule
                    const tanggal = document.querySelector('input[name="tanggal"]').value;
                    const jam = document.querySelector('input[name="jam"]').value;
                    if (!tanggal || !jam) {
                        Swal.fire('Error', 'Tanggal dan jam harus dipilih', 'error');
                        return false;
                    }

                    // Validate customer data - Only required for guests
                    if (!isLoggedIn) {
                        const nama = document.querySelector('input[name="nama_pelanggan"]').value;
                        const hp = document.querySelector('input[name="no_hp"]').value;
                        if (!nama.trim() || !hp.trim()) {
                            Swal.fire('Error', 'Nama dan nomor HP harus diisi', 'error');
                            return false;
                        }
                    }
                    break;
            }
            return true;
        }

        function updateStepDisplay() {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(step => {
                step.style.display = 'none';
            });

            // Show current step
            document.getElementById(`formStep${currentStep}`).style.display = 'block';

            // Update step indicators
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    step.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    step.classList.add('active');
                }
            });
        }

        function updateButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            prevBtn.style.display = currentStep > 1 ? 'block' : 'none';

            if (currentStep === maxSteps) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            } else {
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }
        }

        function updateNextButton() {
            const nextBtn = document.getElementById('nextBtn');
            if (currentStep === 1 && selectedServices.length === 0) {
                nextBtn.disabled = true;
            } else {
                nextBtn.disabled = false;
            }
        }

        function loadAvailableSlots(tanggal) {
            console.log('loadAvailableSlots called with date:', tanggal);
            console.log('selectedServices:', selectedServices);

            const timeSlotContainer = document.getElementById('timeSlotContainer');
            timeSlotContainer.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Memuat slot waktu yang tersedia...
                </div>
            `;

            if (selectedServices.length === 0) {
                console.log('No services selected');
                timeSlotContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pilih layanan terlebih dahulu untuk melihat slot waktu
                    </div>
                `;
                return;
            }

            const totalDuration = getTotalDuration();
            const vehicleType = getMainVehicleType();

            console.log('Total duration:', totalDuration, 'minutes');
            console.log('Vehicle type:', vehicleType);

            // Generate flexible time slots based on total duration
            const availableSlots = generateFlexibleTimeSlots(totalDuration);

            // Fetch existing bookings to filter out conflicts
            const formData = new FormData();
            formData.append('tanggal', tanggal);
            formData.append('total_durasi', totalDuration);
            formData.append('jenis_kendaraan', vehicleType);

            fetch('<?= site_url('booking/get-available-slots') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response data:', data);

                    if (data.status === 'success') {
                        const existingBookings = data.existing_bookings || [];
                        const totalKaryawan = data.total_karyawan || 1;
                        const filteredSlots = filterSlotsByKaryawanAvailability(availableSlots, existingBookings, totalDuration, totalKaryawan);

                        if (filteredSlots.length > 0) {
                            renderTimeSlots(filteredSlots, totalDuration);
                        } else {
                            timeSlotContainer.innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-2"></i>
                                    Tidak ada karyawan tersedia untuk durasi ini. Silakan pilih tanggal lain.
                                </div>
                            `;
                        }
                    } else {
                        // Fallback to generated slots
                        renderTimeSlots(availableSlots, totalDuration);
                    }
                })
                .catch(error => {
                    console.error('Error loading slots:', error);
                    // Fallback to generated slots
                    renderTimeSlots(availableSlots, totalDuration);
                });
        }

        function renderTimeSlots(slots, totalDuration) {
            const timeSlotContainer = document.getElementById('timeSlotContainer');
            const currentTime = new Date().toTimeString().slice(0, 5);
            const selectedDate = document.querySelector('input[name="tanggal"]').value;
            const isToday = selectedDate === new Date().toISOString().slice(0, 10);

            let slotsHTML = '<div class="time-slots-grid">';

            slots.forEach(slot => {
                const endTime = addMinutesToTime(slot.start_time, totalDuration);
                const availableKaryawan = slot.available_karyawan || 1;
                const isPast = isToday && slot.start_time <= currentTime;
                const isUnavailable = isPast || availableKaryawan === 0;

                const badgeText = isUnavailable ? 'Tidak Tersedia' : `${availableKaryawan} Karyawan`;
                const badgeClass = isUnavailable ? 'unavailable' : '';

                slotsHTML += `
                    <div class="time-slot ${isUnavailable ? 'unavailable' : ''}" 
                         data-time="${slot.start_time}" 
                         ${!isUnavailable ? 'onclick="selectTimeSlot(this)"' : ''}>
                        <div class="time-slot-badge ${badgeClass}">${badgeText}</div>
                        <div class="time-slot-time">${slot.start_time}</div>
                        <div class="time-slot-info">sampai ${endTime}</div>
                        <div class="time-slot-duration">${totalDuration} menit</div>
                    </div>
                `;
            });

            slotsHTML += '</div>';
            timeSlotContainer.innerHTML = slotsHTML;
        }

        function selectTimeSlot(element) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });

            // Add selection to clicked slot
            element.classList.add('selected');

            // Update hidden input
            const selectedTime = element.dataset.time;
            document.querySelector('input[name="jam"]').value = selectedTime;

            console.log('Selected time:', selectedTime);
        }

        function generateFlexibleTimeSlots(totalDuration) {
            const slots = [];
            const startHour = 8; // 08:00
            const endHour = 20; // 20:00
            const intervalMinutes = 30; // 30 minute intervals

            for (let hour = startHour; hour < endHour; hour++) {
                for (let minute = 0; minute < 60; minute += intervalMinutes) {
                    const startTime = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    const endTimeMinutes = hour * 60 + minute + totalDuration;
                    const endHour = Math.floor(endTimeMinutes / 60);

                    // Check if end time is within operating hours
                    if (endHour <= 20) {
                        slots.push({
                            start_time: startTime,
                            duration: totalDuration
                        });
                    }
                }
            }

            return slots;
        }

        function filterConflictingSlots(availableSlots, existingBookings, totalDuration) {
            return availableSlots.filter(slot => {
                const slotStart = timeToMinutes(slot.start_time);
                const slotEnd = slotStart + totalDuration;

                // Check for conflicts with existing bookings
                return !existingBookings.some(booking => {
                    const bookingStart = timeToMinutes(booking.jam);
                    const bookingEnd = bookingStart + parseInt(booking.durasi || 60); // Default 60 if no duration

                    // Check if slots overlap
                    return (slotStart < bookingEnd && slotEnd > bookingStart);
                });
            });
        }

        function filterSlotsByKaryawanAvailability(availableSlots, existingBookings, totalDuration, totalKaryawan) {
            return availableSlots.map(slot => {
                const slotStart = timeToMinutes(slot.start_time);
                const slotEnd = slotStart + totalDuration;

                // Group bookings by karyawan to count unique busy karyawan
                const busyKaryawanSet = new Set();

                existingBookings.forEach(booking => {
                    const bookingStart = timeToMinutes(booking.jam);
                    const bookingEnd = bookingStart + parseInt(booking.durasi || 60);

                    // Check if slots overlap
                    if (slotStart < bookingEnd && slotEnd > bookingStart) {
                        // Add karyawan ID to set (Set automatically handles duplicates)
                        if (booking.id_karyawan) {
                            busyKaryawanSet.add(booking.id_karyawan);
                        }
                    }
                });

                // Count unique busy karyawan (1 karyawan per booking, regardless of how many services)
                const busyKaryawanCount = busyKaryawanSet.size;
                const availableKaryawan = totalKaryawan - busyKaryawanCount;

                // Debug logging for slot calculation
                console.log(`Slot ${slot.start_time}: Total karyawan = ${totalKaryawan}, Busy unique karyawan = ${busyKaryawanCount}, Available = ${availableKaryawan}`);

                return {
                    ...slot,
                    available_karyawan: availableKaryawan
                };
            }).filter(slot => slot.available_karyawan > 0); // Only show slots with available karyawan
        }

        function timeToMinutes(timeString) {
            const [hours, minutes] = timeString.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function addMinutesToTime(timeString, minutesToAdd) {
            const [hours, minutes] = timeString.split(':').map(Number);
            const totalMinutes = hours * 60 + minutes + minutesToAdd;
            const newHours = Math.floor(totalMinutes / 60);
            const newMinutes = totalMinutes % 60;
            return `${newHours.toString().padStart(2, '0')}:${newMinutes.toString().padStart(2, '0')}`;
        }

        function updateBookingSummary() {
            const summary = document.getElementById('bookingSummary');
            const noPlat = document.querySelector('input[name="no_plat"]').value;
            const merk = document.querySelector('input[name="merk_kendaraan"]').value;
            const tanggal = document.querySelector('input[name="tanggal"]').value;
            const jam = document.querySelector('input[name="jam"]').value;

            let pelangganInfo = '';
            if (!isLoggedIn) {
                const nama = document.querySelector('input[name="nama_pelanggan"]').value;
                const hp = document.querySelector('input[name="no_hp"]').value;
                const email = document.querySelector('input[name="email"]').value;
                pelangganInfo = `
                <div class="row">
                    <div class="col-12">
                        <h6>Data Pelanggan</h6>
                        <p><strong>${nama}</strong> - ${hp}${email ? ` (${email})` : ''}</p>
                    </div>
                </div>
                `;
            } else {
                pelangganInfo = `
                <div class="row">
                    <div class="col-12">
                        <h6>Data Pelanggan</h6>
                        <p><i class="fas fa-user-check text-success me-2"></i>Data diambil dari akun: <strong><?= esc($user['name'] ?? 'User') ?></strong></p>
                        <?php if (isset($pelanggan) && $pelanggan): ?>
                        <small class="text-muted">HP: <?= esc($pelanggan['no_hp'] ?? '-') ?> | Email: <?= esc($user['email'] ?? '-') ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                `;
            }

            let servicesHtml = '';
            selectedServices.forEach(service => {
                servicesHtml += `<p><strong>${service.nama_layanan}</strong> - ${service.durasi_menit} menit (Rp ${parseInt(service.harga).toLocaleString('id-ID')})</p>`;
            });

            const totalDuration = getTotalDuration();
            const totalPrice = getTotalPrice();
            const endTime = jam ? addMinutesToTime(jam, totalDuration) : '';

            summary.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Layanan</h6>
                        ${servicesHtml}
                        <p><strong>Total Durasi: ${totalDuration} menit</strong></p>
                        <p>Total Harga: <strong class="text-primary">Rp ${totalPrice.toLocaleString('id-ID')}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Kendaraan</h6>
                        <p><strong>${noPlat}</strong></p>
                        ${merk ? `<p>${merk}</p>` : ''}
                        
                        <h6>Jadwal</h6>
                        <p><strong>${new Date(tanggal).toLocaleDateString('id-ID', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}</strong></p>
                        <p>Waktu: <strong>${jam} - ${endTime}</strong></p>
                    </div>
                </div>
                
                ${pelangganInfo}
            `;
        }

        // Form submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateCurrentStep()) return;

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

            const formData = new FormData(this);

            // Add selected services data
            formData.append('selected_services', JSON.stringify(selectedServices.map(s => s.kode_layanan)));
            formData.append('total_durasi', getTotalDuration());
            formData.append('total_harga', getTotalPrice());
            formData.append('jenis_kendaraan', getMainVehicleType());

            fetch('<?= site_url('booking/store') ?>', {
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
                            html: `
                                <div class="text-start">
                                    ${data.message.replace(/\n/g, '<br>')}
                                </div>
                                <div class="mt-3 p-3 bg-light rounded">
                                    <strong>Selanjutnya:</strong> Lakukan pembayaran untuk mengkonfirmasi booking Anda
                                </div>
                            `,
                            showCancelButton: false,
                            confirmButtonText: 'Lanjut ke Pembayaran',
                            confirmButtonColor: '#28a745',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to payment page
                                window.location.href = data.data.payment_url;
                            }
                        });
                    } else {
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
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Konfirmasi Booking';
                });
        });
    </script>
</body>

</html>