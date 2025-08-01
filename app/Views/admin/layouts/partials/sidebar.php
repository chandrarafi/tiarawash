<aside class="sidebar" style="height: 100vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;">
    <div class="sidebar-header">
        <div class="text-center">
            <h4 class="fw-bold text-primary mb-0 text-white">TiaraWash</h4>
            <small class="text-white">
                <?php
                $userRole = session()->get('role');
                switch ($userRole) {
                    case 'admin':
                        echo 'Admin Panel';
                        break;
                    case 'pimpinan':
                        echo 'Dashboard Pimpinan';
                        break;
                    case 'pelanggan':
                        echo 'Portal Pelanggan';
                        break;
                    default:
                        echo 'Dashboard';
                }
                ?>
            </small>
        </div>
    </div>
    <div class="sidebar-menu" style="padding-bottom: 2rem;">
        <ul class="nav flex-column">
            <!-- Dashboard - Semua Role -->
            <li class="nav-item">
                <a href="<?= base_url('admin/') ?>" class="nav-link <?= isset($active) && $active == 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <?php if (session()->get('role') == 'admin') : ?>
                <!-- Menu Admin -->
                <li class="nav-item">
                    <a href="<?= base_url('admin/users') ?>" class="nav-link <?= isset($active) && $active == 'users' ? 'active' : '' ?>">
                        <i class="bi bi-person-gear"></i> Manajemen User
                    </a>
                </li>

                <!-- Data Master -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white">
                        <span>Data Master</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/karyawan') ?>" class="nav-link <?= isset($active) && $active == 'karyawan' ? 'active' : '' ?>">
                        <i class="bi bi-people-fill"></i> Data Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'pelanggan' ? 'active' : '' ?>" href="<?= base_url('admin/pelanggan') ?>">
                        <i class="bi bi-people"></i> Data Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/layanan') ?>" class="nav-link <?= isset($active) && $active == 'layanan' ? 'active' : '' ?>">
                        <i class="bi bi-tools"></i> Layanan Cucian
                    </a>
                </li>


                <!-- Operasional -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Operasional</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/booking') ?>" class="nav-link <?= isset($active) && $active == 'booking' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i> Manajemen Booking
                        <?php
                        // Get pending payment count for badge
                        $transaksiModel = new \App\Models\TransaksiModel();
                        $pendingCount = $transaksiModel->where('status_pembayaran', 'belum_bayar')
                            ->where('bukti_pembayaran IS NOT NULL')
                            ->countAllResults();
                        if ($pendingCount > 0): ?>
                            <span class="badge bg-warning ms-2"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/antrian') ?>" class="nav-link <?= isset($active) && $active == 'antrian' ? 'active' : '' ?>">
                        <i class="bi bi-list-ol"></i> Antrian Cucian
                    </a>
                </li>



                <!-- Inventaris -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Inventaris</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'perlengkapan' ? 'active' : '' ?>" href="<?= base_url('admin/perlengkapan') ?>">
                        <i class="bi bi-box"></i> Perlengkapan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'pembelian' ? 'active' : '' ?>" href="<?= base_url('admin/pembelian') ?>">
                        <i class="bi bi-cart-plus"></i> Pembelian Perlengkapan
                    </a>
                </li>

                <!-- Laporan -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Laporan</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-pembelian' ? 'active' : '' ?>" href="<?= base_url('admin/pembelian/laporan') ?>">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/report') ?>">
                        <i class="bi bi-graph-up"></i> Laporan Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-booking' ? 'active' : '' ?>" href="<?= base_url('admin/booking/laporan') ?>">
                        <i class="bi bi-calendar-week"></i> Laporan Booking
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-booking-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/booking/laporan-perbulan') ?>">
                        <i class="bi bi-calendar-month"></i> Laporan Booking PerBulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-perlengkapan-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/perlengkapan/laporan-perbulan') ?>">
                        <i class="bi bi-box-seam"></i> Laporan Perlengkapan PerBulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-keuangan-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/keuangan/laporan-perbulan') ?>">
                        <i class="bi bi-cash-stack"></i> Laporan Keuangan PerBulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-keuangan-pertahun' ? 'active' : '' ?>" href="<?= base_url('admin/keuangan/laporan-pertahun') ?>">
                        <i class="bi bi-calendar-year"></i> Laporan Keuangan PerTahun
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-pelanggan' ? 'active' : '' ?>" href="<?= base_url('admin/pelanggan/laporan') ?>">
                        <i class="bi bi-people"></i> Laporan Data Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-karyawan' ? 'active' : '' ?>" href="<?= base_url('admin/karyawan/laporan') ?>">
                        <i class="bi bi-person-badge"></i> Laporan Data Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-antrian' ? 'active' : '' ?>" href="<?= base_url('admin/antrian/laporan') ?>">
                        <i class="bi bi-list-ol"></i> Laporan Antrian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi-pertanggal' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/laporan-pertanggal') ?>">
                        <i class="bi bi-receipt-cutoff"></i> Laporan Transaksi Pertanggal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/laporan-perbulan') ?>">
                        <i class="bi bi-receipt"></i> Laporan Transaksi Perbulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi-pertahun' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/laporan-pertahun') ?>">
                        <i class="bi bi-calendar2-check"></i> Laporan Transaksi Pertahun
                    </a>
                </li>

            <?php elseif (session()->get('role') == 'pimpinan') : ?>
                <!-- Menu Pimpinan -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Monitoring</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/booking') ?>" class="nav-link <?= isset($active) && $active == 'booking' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i> Data Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/antrian') ?>" class="nav-link <?= isset($active) && $active == 'antrian' ? 'active' : '' ?>">
                        <i class="bi bi-list-ol"></i> Antrian Cucian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('admin/transaksi') ?>" class="nav-link <?= isset($active) && $active == 'transaksi' ? 'active' : '' ?>">
                        <i class="bi bi-receipt"></i> Transaksi
                    </a>
                </li>

                <!-- Laporan -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Laporan</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/report') ?>">
                        <i class="bi bi-graph-up"></i> Laporan Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-pembelian' ? 'active' : '' ?>" href="<?= base_url('admin/pembelian/laporan') ?>">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-booking' ? 'active' : '' ?>" href="<?= base_url('admin/booking/laporan') ?>">
                        <i class="bi bi-calendar-week"></i> Laporan Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-booking-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/booking/laporan-perbulan') ?>">
                        <i class="bi bi-calendar-month"></i> Laporan Booking PerBulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-perlengkapan-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/perlengkapan/laporan-perbulan') ?>">
                        <i class="bi bi-box-seam"></i> Laporan Perlengkapan PerBulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-keuangan-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/keuangan/laporan-perbulan') ?>">
                        <i class="bi bi-cash-stack"></i> Laporan Keuangan PerBulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-keuangan-pertahun' ? 'active' : '' ?>" href="<?= base_url('admin/keuangan/laporan-pertahun') ?>">
                        <i class="bi bi-calendar-year"></i> Laporan Keuangan PerTahun
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-pelanggan' ? 'active' : '' ?>" href="<?= base_url('admin/pelanggan/laporan') ?>">
                        <i class="bi bi-people"></i> Laporan Data Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-karyawan' ? 'active' : '' ?>" href="<?= base_url('admin/karyawan/laporan') ?>">
                        <i class="bi bi-person-badge"></i> Laporan Data Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-antrian' ? 'active' : '' ?>" href="<?= base_url('admin/antrian/laporan') ?>">
                        <i class="bi bi-list-ol"></i> Laporan Antrian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi-pertanggal' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/laporan-pertanggal') ?>">
                        <i class="bi bi-receipt-cutoff"></i> Laporan Transaksi Pertanggal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi-perbulan' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/laporan-perbulan') ?>">
                        <i class="bi bi-receipt"></i> Laporan Transaksi Perbulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($active) && $active == 'laporan-transaksi-pertahun' ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/laporan-pertahun') ?>">
                        <i class="bi bi-calendar2-check"></i> Laporan Transaksi Pertahun
                    </a>
                </li>

            <?php elseif (session()->get('role') == 'pelanggan') : ?>
                <!-- Menu Pelanggan -->
                <li class="nav-item">
                    <a href="<?= base_url('pelanggan/profile') ?>" class="nav-link <?= isset($active) && $active == 'profile' ? 'active' : '' ?>">
                        <i class="bi bi-person-circle"></i> Profil Saya
                    </a>
                </li>

                <!-- Kendaraan -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Kendaraan</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('pelanggan/kendaraan') ?>" class="nav-link <?= isset($active) && $active == 'kendaraan' ? 'active' : '' ?>">
                        <i class="bi bi-car-front"></i> Kendaraan Saya
                    </a>
                </li>

                <!-- Booking -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Booking</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('pelanggan/booking/create') ?>" class="nav-link <?= isset($active) && $active == 'booking-create' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-plus"></i> Buat Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('pelanggan/booking/history') ?>" class="nav-link <?= isset($active) && $active == 'booking-history' ? 'active' : '' ?>">
                        <i class="bi bi-clock-history"></i> Riwayat Booking
                    </a>
                </li>

                <!-- Transaksi -->
                <li class="nav-item">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Transaksi</span>
                    </h6>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('pelanggan/transaksi/history') ?>" class="nav-link <?= isset($active) && $active == 'transaksi-history' ? 'active' : '' ?>">
                        <i class="bi bi-receipt"></i> Riwayat Transaksi
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</aside>