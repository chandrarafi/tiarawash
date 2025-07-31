<?= $this->extend('pelanggan/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-history me-3 fs-4"></i>
                            <div>
                                <h4 class="mb-0">Riwayat Booking</h4>
                                <small class="opacity-75">Semua booking layanan cuci kendaraan Anda</small>
                            </div>
                        </div>
                        <a href="<?= site_url('pelanggan/booking/create') ?>" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Booking Baru
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?php if (empty($bookings)): ?>
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted mb-3">Belum Ada Booking</h5>
                            <p class="text-muted mb-4">Anda belum memiliki riwayat booking. Mulai booking layanan cuci kendaraan sekarang!</p>
                            <a href="<?= site_url('pelanggan/booking/create') ?>" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Buat Booking Pertama
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Booking Stats -->
                        <div class="row mb-4">
                            <?php
                            $totalBookings = count($bookings);
                            $pendingCount = count(array_filter($bookings, fn($b) => $b['status'] === 'pending'));
                            $completedCount = count(array_filter($bookings, fn($b) => $b['status'] === 'selesai'));
                            $cancelledCount = count(array_filter($bookings, fn($b) => $b['status'] === 'dibatalkan'));
                            ?>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="stats-icon bg-primary">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stats-content">
                                        <div class="stats-number"><?= $totalBookings ?></div>
                                        <div class="stats-label">Total Booking</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="stats-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stats-content">
                                        <div class="stats-number"><?= $pendingCount ?></div>
                                        <div class="stats-label">Menunggu</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="stats-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stats-content">
                                        <div class="stats-number"><?= $completedCount ?></div>
                                        <div class="stats-label">Selesai</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="stats-icon bg-danger">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="stats-content">
                                        <div class="stats-number"><?= $cancelledCount ?></div>
                                        <div class="stats-label">Dibatalkan</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter & Search -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchBooking" placeholder="Cari berdasarkan kode booking, no plat, atau layanan...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="dikonfirmasi">Dikonfirmasi</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="sortBy">
                                    <option value="newest">Terbaru</option>
                                    <option value="oldest">Terlama</option>
                                    <option value="status">Status</option>
                                </select>
                            </div>
                        </div>

                        <!-- Booking List -->
                        <div class="row" id="bookingList">
                            <?php foreach ($bookings as $booking): ?>
                                <div class="col-lg-6 mb-4 booking-item"
                                    data-status="<?= $booking['status'] ?>"
                                    data-search="<?= strtolower($booking['kode_booking'] . ' ' . $booking['no_plat'] . ' ' . ($booking['nama_layanan'] ?? '')) ?>">
                                    <div class="booking-card">
                                        <div class="booking-header">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="booking-code"><?= esc($booking['kode_booking']) ?></h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= date('d M Y, H:i', strtotime($booking['tanggal'] . ' ' . $booking['jam'])) ?>
                                                    </small>
                                                </div>
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
                                            </div>
                                        </div>

                                        <div class="booking-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="booking-service">
                                                        <i class="fas fa-cogs text-primary me-2"></i>
                                                        <strong><?= esc($booking['nama_layanan'] ?? 'Layanan tidak ditemukan') ?></strong>
                                                    </div>
                                                    <div class="booking-vehicle">
                                                        <i class="fas fa-car text-success me-2"></i>
                                                        <?= ucfirst($booking['jenis_kendaraan']) ?> -
                                                        <strong><?= esc($booking['no_plat']) ?></strong>
                                                        <?php if ($booking['merk_kendaraan']): ?>
                                                            <small class="text-muted">(<?= esc($booking['merk_kendaraan']) ?>)</small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if ($booking['harga']): ?>
                                                        <div class="booking-price">
                                                            <i class="fas fa-money-bill text-warning me-2"></i>
                                                            <strong class="text-success">Rp <?= number_format($booking['harga'], 0, ',', '.') ?></strong>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div class="booking-actions">
                                                        <a href="<?= site_url('pelanggan/booking/detail/' . $booking['id']) ?>"
                                                            class="btn btn-sm btn-outline-primary mb-1">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if (in_array($booking['status'], ['pending', 'dikonfirmasi'])): ?>
                                                            <button class="btn btn-sm btn-outline-danger mb-1"
                                                                onclick="cancelBooking(<?= $booking['id'] ?>, '<?= esc($booking['kode_booking']) ?>')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($booking['catatan']): ?>
                                            <div class="booking-footer">
                                                <small class="text-muted">
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    <?= esc($booking['catatan']) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- No Results -->
                        <div id="noResults" class="text-center py-5 d-none">
                            <div class="mb-3">
                                <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="text-muted">Tidak ada booking yang ditemukan</h6>
                            <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchBooking');
        const statusFilter = document.getElementById('filterStatus');
        const sortSelect = document.getElementById('sortBy');
        const bookingList = document.getElementById('bookingList');
        const noResults = document.getElementById('noResults');

        // Search and filter functionality
        function filterBookings() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('filterStatus')?.value || '';
            const bookingItems = document.querySelectorAll('.booking-item');
            let visibleCount = 0;

            bookingItems.forEach(item => {
                const searchData = item.dataset.search || '';
                const status = item.dataset.status || '';

                const matchesSearch = searchData.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;

                if (matchesSearch && matchesStatus) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (noResults) {
                if (visibleCount === 0 && bookingItems.length > 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            }
        }

        // Sort functionality
        function sortBookings() {
            const sortBy = sortSelect?.value || 'newest';
            const bookingItems = Array.from(document.querySelectorAll('.booking-item'));

            bookingItems.sort((a, b) => {
                switch (sortBy) {
                    case 'oldest':
                        return new Date(a.querySelector('.text-muted').textContent) - new Date(b.querySelector('.text-muted').textContent);
                    case 'status':
                        return a.dataset.status.localeCompare(b.dataset.status);
                    case 'newest':
                    default:
                        return new Date(b.querySelector('.text-muted').textContent) - new Date(a.querySelector('.text-muted').textContent);
                }
            });

            // Re-append sorted items
            bookingItems.forEach(item => bookingList?.appendChild(item));
        }

        // Event listeners
        searchInput?.addEventListener('input', filterBookings);
        statusFilter?.addEventListener('change', filterBookings);
        sortSelect?.addEventListener('change', sortBookings);
    });

    // Cancel booking function
    function cancelBooking(bookingId, kodeBooking) {
        Swal.fire({
            title: 'Batalkan Booking?',
            text: `Apakah Anda yakin ingin membatalkan booking ${kodeBooking}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= site_url('pelanggan/booking/cancel/') ?>${bookingId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Dibatalkan',
                                text: data.message,
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Membatalkan',
                                text: data.message || 'Terjadi kesalahan saat membatalkan booking',
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
                    });
            }
        });
    }
</script>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0088cc, #00aaff) !important;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2d3748;
    }

    .stats-label {
        color: #718096;
        font-size: 0.9rem;
    }

    .booking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .booking-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .booking-header {
        background: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .booking-code {
        font-weight: bold;
        color: #0088cc;
        margin-bottom: 0.25rem;
    }

    .booking-body {
        padding: 1rem;
    }

    .booking-service,
    .booking-vehicle,
    .booking-price {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .booking-footer {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-top: 1px solid #e9ecef;
    }

    .booking-actions .btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border-radius: 16px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0088cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 136, 204, 0.25);
    }
</style>

<?= $this->endSection() ?>