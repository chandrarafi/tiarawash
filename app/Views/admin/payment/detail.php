<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .detail-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
    }

    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        flex: 0 0 auto;
        margin-right: 1rem;
    }

    .info-value {
        color: #212529;
        text-align: right;
        flex: 1;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-pending {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        color: #8b4513;
    }

    .status-approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .status-rejected {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    .payment-proof {
        max-width: 100%;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-proof:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .service-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-left: 4px solid #667eea;
    }

    .btn-action {
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: white;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
    }

    .btn-back {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }

    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #667eea;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 1rem;
        width: 2px;
        height: calc(100% - 0.5rem);
        background: #e9ecef;
    }

    .timeline-item:last-child::after {
        display: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><?= esc($title) ?></h2>
                    <p class="text-muted mb-0"><?= esc($subtitle) ?></p>
                </div>
                <a href="<?= site_url('admin/payment') ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Details -->
        <div class="col-lg-8">
            <!-- Transaction Info -->
            <div class="detail-card card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Nomor Transaksi</span>
                        <span class="info-value">
                            <strong><?= esc($transaksi['no_transaksi']) ?></strong>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status Pembayaran</span>
                        <span class="info-value">
                            <span class="status-badge status-<?= $transaksi['status_pembayaran'] === 'belum_bayar' ? 'pending' : ($transaksi['status_pembayaran'] === 'dibayar' ? 'approved' : 'rejected') ?>">
                                <i class="fas fa-<?= $transaksi['status_pembayaran'] === 'belum_bayar' ? 'clock' : ($transaksi['status_pembayaran'] === 'dibayar' ? 'check-circle' : 'times-circle') ?> me-1"></i>
                                <?= ucfirst($transaksi['status_pembayaran']) ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Transaksi</span>
                        <span class="info-value"><?= date('d F Y H:i', strtotime($transaksi['created_at'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Metode Pembayaran</span>
                        <span class="info-value">
                            <i class="fas fa-university me-1"></i>
                            <?= ucfirst($transaksi['metode_pembayaran']) ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Pembayaran</span>
                        <span class="info-value">
                            <h5 class="text-success mb-0">
                                Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                            </h5>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="detail-card card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informasi Pelanggan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Nama Pelanggan</span>
                        <span class="info-value"><?= esc($pelanggan['nama_pelanggan'] ?? 'Guest') ?></span>
                    </div>
                    <?php if ($pelanggan): ?>
                        <div class="info-item">
                            <span class="info-label">No. HP</span>
                            <span class="info-value"><?= esc($pelanggan['no_hp']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Alamat</span>
                            <span class="info-value"><?= esc($pelanggan['alamat']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="info-label">No. Plat Kendaraan</span>
                        <span class="info-value">
                            <strong><?= esc($transaksi['no_plat']) ?></strong>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Kendaraan</span>
                        <span class="info-value"><?= ucfirst($transaksi['jenis_kendaraan']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <?php if (!empty($allBookings)): ?>
                <div class="detail-card card">
                    <div class="detail-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Detail Layanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($allBookings as $booking): ?>
                            <div class="service-item">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="mb-1"><?= esc($booking['nama_layanan']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= $booking['durasi_menit'] ?> menit
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Jadwal:</small><br>
                                        <strong><?= date('H:i', strtotime($booking['jam'])) ?></strong>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <strong>Rp <?= number_format($booking['harga'], 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                                <?php if ($booking['namakaryawan']): ?>
                                    <div class="mt-2">
                                        <small class="text-primary">
                                            <i class="fas fa-user-tie me-1"></i>
                                            Karyawan: <?= esc($booking['namakaryawan']) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payment Proof & Actions -->
        <div class="col-lg-4">
            <!-- Payment Proof -->
            <?php if (!empty($transaksi['bukti_pembayaran'])): ?>
                <div class="detail-card card">
                    <div class="detail-header">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            Bukti Pembayaran
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <?php
                        $fileExtension = pathinfo($transaksi['bukti_pembayaran'], PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                        ?>

                        <?php if ($isImage): ?>
                            <img src="<?= base_url($transaksi['bukti_pembayaran']) ?>"
                                alt="Bukti Pembayaran"
                                class="payment-proof"
                                onclick="viewProof('<?= base_url($transaksi['bukti_pembayaran']) ?>')">
                            <p class="text-muted mt-2 small">Klik untuk memperbesar</p>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-pdf" style="font-size: 4rem; color: #dc3545;"></i>
                                <p class="mt-2">Dokumen PDF</p>
                                <a href="<?= base_url($transaksi['bukti_pembayaran']) ?>"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-1"></i>Buka File
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <?php if ($transaksi['status_pembayaran'] === 'belum_bayar'): ?>
                <div class="detail-card card">
                    <div class="detail-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>
                            Aksi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-approve btn-action" onclick="approvePayment()">
                                <i class="fas fa-check-circle me-2"></i>
                                Konfirmasi Pembayaran
                            </button>
                            <button class="btn btn-reject btn-action" onclick="rejectPayment()">
                                <i class="fas fa-times-circle me-2"></i>
                                Tolak Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Timeline -->
            <div class="detail-card card">
                <div class="detail-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-item">
                        <strong>Transaksi Dibuat</strong><br>
                        <small class="text-muted"><?= date('d M Y H:i', strtotime($transaksi['created_at'])) ?></small>
                    </div>
                    <?php if ($transaksi['bukti_pembayaran']): ?>
                        <div class="timeline-item">
                            <strong>Bukti Pembayaran Diupload</strong><br>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($transaksi['created_at'])) ?></small>
                        </div>
                    <?php endif; ?>
                    <?php if ($transaksi['status_pembayaran'] !== 'belum_bayar'): ?>
                        <div class="timeline-item">
                            <strong>Status Diubah ke <?= ucfirst($transaksi['status_pembayaran']) ?></strong><br>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($transaksi['updated_at'])) ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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

    function approvePayment() {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda yakin ingin mengkonfirmasi pembayaran ini? Booking akan diubah statusnya menjadi dikonfirmasi.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= site_url('admin/payment/approve/' . $transaksi['id']) ?>`, {
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
                                window.location.href = '<?= site_url('admin/payment') ?>';
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

    function rejectPayment() {
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

                fetch(`<?= site_url('admin/payment/reject/' . $transaksi['id']) ?>`, {
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
                                window.location.href = '<?= site_url('admin/payment') ?>';
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