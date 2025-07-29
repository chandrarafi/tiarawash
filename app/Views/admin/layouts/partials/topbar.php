<!-- Top Navbar -->
<div class="topbar">
    <!-- Brand on mobile -->
    <div class="d-lg-none">
        <a href="<?= base_url('admin') ?>" class="text-decoration-none d-flex align-items-center">
            <div class="brand-icon me-2">
                <i class="fas fa-car-wash"></i>
            </div>
            <span class="brand-text">TiaraWash</span>
        </a>
    </div>

    <!-- Spacer to push items to right -->
    <div class="flex-grow-1"></div>

    <!-- Right navbar content -->
    <div class="d-flex align-items-center gap-2">

        <?php if (session()->get('role') === 'pelanggan'): ?>
            <!-- Profile Link - Untuk Pelanggan -->
            <div class="topbar-item">
                <a class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" href="<?= site_url('pelanggan/profile') ?>">
                    <i class="bi bi-person-circle me-1"></i>
                    <span class="d-none d-md-inline fw-semibold">Profil Saya</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Notifications -->
        <div class="topbar-item dropdown position-relative">
            <button class="topbar-btn position-relative"
                type="button" id="alertsDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                title="Notifikasi">
                <i class="bi bi-bell-fill"></i>
                <span class="notification-badge">3</span>
                <div class="btn-ripple"></div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end modern-dropdown"
                style="width: 340px; z-index: 99999;">
                <li class="dropdown-header modern-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-bell-fill me-2 text-primary"></i>Notifikasi
                        </h6>
                        <span class="badge bg-primary rounded-pill">3</span>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon bg-primary">
                                <i class="bi bi-file-earmark-text text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notification-title">Laporan Bulanan Tersedia</div>
                                <div class="notification-desc">Laporan penjualan bulan Juni telah siap diunduh</div>
                                <div class="notification-time">
                                    <i class="bi bi-clock me-1"></i>2 jam yang lalu
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon bg-success">
                                <i class="bi bi-check-circle text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notification-title">Pesanan Selesai</div>
                                <div class="notification-desc">10 pesanan cuci mobil telah selesai diproses</div>
                                <div class="notification-time">
                                    <i class="bi bi-clock me-1"></i>5 jam yang lalu
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon bg-warning">
                                <i class="bi bi-exclamation-triangle text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notification-title">Stok Perlengkapan Menipis</div>
                                <div class="notification-desc">Sabun cuci mobil tersisa 5 unit</div>
                                <div class="notification-time">
                                    <i class="bi bi-clock me-1"></i>1 hari yang lalu
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <a class="dropdown-item text-center text-primary py-3 fw-semibold" href="#">
                        <i class="bi bi-eye me-1"></i>Lihat Semua Notifikasi
                    </a>
                </li>
            </ul>
        </div>

        <!-- Messages -->
        <div class="topbar-item dropdown position-relative">
            <button class="topbar-btn position-relative"
                type="button" id="messagesDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                title="Pesan">
                <i class="bi bi-chat-dots-fill"></i>
                <span class="notification-badge">7</span>
                <div class="btn-ripple"></div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end modern-dropdown"
                style="width: 380px; z-index: 99999;">
                <li class="dropdown-header modern-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-chat-dots-fill me-2 text-primary"></i>Pesan
                        </h6>
                        <span class="badge bg-primary rounded-pill">7</span>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="d-flex align-items-start">
                            <div class="message-avatar">
                                <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=0088cc&color=ffffff"
                                    alt="Budi" class="rounded-circle">
                                <div class="online-indicator"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="message-sender">Budi Santoso</div>
                                <div class="message-preview">Halo admin, pesanan cuci mobil saya sudah selesai belum ya?</div>
                                <div class="message-time">
                                    <i class="bi bi-clock me-1"></i>15 menit yang lalu
                                </div>
                            </div>
                            <div class="message-status">
                                <span class="badge bg-danger rounded-pill">Baru</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="d-flex align-items-start">
                            <div class="message-avatar">
                                <img src="https://ui-avatars.com/api/?name=Dewi+Anggraini&background=28a745&color=ffffff"
                                    alt="Dewi" class="rounded-circle">
                                <div class="offline-indicator"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="message-sender">Dewi Anggraini</div>
                                <div class="message-preview">Terima kasih pelayanannya sangat memuaskan!</div>
                                <div class="message-time">
                                    <i class="bi bi-clock me-1"></i>2 jam yang lalu
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="d-flex align-items-start">
                            <div class="message-avatar">
                                <img src="https://ui-avatars.com/api/?name=Ahmad+Rizki&background=ffc107&color=000000"
                                    alt="Ahmad" class="rounded-circle">
                                <div class="online-indicator"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="message-sender">Ahmad Rizki</div>
                                <div class="message-preview">Saya mau booking untuk hari Sabtu bisa?</div>
                                <div class="message-time">
                                    <i class="bi bi-clock me-1"></i>1 hari yang lalu
                                </div>
                            </div>
                            <div class="message-status">
                                <span class="badge bg-success rounded-pill">Dibaca</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <a class="dropdown-item text-center text-primary py-3 fw-semibold" href="#">
                        <i class="bi bi-chat-square-text me-1"></i>Buka Chat Lengkap
                    </a>
                </li>
            </ul>
        </div>

        <!-- Divider -->
        <div class="topbar-divider"></div>

        <!-- User Profile -->
        <div class="topbar-item dropdown position-relative">
            <button class="user-profile-btn d-flex align-items-center"
                type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-info me-3 d-none d-lg-block text-end">
                    <div class="user-name"><?= session()->get('name') ?></div>
                    <div class="user-role">
                        <?php
                        $role = session()->get('role');
                        $roleNames = [
                            'admin' => 'Administrator',
                            'pimpinan' => 'Pimpinan',
                            'pelanggan' => 'Pelanggan'
                        ];
                        echo $roleNames[$role] ?? ucfirst($role);
                        ?>
                    </div>
                </div>
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=0088cc&color=ffffff"
                        alt="Profile" class="rounded-circle">
                    <div class="avatar-status"></div>
                </div>
                <i class="bi bi-chevron-down ms-2 dropdown-arrow"></i>
                <div class="btn-ripple"></div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end modern-dropdown user-dropdown"
                style="width: 280px; z-index: 99999;">
                <li class="dropdown-header modern-header">
                    <div class="d-flex align-items-center">
                        <div class="header-avatar me-3">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=0088cc&color=ffffff"
                                alt="Profile" class="rounded-circle">
                            <div class="avatar-status"></div>
                        </div>
                        <div>
                            <div class="header-name"><?= session()->get('name') ?></div>
                            <div class="header-role">
                                <?php
                                $role = session()->get('role');
                                $roleNames = [
                                    'admin' => 'Administrator',
                                    'pimpinan' => 'Pimpinan',
                                    'pelanggan' => 'Pelanggan'
                                ];
                                echo $roleNames[$role] ?? ucfirst($role);
                                ?>
                            </div>
                            <div class="header-status">
                                <i class="bi bi-circle-fill text-success me-1" style="font-size: 8px;"></i>
                                Online
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="menu-icon bg-primary">
                            <i class="bi bi-person text-white"></i>
                        </div>
                        <div class="menu-content">
                            <div class="menu-title">Profil Saya</div>
                            <div class="menu-desc">Kelola informasi akun Anda</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="menu-icon bg-secondary">
                            <i class="bi bi-gear text-white"></i>
                        </div>
                        <div class="menu-content">
                            <div class="menu-title">Pengaturan</div>
                            <div class="menu-desc">Kustomisasi preferensi sistem</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item modern-item" href="#">
                        <div class="menu-icon bg-info">
                            <i class="bi bi-list-check text-white"></i>
                        </div>
                        <div class="menu-content">
                            <div class="menu-title">Log Aktivitas</div>
                            <div class="menu-desc">Riwayat aktivitas akun</div>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <button class="dropdown-item modern-item logout-btn border-0 bg-transparent w-100 text-start" id="btn-logout">
                        <div class="menu-icon bg-danger">
                            <i class="bi bi-box-arrow-right text-white"></i>
                        </div>
                        <div class="menu-content">
                            <div class="menu-title text-danger">Keluar</div>
                            <div class="menu-desc">Logout dari sistem</div>
                        </div>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    /* Modern Topbar Styling */
    .topbar {
        position: relative !important;
        z-index: 9998 !important;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%) !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 136, 204, 0.1);
        height: 70px;
        display: flex;
        align-items: center;
        padding: 0 2rem;
        margin-bottom: 1.5rem;
        border-radius: 15px;
    }

    /* Brand Styling */
    .brand-icon {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #0088cc, #00aaff);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(0, 136, 204, 0.3);
    }

    .brand-text {
        font-weight: 700;
        font-size: 18px;
        background: linear-gradient(135deg, #0088cc, #00aaff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Topbar Buttons */
    .topbar-btn {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        border: none;
        background: rgba(0, 136, 204, 0.1);
        color: #0088cc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .topbar-btn:hover {
        background: linear-gradient(135deg, #0088cc, #00aaff);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 136, 204, 0.3);
    }

    .topbar-btn:active {
        transform: translateY(0px);
    }

    /* Ripple Effect */
    .btn-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: linear-gradient(135deg, #dc3545, #ff6b7a);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    /* User Profile Button */
    .user-profile-btn {
        background: rgba(0, 136, 204, 0.05);
        border: 1px solid rgba(0, 136, 204, 0.1);
        border-radius: 25px;
        padding: 8px 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .user-profile-btn:hover {
        background: rgba(0, 136, 204, 0.1);
        border-color: rgba(0, 136, 204, 0.2);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 136, 204, 0.15);
    }

    .user-info .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        line-height: 1.2;
    }

    .user-info .user-role {
        font-size: 12px;
        color: #6c757d;
        line-height: 1.2;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        position: relative;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border: 2px solid rgba(0, 136, 204, 0.2);
    }

    .avatar-status {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background: #28a745;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
    }

    .dropdown-arrow {
        color: #6c757d;
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .user-profile-btn[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* Modern Dropdown */
    .modern-dropdown {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15) !important;
        padding: 0 !important;
        margin-top: 8px !important;
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95) !important;
        overflow: hidden;
        animation: dropdownFadeIn 0.3s ease;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modern-header {
        background: linear-gradient(135deg, #f8f9fc, #e9ecef) !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px !important;
        margin: 0 !important;
        border: none !important;
    }

    .modern-item {
        padding: 15px 20px !important;
        border: none !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
    }

    .modern-item:hover {
        background: rgba(0, 136, 204, 0.05) !important;
        transform: translateX(5px);
    }

    /* Notification Items */
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 16px;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .notification-desc {
        font-size: 12px;
        color: #6c757d;
        line-height: 1.4;
        margin-bottom: 6px;
    }

    .notification-time {
        font-size: 11px;
        color: #adb5bd;
        display: flex;
        align-items: center;
    }

    /* Message Items */
    .message-avatar {
        width: 45px;
        height: 45px;
        position: relative;
        margin-right: 15px;
    }

    .message-avatar img {
        width: 100%;
        height: 100%;
        border: 2px solid rgba(0, 136, 204, 0.1);
    }

    .online-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background: #28a745;
        border: 2px solid white;
        border-radius: 50%;
    }

    .offline-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background: #6c757d;
        border: 2px solid white;
        border-radius: 50%;
    }

    .message-sender {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .message-preview {
        font-size: 12px;
        color: #6c757d;
        line-height: 1.4;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .message-time {
        font-size: 11px;
        color: #adb5bd;
        display: flex;
        align-items: center;
    }

    .message-status {
        margin-left: auto;
    }

    /* User Dropdown Menu Items */
    .menu-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 14px;
    }

    .menu-content .menu-title {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .menu-content .menu-desc {
        font-size: 11px;
        color: #6c757d;
        line-height: 1.3;
    }

    .header-avatar {
        width: 50px;
        height: 50px;
        position: relative;
    }

    .header-avatar img {
        width: 100%;
        height: 100%;
        border: 3px solid rgba(0, 136, 204, 0.2);
    }

    .header-name {
        font-weight: 700;
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .header-role {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .header-status {
        font-size: 11px;
        color: #28a745;
        display: flex;
        align-items: center;
    }

    /* Topbar Divider */
    .topbar-divider {
        width: 1px;
        height: 30px;
        background: linear-gradient(to bottom, transparent, rgba(0, 136, 204, 0.2), transparent);
        margin: 0 15px;
    }

    /* Dropdown positioning */
    .topbar .dropdown-menu {
        position: absolute !important;
        z-index: 99999 !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
    }

    .topbar .dropdown-menu[data-bs-popper] {
        position: absolute !important;
        z-index: 99999 !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .topbar {
            padding: 0 1rem;
        }

        .topbar-btn {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .modern-dropdown {
            width: 300px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ripple effect for buttons
        document.querySelectorAll('.topbar-btn, .user-profile-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = this.querySelector('.btn-ripple');
                if (ripple) {
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.opacity = '1';

                    setTimeout(() => {
                        ripple.style.transform = 'scale(4)';
                        ripple.style.opacity = '0';
                    }, 10);
                }
            });
        });

        // Logout dengan SweetAlert
        const logoutBtn = document.getElementById('btn-logout');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar dari sistem?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0088cc',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Ya, Keluar',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-3 shadow-lg',
                        confirmButton: 'btn btn-primary me-2 rounded-pill',
                        cancelButton: 'btn btn-secondary rounded-pill'
                    },
                    buttonsStyling: false,
                    zIndex: 99999,
                    backdrop: 'rgba(0,0,0,0.4)',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sedang Logout...',
                            text: 'Mohon tunggu sebentar',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            zIndex: 99999,
                            customClass: {
                                popup: 'rounded-3 shadow-lg'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        setTimeout(() => {
                            window.location.href = '<?= base_url('auth/logout') ?>';
                        }, 1500);
                    }
                });
            });
        }

        // Force dropdown positioning
        document.querySelectorAll('.topbar .dropdown').forEach(function(dropdown) {
            dropdown.addEventListener('shown.bs.dropdown', function() {
                const menu = this.querySelector('.dropdown-menu');
                if (menu) {
                    menu.style.position = 'absolute';
                    menu.style.zIndex = '99999';
                    menu.style.top = '100%';
                    menu.style.right = '0';
                    menu.style.left = 'auto';
                    menu.style.transform = 'none';
                }
            });
        });

        // Auto-hide notifications after reading
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                this.style.opacity = '0.7';
            });
        });
    });
</script>