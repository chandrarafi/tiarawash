<?= $this->extend('pelanggan/layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="row">
        <!-- Booking Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt me-3 fs-4"></i>
                            <div>
                                <h4 class="mb-0">Detail Booking</h4>
                                <small class="opacity-75">Kode: <?= esc($booking['kode_booking']) ?></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php
                            $statusColors = [
                                'menunggu_konfirmasi' => 'warning',
                                'dikonfirmasi' => 'info',
                                'selesai' => 'success',
                                'dibatalkan' => 'danger',
                                'batal' => 'danger'
                            ];
                            $statusLabels = [
                                'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                                'batal' => 'Dibatalkan'
                            ];
                            $statusColor = $statusColors[$booking['status']] ?? 'secondary';
                            $statusLabel = $statusLabels[$booking['status']] ?? ucfirst($booking['status']);
                            ?>
                            <span class="badge bg-<?= $statusColor ?> fs-6 px-3 py-2">
                                <?= $statusLabel ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <!-- Informasi Pelanggan -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Informasi Pelanggan
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">Nama:</td>
                                    <td class="fw-bold"><?= esc($booking['nama_pelanggan'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kode Pelanggan:</td>
                                    <td><?= esc($booking['pelanggan_id']) ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Informasi Layanan -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-cogs me-2"></i>Informasi Layanan
                                <?php if (!empty($relatedBookings) && count($relatedBookings) > 1): ?>
                                    <span class="badge bg-success ms-2"><?= count($relatedBookings) ?> Layanan</span>
                                <?php endif; ?>
                            </h5>

                            <?php if (!empty($relatedBookings) && count($relatedBookings) > 1): ?>
                                <!-- Multiple Services -->
                                <?php
                                $totalHarga = 0;
                                $totalDurasi = 0;
                                ?>
                                <div class="service-list">
                                    <?php foreach ($relatedBookings as $service): ?>
                                        <?php
                                        $totalHarga += (float)$service['harga'];
                                        $totalDurasi += (int)$service['durasi_menit'];
                                        ?>
                                        <div class="service-item mb-3 p-2 border rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= esc($service['nama_layanan']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i><?= $service['durasi_menit'] ?> menit
                                                        | <i class="fas fa-calendar me-1"></i><?= date('H:i', strtotime($service['jam'])) ?>
                                                    </small>
                                                </div>
                                                <div class="text-success fw-bold">
                                                    Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="border-top pt-2 mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Total:</strong>
                                        <div>
                                            <span class="text-success fw-bold">Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
                                            <br>
                                            <small class="text-muted"><?= $totalDurasi ?> menit</small>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Single Service -->
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">Layanan:</td>
                                        <td class="fw-bold"><?= esc($booking['nama_layanan'] ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Harga:</td>
                                        <td class="text-success fw-bold">Rp <?= number_format($booking['harga'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Durasi:</td>
                                        <td><?= ($booking['durasi_menit'] ?? 0) ?> menit</td>
                                    </tr>
                                </table>
                            <?php endif; ?>
                        </div>

                        <!-- Informasi Kendaraan -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-warning mb-3">
                                <i class="fas fa-car me-2"></i>Informasi Kendaraan
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">Jenis:</td>
                                    <td class="fw-bold"><?= ucfirst($booking['jenis_kendaraan']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Plat:</td>
                                    <td class="fw-bold text-dark"><?= esc($booking['no_plat']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Merk:</td>
                                    <td><?= esc($booking['merk_kendaraan'] ?? 'Tidak disebutkan') ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Informasi Jadwal -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Informasi Jadwal
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">Tanggal:</td>
                                    <td class="fw-bold"><?= date('d F Y', strtotime($booking['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jam:</td>
                                    <td class="fw-bold"><?= date('H:i', strtotime($booking['jam'])) ?> WIB</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dibuat:</td>
                                    <td><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'menunggu_konfirmasi' => 'warning',
                                            'dikonfirmasi' => 'info',
                                            'selesai' => 'success',
                                            'dibatalkan' => 'danger',
                                            'batal' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                            'dikonfirmasi' => 'Dikonfirmasi',
                                            'selesai' => 'Selesai',
                                            'dibatalkan' => 'Dibatalkan',
                                            'batal' => 'Dibatalkan'
                                        ];
                                        $statusColor = $statusColors[$booking['status']] ?? 'secondary';
                                        $statusLabel = $statusLabels[$booking['status']] ?? ucfirst($booking['status']);
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>"><?= $statusLabel ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <?php if (!empty($booking['catatan'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Catatan
                                </h5>
                                <div class="alert alert-light">
                                    <?= nl2br(esc($booking['catatan'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Status & Actions -->
        <div class="col-lg-4">
            <!-- Status Antrian -->
            <?php if ($antrian): ?>
                <div class="card border-0 shadow mb-4">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list-ol me-2"></i>Status Antrian
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="display-4 fw-bold text-primary"><?= esc($antrian['nomor_antrian']) ?></div>
                            <small class="text-muted">Nomor Antrian Anda</small>
                        </div>

                        <?php
                        $antrianStatusColors = [
                            'menunggu' => 'warning',
                            'diproses' => 'info',
                            'selesai' => 'success'
                        ];
                        $antrianColor = $antrianStatusColors[$antrian['status']] ?? 'secondary';
                        ?>
                        <div class="text-center">
                            <span class="badge bg-<?= $antrianColor ?> fs-6 px-3 py-2">
                                <?= ucfirst($antrian['status']) ?>
                            </span>
                        </div>

                        <?php if ($antrian['jam_mulai']): ?>
                            <div class="mt-3">
                                <small class="text-muted">Mulai:</small>
                                <div class="fw-bold"><?= date('H:i', strtotime($antrian['jam_mulai'])) ?> WIB</div>
                            </div>
                        <?php endif; ?>

                        <?php if ($antrian['jam_selesai']): ?>
                            <div class="mt-2">
                                <small class="text-muted">Selesai:</small>
                                <div class="fw-bold"><?= date('H:i', strtotime($antrian['jam_selesai'])) ?> WIB</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Status Transaksi -->
            <?php if ($transaksi): ?>
                <div class="card border-0 shadow mb-4">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Status Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-check-circle text-success display-4"></i>
                            <div class="mt-2">
                                <strong>LUNAS</strong>
                            </div>
                            <small class="text-muted">No. Transaksi: <?= esc($transaksi['no_transaksi']) ?></small>
                        </div>

                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Total:</td>
                                <td class="fw-bold text-success">Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Metode:</td>
                                <td><?= ucfirst($transaksi['metode_pembayaran']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal:</td>
                                <td><?= date('d/m/Y H:i', strtotime($transaksi['created_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="card border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-tools me-2"></i>Aksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($booking['status'] === 'menunggu_konfirmasi'): ?>
                            <!-- Booking belum dikonfirmasi -->
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Menunggu Konfirmasi</strong><br>
                                <small>Booking Anda sedang menunggu konfirmasi dari admin. Anda dapat melakukan pembayaran setelah booking dikonfirmasi.</small>
                            </div>
                            <a href="<?= site_url('payment/' . $booking['kode_booking']) ?>" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i>Lanjutkan Pembayaran
                            </a>
                            <button class="btn btn-outline-danger" onclick="cancelBooking()">
                                <i class="fas fa-times me-2"></i>Batalkan Booking
                            </button>

                        <?php elseif ($booking['status'] === 'dikonfirmasi' && !$transaksi): ?>
                            <!-- Booking dikonfirmasi tapi belum bayar -->
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Booking Dikonfirmasi</strong><br>
                                <small>Silakan lakukan pembayaran untuk menyelesaikan booking Anda.</small>
                            </div>
                            <a href="<?= site_url('payment/' . $booking['kode_booking']) ?>" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                            </a>
                            <button class="btn btn-outline-danger" onclick="cancelBooking()">
                                <i class="fas fa-times me-2"></i>Batalkan Booking
                            </button>

                        <?php elseif ($transaksi): ?>
                            <!-- Booking sudah dibayar -->
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Pembayaran Berhasil</strong><br>
                                <small>Booking Anda telah dikonfirmasi dan pembayaran berhasil. Silakan datang sesuai jadwal.</small>
                            </div>
                            <a href="<?= site_url('payment/success/' . $transaksi['no_transaksi']) ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-receipt me-2"></i>Lihat Struk Pembayaran
                            </a>

                        <?php elseif ($booking['status'] === 'selesai'): ?>
                            <!-- Booking selesai -->
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-double me-2"></i>
                                <strong>Layanan Selesai</strong><br>
                                <small>Terima kasih telah menggunakan layanan kami!</small>
                            </div>
                            <?php if ($transaksi): ?>
                                <a href="<?= site_url('payment/success/' . $transaksi['no_transaksi']) ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-receipt me-2"></i>Lihat Struk Pembayaran
                                </a>
                            <?php endif; ?>

                        <?php elseif (in_array($booking['status'], ['dibatalkan', 'batal'])): ?>
                            <!-- Booking dibatalkan -->
                            <div class="alert alert-danger mb-3">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Booking Dibatalkan</strong><br>
                                <small>Booking ini telah dibatalkan.</small>
                                <?php if ($booking['catatan']): ?>
                                    <br><strong>Alasan:</strong> <?= esc($booking['catatan']) ?>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <!-- Status tidak dikenali -->
                            <div class="alert alert-secondary mb-3">
                                <i class="fas fa-question-circle me-2"></i>
                                <strong>Status: <?= ucfirst($booking['status']) ?></strong>
                            </div>
                        <?php endif; ?>

                        <!-- Back Button (always available) -->
                        <a href="<?= site_url('pelanggan/booking/history') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment modal removed - payment now handled through dedicated payment page -->

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Payment is now handled through dedicated payment page

    // Cancel booking
    function cancelBooking() {
        Swal.fire({
            title: 'Batalkan Booking?',
            text: 'Apakah Anda yakin ingin membatalkan booking ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= site_url('pelanggan/booking/cancel/' . $booking['id']) ?>', {
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

    // Receipt is now accessible through dedicated payment success page
</script>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0088cc, #00aaff) !important;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #20c997) !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
    }

    .card {
        border-radius: 16px;
    }

    .table td {
        padding: 0.5rem 0;
    }

    @media print {

        .btn,
        .card-header {
            display: none !important;
        }
    }
</style>

<?= $this->endSection() ?>