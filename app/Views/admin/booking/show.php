<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-radius: 10px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .info-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .info-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
    }

    .info-card .card-body {
        padding: 1.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0;
    }

    .info-value {
        font-weight: 500;
        color: #495057;
        text-align: right;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .badge-belum-bayar {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .badge-dibayar {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .badge-batal {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .payment-proof-container {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .payment-proof-image {
        max-width: 100%;
        height: auto;
        max-height: 300px;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .payment-proof-image:hover {
        transform: scale(1.02);
    }

    .service-item {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-radius: 0 8px 8px 0;
    }

    .action-card {
        background: white;
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .no-transaction {
        text-align: center;
        padding: 2rem;
        background: #fff3cd;
        border-radius: 8px;
        border-left: 4px solid #ffc107;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .info-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-value {
            text-align: left;
            margin-top: 0.25rem;
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
                    <i class="fas fa-calendar-check me-2"></i>
                    Detail Booking <?= esc($booking['kode_booking'] ?? 'N/A') ?>
                </h2>
                <p class="mb-0 opacity-75">
                    <?php if ($transaksi): ?>
                        Transaksi: <?= esc($transaksi['no_transaksi']) ?> |
                    <?php endif; ?>
                    Informasi lengkap booking dan status pembayaran
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= site_url('admin/booking') ?>" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Booking Details -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="info-card card">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i>Informasi Pelanggan
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Nama Pelanggan</span>
                        <span class="info-value"><?= esc($booking['nama_pelanggan'] ?? 'Guest') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. HP</span>
                        <span class="info-value"><?= esc($booking['no_hp'] ?? '-') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Alamat</span>
                        <span class="info-value"><?= esc($booking['alamat'] ?? '-') ?></span>
                    </div>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="info-card card">
                <div class="card-header">
                    <i class="fas fa-calendar-check me-2"></i>Informasi Booking
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Kode Booking</span>
                        <span class="info-value">
                            <span class="badge bg-primary"><?= esc($booking['kode_booking'] ?? '-') ?></span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Booking</span>
                        <span class="info-value"><?= date('d F Y', strtotime($booking['tanggal_booking'] ?? $booking['tanggal'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jam Booking</span>
                        <span class="info-value"><?= esc($booking['jam_booking'] ?? $booking['jam']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Karyawan</span>
                        <span class="info-value">
                            <i class="fas fa-user-tie me-1 text-info"></i>
                            <?= esc($booking['namakaryawan'] ?? 'Belum ditentukan') ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status Booking</span>
                        <span class="info-value">
                            <?php
                            $bookingStatusClass = '';
                            $bookingStatusText = '';
                            switch ($booking['booking_status']) {
                                case 'menunggu_konfirmasi':
                                    $bookingStatusClass = 'badge bg-warning text-dark';
                                    $bookingStatusText = 'Menunggu Konfirmasi';
                                    break;
                                case 'dikonfirmasi':
                                    $bookingStatusClass = 'badge bg-info';
                                    $bookingStatusText = 'Dikonfirmasi';
                                    break;
                                case 'selesai':
                                    $bookingStatusClass = 'badge bg-success';
                                    $bookingStatusText = 'Selesai';
                                    break;
                                case 'dibatalkan':
                                case 'batal':
                                    $bookingStatusClass = 'badge bg-danger';
                                    $bookingStatusText = 'Dibatalkan';
                                    break;
                                default:
                                    $bookingStatusClass = 'badge bg-secondary';
                                    $bookingStatusText = 'Tidak Diketahui';
                                    break;
                            }
                            ?>
                            <span class="badge <?= $bookingStatusClass ?>"><?= $bookingStatusText ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <?php if (!empty($relatedBookings)): ?>
                <div class="info-card card">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>Layanan (<?= count($relatedBookings) ?> layanan)
                    </div>
                    <div class="card-body">
                        <?php foreach ($relatedBookings as $service): ?>
                            <div class="service-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($service['nama_layanan']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= $service['durasi_menit'] ?> menit
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">
                                            Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="info-card card">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>Layanan
                    </div>
                    <div class="card-body">
                        <div class="service-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= esc($booking['nama_layanan'] ?? 'Layanan') ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= $booking['durasi_menit'] ?? 60 ?> menit
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">
                                        Rp <?= number_format($booking['harga'] ?? 0, 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Payment & Actions -->
        <div class="col-lg-4">
            <!-- Payment Information -->
            <?php if ($transaksi): ?>
                <div class="info-card card">
                    <div class="card-header">
                        <i class="fas fa-credit-card me-2"></i>Informasi Pembayaran
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">No. Transaksi</span>
                            <span class="info-value">
                                <span class="badge bg-info"><?= esc($transaksi['no_transaksi']) ?></span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Harga</span>
                            <span class="info-value text-success fw-bold">
                                Rp <?= number_format($transaksi['total_harga'] ?? 0, 0, ',', '.') ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Metode Pembayaran</span>
                            <span class="info-value"><?= ucfirst($transaksi['metode_pembayaran'] ?? 'Transfer') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status Pembayaran</span>
                            <span class="info-value">
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($transaksi['status_pembayaran']) {
                                    case 'belum_bayar':
                                        $statusClass = 'badge-belum-bayar';
                                        $statusText = 'Belum Bayar';
                                        break;
                                    case 'dibayar':
                                        $statusClass = 'badge-dibayar';
                                        $statusText = 'Dibayar';
                                        break;
                                    case 'batal':
                                        $statusClass = 'badge-batal';
                                        $statusText = 'Batal';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Booking Information when no transaction -->
                <div class="info-card card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Informasi Booking
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Status Booking</span>
                            <span class="info-value">
                                <?php
                                $bookingStatusClass = '';
                                $bookingStatusText = '';
                                switch ($booking['status']) {
                                    case 'menunggu_konfirmasi':
                                        $bookingStatusClass = 'badge bg-warning text-dark';
                                        $bookingStatusText = 'Menunggu Konfirmasi';
                                        break;
                                    case 'dikonfirmasi':
                                        $bookingStatusClass = 'badge bg-info';
                                        $bookingStatusText = 'Dikonfirmasi';
                                        break;
                                    case 'selesai':
                                        $bookingStatusClass = 'badge bg-success';
                                        $bookingStatusText = 'Selesai';
                                        break;
                                    case 'dibatalkan':
                                    case 'batal':
                                        $bookingStatusClass = 'badge bg-danger';
                                        $bookingStatusText = 'Dibatalkan';
                                        break;
                                    default:
                                        $bookingStatusClass = 'badge bg-secondary';
                                        $bookingStatusText = 'Tidak Diketahui';
                                        break;
                                }
                                ?>
                                <span class="<?= $bookingStatusClass ?>"><?= $bookingStatusText ?></span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status Transaksi</span>
                            <span class="info-value">
                                <span class="badge bg-secondary">Belum Ada Transaksi</span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Proof -->
            <?php if ($transaksi && $transaksi['bukti_pembayaran']): ?>
                <div class="info-card card">
                    <div class="card-header">
                        <i class="fas fa-image me-2"></i>Bukti Pembayaran
                    </div>
                    <div class="card-body">
                        <div class="payment-proof-container">
                            <?php
                            $fileExtension = pathinfo($transaksi['bukti_pembayaran'], PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                            ?>
                            <?php if ($isImage): ?>
                                <img src="<?= base_url($transaksi['bukti_pembayaran']) ?>"
                                    alt="Bukti Pembayaran"
                                    class="payment-proof-image img-fluid"
                                    onclick="viewProof('<?= base_url($transaksi['bukti_pembayaran']) ?>')">
                                <p class="mt-2 mb-0 small text-muted">Klik untuk memperbesar</p>
                            <?php else: ?>
                                <div class="text-center">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                    <br>
                                    <a href="<?= base_url($transaksi['bukti_pembayaran']) ?>"
                                        target="_blank"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Unduh PDF
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Booking Status Actions Card -->
            <?php if ($booking['booking_status'] === 'menunggu_konfirmasi'): ?>
                <div class="card shadow mb-4" style="border-left: 4px solid #007bff; margin-bottom: 2rem !important;">
                    <div class="card-header py-3" style="background-color: #cce7ff; border-bottom: 1px solid #99d6ff;">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-check me-2"></i>Konfirmasi Booking
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Booking menunggu konfirmasi!</strong> Silakan konfirmasi booking ini untuk melanjutkan proses.
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button class="btn btn-primary btn-lg me-md-2"
                                onclick="confirmBookingFromDetail('<?= $booking['kode_booking'] ?>')">
                                <i class="fas fa-check me-2"></i>Konfirmasi Booking
                            </button>
                            <button class="btn btn-danger btn-lg"
                                onclick="rejectBookingFromDetail('<?= $booking['kode_booking'] ?>')">
                                <i class="fas fa-times me-2"></i>Tolak Booking
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Actions Card -->
            <?php if ($transaksi && $transaksi['status_pembayaran'] === 'belum_bayar' && $transaksi['bukti_pembayaran']): ?>
                <div class="card shadow mb-4" style="border-left: 4px solid #ffc107; margin-bottom: 2rem !important;">
                    <div class="card-header py-3" style="background-color: #fff3cd; border-bottom: 1px solid #ffeaa7;">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perhatian!</strong> Pelanggan telah mengupload bukti pembayaran. Silakan periksa dan konfirmasi pembayaran.
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button class="btn btn-success btn-lg me-md-2"
                                onclick="approvePayment(<?= $transaksi['id'] ?>)">
                                <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                            </button>
                            <button class="btn btn-danger btn-lg"
                                onclick="rejectPayment(<?= $transaksi['id'] ?>)">
                                <i class="fas fa-times me-2"></i>Tolak Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            <?php elseif ($transaksi && $transaksi['status_pembayaran'] === 'belum_bayar'): ?>
                <div class="card shadow mb-4" style="border-left: 4px solid #17a2b8; margin-bottom: 2rem !important;">
                    <div class="card-header py-3" style="background-color: #d1ecf1; border-bottom: 1px solid #bee5eb;">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-clock me-2"></i>Status Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Menunggu pelanggan mengupload bukti pembayaran.
                        </div>
                    </div>
                </div>
            <?php elseif ($transaksi && $transaksi['status_pembayaran'] === 'dibayar'): ?>
                <div class="card shadow mb-4" style="border-left: 4px solid #28a745; margin-bottom: 2rem !important;">
                    <div class="card-header py-3" style="background-color: #d4edda; border-bottom: 1px solid #c3e6cb;">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-check-circle me-2"></i>Pembayaran Terkonfirmasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Pembayaran telah dikonfirmasi. Booking siap diproses.
                        </div>
                    </div>
                </div>
            <?php elseif ($transaksi && $transaksi['status_pembayaran'] === 'batal'): ?>
                <div class="card shadow mb-4" style="border-left: 4px solid #dc3545; margin-bottom: 2rem !important;">
                    <div class="card-header py-3" style="background-color: #f8d7da; border-bottom: 1px solid #f5c6cb;">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="fas fa-times-circle me-2"></i>Pembayaran Ditolak
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-times-circle me-2"></i>
                            Pembayaran telah ditolak atau dibatalkan.
                        </div>
                    </div>
                </div>
            <?php elseif (!$transaksi): ?>
                <div class="card shadow mb-4" style="border-left: 4px solid #6c757d; margin-bottom: 2rem !important;">
                    <div class="card-header py-3" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <h6 class="m-0 font-weight-bold text-muted">
                            <i class="fas fa-info-circle me-2"></i>Status Transaksi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada transaksi untuk booking ini.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function viewProof(imageUrl) {
        Swal.fire({
            imageUrl: imageUrl,
            imageAlt: 'Bukti Pembayaran',
            showCloseButton: true,
            showConfirmButton: false,
            imageWidth: '90%',
            imageHeight: 'auto',
            customClass: {
                popup: 'swal-wide'
            }
        });
    }

    function approvePayment(transaksiId) {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= site_url('admin/booking/approve-payment/') ?>${transaksiId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    function confirmBookingFromDetail(kodeBooking) {
        Swal.fire({
            title: 'Konfirmasi Booking',
            text: 'Apakah Anda yakin ingin mengkonfirmasi booking ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= site_url('admin/booking/confirm-booking/') ?>${kodeBooking}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    function rejectBookingFromDetail(kodeBooking) {
        Swal.fire({
            title: 'Tolak Booking',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Masukkan alasan mengapa booking ditolak...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan booking'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan harus diisi!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('alasan', result.value);

                fetch(`<?= site_url('admin/booking/reject-booking/') ?>${kodeBooking}`, {
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
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    function rejectPayment(transaksiId) {
        Swal.fire({
            title: 'Tolak Pembayaran',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Masukkan alasan mengapa pembayaran ditolak...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan harus diisi!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('alasan', result.value);

                fetch(`<?= site_url('admin/booking/reject-payment/') ?>${transaksiId}`, {
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
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }
</script>

<style>
    .swal-wide {
        width: 90% !important;
    }
</style>
<?= $this->endSection() ?>