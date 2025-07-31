<?= $this->extend('pelanggan/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Professional Statistics Cards -->
<div class="row g-4 mb-5">
    <!-- Booking CTA Card - Prominent call to action -->
    <div class="col-12 mb-4">
        <div class="card booking-cta-card">
            <div class="card-body text-center py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="text-primary mb-2">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Butuh Layanan Cuci Kendaraan?
                        </h4>
                        <p class="text-muted mb-0">
                            Buat booking baru melalui website utama kami dengan berbagai pilihan layanan premium
                        </p>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= site_url('/') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Buat Booking di Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Booking Card -->
    <div class="col-xl-3 col-md-6">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stats-content">
                <div class="stat-value"><?= $stats['total_booking'] ?? 0 ?></div>
                <div class="stat-label">Total Booking</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i> +12% dari bulan lalu
                </div>
            </div>
        </div>
    </div>

    <!-- Antrian Aktif Card -->
    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="stats-icon">
                <i class="fas fa-list-ol"></i>
            </div>
            <div class="stats-content">
                <div class="stat-value"><?= $pendingBookings ?? 0 ?></div>
                <div class="stat-label">Antrian Aktif</div>
                <div class="stat-trend">
                    <i class="fas fa-clock"></i> Menunggu proses
                </div>
            </div>
        </div>
    </div>

    <!-- Total Transaksi Card -->
    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stats-content">
                <div class="stat-value"><?= $stats['total_transaksi'] ?? 0 ?></div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i> +15% dari bulan lalu
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pengeluaran Card -->
    <div class="col-xl-3 col-md-6">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-content">
                <div class="stat-value">Rp <?= number_format($totalSpent ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-trend">
                    <i class="fas fa-chart-line"></i> Lifetime spending
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Booking Terbaru
                </h5>
                <a href="<?= site_url('pelanggan/booking/history') ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recentBookings)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Layanan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($recentBookings, 0, 5) as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?= esc($booking['kode_booking']) ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($booking['nama_layanan'] ?? 'N/A') ?></strong>
                                                <br><small class="text-muted"><?= esc($booking['no_plat']) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($booking['tanggal'] . ' ' . $booking['jam'])) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'dikonfirmasi' => 'info',
                                                'selesai' => 'success',
                                                'dibatalkan' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$booking['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($booking['status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">Belum Ada Booking</h6>
                        <p class="text-muted">Anda belum memiliki booking apapun</p>
                        <a href="<?= site_url('/') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Buat Booking
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>Transaksi Terbaru
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="showAllTransactions()">
                    Lihat Semua
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($recentTransactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Layanan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($recentTransactions, 0, 5) as $transaction): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-success"><?= esc($transaction['no_transaksi']) ?></strong>
                                            <br><small class="text-muted"><?= date('d/m/Y', strtotime($transaction['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($transaction['nama_layanan'] ?? 'N/A') ?></strong>
                                                <br><small class="text-muted"><?= esc($transaction['no_plat'] ?? '') ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-success">Rp <?= number_format($transaction['total_harga'], 0, ',', '.') ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $paymentColors = [
                                                'pending' => 'warning',
                                                'dibayar' => 'success',
                                                'gagal' => 'danger'
                                            ];
                                            $paymentColor = $paymentColors[$transaction['status_pembayaran']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $paymentColor ?>"><?= ucfirst($transaction['status_pembayaran']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">Belum Ada Transaksi</h6>
                        <p class="text-muted">Transaksi Anda akan muncul di sini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Antrian Status Row -->
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list-ul me-2"></i>Status Antrian Hari Ini
                </h5>
            </div>
            <div class="card-body">
                <?php
                // Get today's queue data from controller
                $todayQueues = $todayQueues ?? [];
                ?>

                <?php if (!empty($todayQueues)): ?>
                    <div class="row">
                        <?php foreach ($todayQueues as $queue): ?>
                            <div class="col-md-4 mb-3">
                                <div class="queue-card">
                                    <div class="queue-number"><?= esc($queue['nomor_antrian']) ?></div>
                                    <div class="queue-service"><?= esc($queue['nama_layanan']) ?></div>
                                    <div class="queue-vehicle"><?= esc($queue['no_plat']) ?></div>
                                    <div class="queue-status">
                                        <?php
                                        $queueColors = [
                                            'menunggu' => 'warning',
                                            'diproses' => 'info',
                                            'selesai' => 'success'
                                        ];
                                        $queueColor = $queueColors[$queue['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $queueColor ?>"><?= ucfirst($queue['status']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-list-ol text-muted mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-muted">Tidak Ada Antrian Hari Ini</h5>
                        <p class="text-muted">Anda tidak memiliki antrian untuk hari ini</p>
                        <a href="<?= site_url('/') ?>" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>Buat Booking Baru
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Summary -->
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="quick-stat">
                            <i class="fas fa-calendar-check text-primary fs-2 mb-2"></i>
                            <h6>Booking Bulan Ini</h6>
                            <span class="badge bg-primary"><?= $stats['booking_bulan_ini'] ?? 0 ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quick-stat">
                            <i class="fas fa-hourglass-half text-warning fs-2 mb-2"></i>
                            <h6>Booking Pending</h6>
                            <span class="badge bg-warning"><?= $pendingBookings ?? 0 ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quick-stat">
                            <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                            <h6>Booking Selesai</h6>
                            <span class="badge bg-success"><?= $completedBookings ?? 0 ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quick-stat">
                            <i class="fas fa-star text-info fs-2 mb-2"></i>
                            <h6>Rating Rata-rata</h6>
                            <span class="badge bg-info"><?= $stats['rating_rata'] ?? 4.8 ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Professional Statistics Cards */
    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    .stats-card.primary::before {
        background: linear-gradient(135deg, #0088cc, #00aaff);
    }

    .stats-card.success::before {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .stats-card.warning::before {
        background: linear-gradient(135deg, #ffc107, #ff8c42);
    }

    .stats-card.info::before {
        background: linear-gradient(135deg, #17a2b8, #20c997);
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stats-card .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }

    .stats-card.primary .stats-icon {
        background: linear-gradient(135deg, #0088cc, #00aaff);
    }

    .stats-card.success .stats-icon {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .stats-card.warning .stats-icon {
        background: linear-gradient(135deg, #ffc107, #ff8c42);
    }

    .stats-card.info .stats-icon {
        background: linear-gradient(135deg, #17a2b8, #20c997);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #718096;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stat-trend {
        font-size: 0.8rem;
        color: #28a745;
        font-weight: 500;
    }

    .stat-trend i {
        margin-right: 0.25rem;
    }

    /* Queue Cards */
    .queue-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .queue-card:hover {
        transform: translateY(-2px);
        border-color: #0088cc;
    }

    .queue-number {
        font-size: 2rem;
        font-weight: 800;
        color: #0088cc;
        margin-bottom: 0.5rem;
    }

    .queue-service {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .queue-vehicle {
        color: #718096;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .queue-status {
        margin-top: 1rem;
    }

    /* Quick Stats */
    .quick-stat {
        padding: 1rem;
    }

    .quick-stat h6 {
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    /* Table Enhancements */
    .table th {
        font-weight: 600;
        color: #2d3748;
        border-bottom: 2px solid #e9ecef;
        font-size: 0.85rem;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 136, 204, 0.05);
    }

    /* Cards */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 16px 16px 0 0 !important;
        padding: 1.25rem;
    }

    .card-title {
        color: #2d3748;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-card {
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
        }

        .queue-card {
            margin-bottom: 1rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Show all transactions modal
    function showAllTransactions() {
        Swal.fire({
            title: 'Semua Transaksi',
            html: `
            <div class="text-start">
                <?php if (!empty($recentTransactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Layanan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                    <tr>
                                        <td><strong><?= esc($transaction['no_transaksi']) ?></strong></td>
                                        <td><?= esc($transaction['nama_layanan'] ?? 'N/A') ?></td>
                                        <td><strong class="text-success">Rp <?= number_format($transaction['total_harga'], 0, ',', '.') ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= ($transaction['status_pembayaran'] == 'dibayar') ? 'success' : 'warning' ?>">
                                                <?= ucfirst($transaction['status_pembayaran']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($transaction['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-receipt text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted">Belum ada transaksi</p>
                    </div>
                <?php endif; ?>
            </div>
        `,
            width: 800,
            showConfirmButton: false,
            showCloseButton: true
        });
    }

    // Animate counter numbers
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-value');

        counters.forEach(counter => {
            const text = counter.textContent;
            const target = parseInt(text.replace(/[^\d]/g, ''));
            if (target && !isNaN(target)) {
                const increment = target / 100;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        if (text.includes('Rp')) {
                            counter.textContent = 'Rp ' + target.toLocaleString('id-ID');
                        } else {
                            counter.textContent = target;
                        }
                        clearInterval(timer);
                    } else {
                        if (text.includes('Rp')) {
                            counter.textContent = 'Rp ' + Math.floor(current).toLocaleString('id-ID');
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                    }
                }, 20);
            }
        });
    }

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Animate counters on load
        setTimeout(animateCounters, 500);

        // Add hover effects to cards
        const cards = document.querySelectorAll('.card, .stats-card, .queue-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
<?= $this->endSection() ?>