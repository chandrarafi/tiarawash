<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'TiaraWash - Portal Pelanggan' ?></title>

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
            --gradient-secondary: linear-gradient(135deg, #ff6b35, #ff8c42);
            --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #0088cc 0%, #0066aa 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar-brand {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h3 {
            color: white;
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-section {
            margin-bottom: 2rem;
        }

        .menu-section-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }

        .menu-item {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: #ff6b35;
            transform: translateX(5px);
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: #ff6b35;
        }

        .menu-item i {
            width: 20px;
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-left {
            flex: 1;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .page-subtitle {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }

        /* User Profile Dropdown */
        .user-profile {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-info:hover {
            background: rgba(0, 136, 204, 0.1);
        }

        .user-details h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .user-details small {
            color: #666;
            font-size: 0.75rem;
        }

        /* Content Area */
        .content-wrapper {
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .card-header {
            border-radius: 16px 16px 0 0 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0077b6, #0099dd);
            transform: translateY(-1px);
        }

        /* Notifications */
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                padding: 1rem;
            }

            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Action Cards */
        .action-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
            border-color: var(--primary-color);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .action-icon.booking {
            background: var(--gradient-primary);
        }

        .action-icon.history {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .action-icon.transactions {
            background: linear-gradient(135deg, #ffc107, #ff8c42);
        }

        .action-icon.profile {
            background: linear-gradient(135deg, #6f42c1, #e83e8c);
        }

        .action-card h6 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .action-card p {
            color: #666;
            font-size: 0.85rem;
            margin: 0;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-car-wash me-2"></i>TiaraWash</h3>
            <small>Portal Pelanggan</small>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Menu Utama</div>
                <a href="<?= site_url('pelanggan/dashboard') ?>" class="menu-item <?= (current_url() == site_url('pelanggan/dashboard')) ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="<?= site_url('pelanggan/profile') ?>" class="menu-item <?= (strpos(current_url(), 'pelanggan/profile') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-user-circle"></i>
                    Profil Saya
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Monitoring</div>

                <a href="<?= site_url('pelanggan/booking/history') ?>" class="menu-item <?= (strpos(current_url(), 'pelanggan/booking/history') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-history"></i>
                    Riwayat Booking
                </a>
            </div>


        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="d-none d-md-block">
                    <h4 class="page-title"><?= $title ?? 'Dashboard' ?></h4>
                    <p class="page-subtitle"><?= $subtitle ?? 'Portal Pelanggan TiaraWash' ?></p>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Notifications -->
                <div class="position-relative">
                    <button class="btn btn-link text-dark" onclick="showNotifications()">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="notification-badge">3</span>
                    </button>
                </div>

                <!-- User Profile -->
                <div class="dropdown">
                    <div class="user-info" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <?= strtoupper(substr(session()->get('name') ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="user-details d-none d-md-block">
                            <h6><?= session()->get('name') ?? 'User' ?></h6>
                            <small>Pelanggan</small>
                        </div>
                        <i class="fas fa-chevron-down ms-2 d-none d-md-block"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= site_url('pelanggan/profile') ?>">
                                <i class="fas fa-user-cog me-2"></i>Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="showSettings()">
                                <i class="fas fa-cog me-2"></i>Pengaturan
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="handleLogout()">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Show notifications
        function showNotifications() {
            Swal.fire({
                title: 'Notifikasi',
                html: `
                    <div class="text-start">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Booking BK-20250730-001 telah dikonfirmasi
                        </div>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Pembayaran berhasil diproses
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            Antrian Anda: A20250730001
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                width: 600
            });
        }

        // Show transaction history
        function showTransactionHistory() {
            Swal.fire({
                title: 'Riwayat Transaksi',
                text: 'Fitur ini akan segera tersedia',
                icon: 'info',
                confirmButtonColor: '#0088cc'
            });
        }

        // Show settings
        function showSettings() {
            Swal.fire({
                title: 'Pengaturan',
                text: 'Fitur pengaturan akan segera tersedia',
                icon: 'info',
                confirmButtonColor: '#0088cc'
            });
        }

        // Handle logout
        function handleLogout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar dari akun?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0088cc',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url('auth/logout') ?>';
                }
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isToggleButton = event.target.closest('[onclick="toggleSidebar()"]');

            if (window.innerWidth <= 768 && !isClickInsideSidebar && !isToggleButton) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>