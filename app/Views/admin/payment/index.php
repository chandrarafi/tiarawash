<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
        border: none;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .stats-card .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stats-card .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .payment-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .payment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .payment-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px 15px 0 0;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        color: #8b4513;
    }

    .btn-action {
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-approve {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        color: white;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
    }

    .btn-view {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
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
                <button class="btn btn-primary" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="stats-number"><?= $stats['pending'] ?></div>
                        <div class="small">Menunggu Konfirmasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="stats-number"><?= $stats['approved'] ?></div>
                        <div class="small">Telah Dikonfirmasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <div class="stats-number"><?= $stats['rejected'] ?></div>
                        <div class="small">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #495057;">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="stats-number"><?= $stats['total'] ?></div>
                        <div class="small">Total Transaksi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Daftar Pembayaran Menunggu Konfirmasi
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($payments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>Tidak Ada Pembayaran Pending</h4>
                            <p>Semua pembayaran telah diproses atau belum ada pembayaran baru.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <div class="payment-card card">
                                <div class="payment-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1">
                                                <i class="fas fa-receipt me-2"></i>
                                                <?= esc($payment['no_transaksi']) ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d M Y H:i', strtotime($payment['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock me-1"></i>Menunggu Konfirmasi
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <strong>Pelanggan:</strong><br>
                                                    <span class="text-primary">
                                                        <i class="fas fa-user me-1"></i>
                                                        <?= esc($payment['nama_pelanggan'] ?? 'Guest') ?>
                                                    </span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>Booking:</strong><br>
                                                    <span class="text-info">
                                                        <i class="fas fa-bookmark me-1"></i>
                                                        <?= esc($payment['kode_booking'] ?? 'N/A') ?>
                                                    </span>
                                                </div>
                                                <div class="col-sm-6 mt-2">
                                                    <strong>Kendaraan:</strong><br>
                                                    <span class="text-secondary">
                                                        <i class="fas fa-car me-1"></i>
                                                        <?= esc($payment['no_plat']) ?>
                                                    </span>
                                                </div>
                                                <div class="col-sm-6 mt-2">
                                                    <strong>Jadwal:</strong><br>
                                                    <span class="text-warning">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= date('d M Y', strtotime($payment['booking_tanggal'])) ?> - <?= $payment['booking_jam'] ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="mb-2">
                                                <h4 class="text-success mb-0">
                                                    Rp <?= number_format($payment['total_harga'], 0, ',', '.') ?>
                                                </h4>
                                                <small class="text-muted">Total Pembayaran</small>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a href="<?= site_url('admin/payment/detail/' . $payment['id']) ?>"
                                                    class="btn btn-view btn-sm">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </a>
                                                <button class="btn btn-approve btn-sm"
                                                    onclick="approvePayment(<?= $payment['id'] ?>)">
                                                    <i class="fas fa-check me-1"></i>Konfirmasi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function refreshData() {
        location.reload();
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
                fetch(`<?= site_url('admin/payment/approve/') ?>${transaksiId}`, {
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

    // Auto refresh every 30 seconds
    setInterval(() => {
        // Only refresh if there are pending payments
        if (<?= count($payments) ?> > 0) {
            location.reload();
        }
    }, 30000);
</script>
<?= $this->endSection() ?>