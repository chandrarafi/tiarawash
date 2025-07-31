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
        margin-bottom: 1.5rem;
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
        margin-top: 1.5rem;
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
                    <i class="fas fa-receipt me-2"></i>
                    Detail Transaksi <?= esc($booking['no_transaksi'] ?? 'Booking') ?>
                </h2>
                <p class="mb-0 opacity-75">
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
            <?php if ($booking['no_transaksi']): ?>
                <div class="info-card card">
                    <div class="card-header">
                        <i class="fas fa-credit-card me-2"></i>Informasi Pembayaran
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">No. Transaksi</span>
                            <span class="info-value">
                                <span class="badge bg-info"><?= esc($booking['no_transaksi']) ?></span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Harga</span>
                            <span class="info-value text-success fw-bold">
                                Rp <?= number_format($booking['total_harga'] ?? 0, 0, ',', '.') ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Metode Pembayaran</span>
                            <span class="info-value"><?= ucfirst($booking['metode_pembayaran'] ?? 'Transfer') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status Pembayaran</span>
                            <span class="info-value">
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($booking['status_pembayaran']) {
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

                <!-- Payment Proof -->
                <?php if ($booking['bukti_pembayaran']): ?>
                    <div class="info-card card">
                        <div class="card-header">
                            <i class="fas fa-image me-2"></i>Bukti Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="payment-proof-container">
                                <?php
                                $fileExtension = pathinfo($booking['bukti_pembayaran'], PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                                ?>
                                <?php if ($isImage): ?>
                                    <img src="<?= base_url($booking['bukti_pembayaran']) ?>"
                                        alt="Bukti Pembayaran"
                                        class="payment-proof-image img-fluid"
                                        onclick="viewProof('<?= base_url($booking['bukti_pembayaran']) ?>')">
                                    <p class="mt-2 mb-0 small text-muted">Klik untuk memperbesar</p>
                                <?php else: ?>
                                    <div class="text-center">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <br>
                                        <a href="<?= base_url($booking['bukti_pembayaran']) ?>"
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

                <!-- Action Buttons -->
                <?php if ($booking['status_pembayaran'] === 'belum_bayar' && $booking['bukti_pembayaran']): ?>
                    <div class="action-card card">
                        <div class="card-header">
                            <i class="fas fa-cogs me-2"></i>Aksi Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-success"
                                    onclick="approvePayment(<?= $booking['transaksi_id'] ?>)">
                                    <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                                </button>
                                <button class="btn btn-danger"
                                    onclick="rejectPayment(<?= $booking['transaksi_id'] ?>)">
                                    <i class="fas fa-times me-2"></i>Tolak Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-transaction">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                    <h5>Belum Ada Transaksi</h5>
                    <p class="text-muted mb-0">Booking ini belum memiliki transaksi pembayaran.</p>
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