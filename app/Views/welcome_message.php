<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiaraWash - Layanan Cuci Kendaraan Premium</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom Fonts -->
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }

        .navbar-custom.scrolled {
            padding: 0.5rem 0;
            background: rgba(255, 255, 255, 0.98);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: var(--gradient-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .btn-primary-custom {
            background: var(--gradient-primary);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
            color: white;
        }

        /* Professional User Dropdown */
        .user-dropdown {
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
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .user-avatar:hover {
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-info:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .dropdown-menu-custom {
            background: white;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            padding: 8px;
            margin-top: 8px;
            min-width: 200px;
        }

        .dropdown-item-custom {
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 500;
            color: var(--dark-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            margin-bottom: 4px;
        }

        .dropdown-item-custom:hover {
            background: var(--light-color);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .dropdown-item-custom.danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .dropdown-divider-custom {
            height: 1px;
            background: #e5e7eb;
            border: none;
            margin: 8px 0;
        }

        /* Notification Badge */
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

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 2rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .hero-stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.8;
        }

        /* Features Section */
        .features-section {
            padding: 8rem 0;
            background: var(--light-color);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 136, 204, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-medium);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .feature-description {
            color: #666;
            line-height: 1.8;
        }

        /* Services Section */
        .services-section {
            padding: 8rem 0;
            background: white;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 136, 204, 0.1);
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .service-image {
            height: 250px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }

        .service-content {
            padding: 2rem;
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .service-description {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .service-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .service-features {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .service-features li {
            padding: 0.5rem 0;
            color: #666;
            position: relative;
            padding-left: 2rem;
        }

        .service-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 8rem 0;
            background: var(--light-color);
        }

        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            border: 1px solid rgba(0, 136, 204, 0.1);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .testimonial-quote {
            font-size: 1.2rem;
            color: #666;
            font-style: italic;
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .testimonial-info h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .testimonial-info small {
            color: #666;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            background: var(--gradient-primary);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.3rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }

        .btn-white-custom {
            background: white;
            color: var(--primary-color);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            display: inline-block;
            margin: 0 1rem;
        }

        .btn-white-custom:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
            color: var(--primary-color);
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-content {
            margin-bottom: 3rem;
        }

        .footer-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .cta-title {
                font-size: 2rem;
            }
        }

        /* Animations */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .user-info {
                background: white;
                border: 1px solid #e5e7eb;
            }

            .user-name {
                color: var(--dark-color);
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-car-wash me-2"></i>TiaraWash
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimoni">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                </ul>

                <div class="navbar-nav">
                    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                        <!-- User is logged in - show profile dropdown -->
                        <div class="dropdown user-dropdown">
                            <div class="user-info" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    <?php if (isset($user['unread_notifications']) && $user['unread_notifications'] > 0): ?>
                                        <span class="notification-badge"><?= $user['unread_notifications'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-none d-lg-block">
                                    <div class="user-name"><?= esc($user['name']) ?></div>
                                    <div class="user-role"><?= ucfirst($user['role']) ?></div>
                                </div>
                                <i class="fas fa-chevron-down ms-2"></i>
                            </div>

                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                <?php if ($user['role'] === 'pelanggan'): ?>
                                    <li>
                                        <a class="dropdown-item-custom" href="<?= site_url('pelanggan/dashboard') ?>">
                                            <i class="fas fa-tachometer-alt"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item-custom" href="<?= site_url('booking') ?>">
                                            <i class="fas fa-calendar-plus"></i>
                                            Booking Baru
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item-custom" href="<?= site_url('pelanggan/booking/history') ?>">
                                            <i class="fas fa-history"></i>
                                            Riwayat Booking
                                        </a>
                                    </li>

                                <?php elseif ($user['role'] === 'admin' || $user['role'] === 'pimpinan'): ?>
                                    <li>
                                        <a class="dropdown-item-custom" href="<?= site_url('admin/dashboard') ?>">
                                            <i class="fas fa-tachometer-alt"></i>
                                            Dashboard Admin
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item-custom" href="<?= site_url('admin/layanan') ?>">
                                            <i class="fas fa-cogs"></i>
                                            Kelola Layanan
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item-custom" href="<?= site_url('admin/pelanggan') ?>">
                                            <i class="fas fa-users"></i>
                                            Kelola Pelanggan
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider-custom">
                                </li>




                                <li>
                                    <hr class="dropdown-divider-custom">
                                </li>

                                <li>
                                    <a class="dropdown-item-custom danger" href="#" onclick="handleLogout()">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- User is not logged in - show login/register buttons -->
                        <a href="<?= site_url('auth') ?>" class="btn-primary-custom me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </a>
                        <a href="<?= site_url('auth/register') ?>" class="btn btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1 class="hero-title">Layanan Cuci Kendaraan <span style="color: #ff6b35;">Premium</span></h1>
                    <p class="hero-subtitle">
                        Nikmati pengalaman cuci kendaraan terbaik dengan teknologi modern,
                        pelayanan profesional, dan hasil yang memuaskan di TiaraWash.
                    </p>

                    <div class="d-flex gap-3 flex-wrap">
                        <?php if (isset($isLoggedIn) && $isLoggedIn && $user['role'] === 'pelanggan'): ?>
                            <a href="<?= site_url('booking') ?>" class="btn-white-custom">
                                <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                            </a>
                            <a href="<?= site_url('pelanggan/dashboard') ?>" class="btn btn-outline-light rounded-pill px-4 py-3">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Saya
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('booking') ?>" class="btn-white-custom">
                                <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="hero-stats" data-aos="fade-up" data-aos-delay="200">
                        <div class="row">
                            <div class="col-4 stat-item">
                                <span class="stat-number"><?= $stats['total_customers'] ?>+</span>
                                <span class="stat-label">Pelanggan Puas</span>
                            </div>
                            <div class="col-4 stat-item">
                                <span class="stat-number"><?= $stats['total_services'] ?>+</span>
                                <span class="stat-label">Jenis Layanan</span>
                            </div>
                            <div class="col-4 stat-item">
                                <span class="stat-number">24/7</span>
                                <span class="stat-label">Customer Support</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center" data-aos="fade-left">
                    <div class="floating">
                        <img src="<?= base_url('images/hero.png') ?>"
                            alt="TiaraWash Car Washing Service"
                            style="max-width: 100%; height: auto; max-height: 500px; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="tentang" class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-12" data-aos="fade-up">
                    <h2 class="section-title">Mengapa Memilih TiaraWash?</h2>
                    <p class="section-subtitle">
                        Kami berkomitmen memberikan layanan cuci kendaraan terbaik dengan
                        teknologi modern dan pelayanan yang memuaskan.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="feature-title">Booking Online 24/7</h3>
                        <p class="feature-description">
                            Pesan layanan cuci kendaraan kapan saja dengan sistem booking online
                            yang mudah dan praktis. Tidak perlu antri lama!
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Teknologi Modern</h3>
                        <p class="feature-description">
                            Menggunakan peralatan cuci terdepan dengan teknologi steam cleaning
                            dan produk pembersih berkualitas tinggi yang aman untuk kendaraan.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Tenaga Profesional</h3>
                        <p class="feature-description">
                            Tim cuci berpengalaman dan terlatih yang memahami cara merawat
                            setiap jenis kendaraan dengan teliti dan hati-hati.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3 class="feature-title">Ramah Lingkungan</h3>
                        <p class="feature-description">
                            Menggunakan produk pembersih yang ramah lingkungan dan sistem
                            daur ulang air untuk menjaga kelestarian alam.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 class="feature-title">Harga Terjangkau</h3>
                        <p class="feature-description">
                            Menawarkan berbagai paket layanan dengan harga yang kompetitif
                            dan sesuai dengan kualitas layanan premium yang diberikan.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3 class="feature-title">Garansi Kepuasan</h3>
                        <p class="feature-description">
                            Memberikan garansi kepuasan 100% dengan layanan after-sales
                            dan komitmen untuk memberikan hasil terbaik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-12" data-aos="fade-up">
                    <h2 class="section-title">Paket Layanan Kami</h2>
                    <p class="section-subtitle">
                        Pilih paket layanan yang sesuai dengan kebutuhan kendaraan Anda.
                        Semua paket dilengkapi dengan jaminan kualitas terbaik.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <?php
                $delay = 100;
                foreach ($services as $service):
                    // Set vehicle icon based on type
                    $vehicleIcons = [
                        'motor' => 'fas fa-motorcycle',
                        'mobil' => 'fas fa-car',
                        'lainnya' => 'fas fa-truck'
                    ];
                    $icon = $vehicleIcons[strtolower($service['jenis_kendaraan'])] ?? 'fas fa-car-wash';
                ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="service-card">
                            <div class="service-image">
                                <?php if (!empty($service['foto'])): ?>
                                    <img src="<?= base_url('uploads/layanan/' . $service['foto']); ?>"
                                        alt="<?= esc($service['nama_layanan']) ?>"
                                        style="width: 100%; height: 250px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="<?= $icon ?>"></i>
                                <?php endif; ?>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title"><?= esc($service['nama_layanan']) ?></h3>
                                <p class="service-description">
                                    <?= esc($service['deskripsi']) ?>
                                </p>
                                <div class="service-price">Rp <?= number_format($service['harga'], 0, ',', '.') ?></div>
                                <ul class="service-features">
                                    <li>Jenis Kendaraan: <?= ucfirst($service['jenis_kendaraan']) ?></li>
                                    <li>Durasi: <?= $service['durasi_menit'] ?> menit</li>
                                    <li>Status: <?= ucfirst($service['status']) ?></li>
                                    <li>Kode Layanan: <?= $service['kode_layanan'] ?></li>
                                </ul>
                                <a href="<?= site_url('booking') ?>" class="btn-primary-custom w-100">
                                    <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay += 100; // Increment delay for staggered animation
                endforeach;
                ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="testimonials-section">
        <div class="container">
            <div class="row">
                <div class="col-12" data-aos="fade-up">
                    <h2 class="section-title">Apa Kata Pelanggan Kami?</h2>
                    <p class="section-subtitle">
                        Kepuasan pelanggan adalah prioritas utama kami.
                        Berikut testimoni dari pelanggan yang telah merasakan layanan TiaraWash.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <p class="testimonial-quote">
                            "Pelayanan sangat memuaskan! Motor saya jadi kinclong dan bersih banget.
                            Sistem booking online juga memudahkan, tidak perlu antri lama."
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">BS</div>
                            <div class="testimonial-info">
                                <h5>Budi Santoso</h5>
                                <small>Pelanggan Motor</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <p class="testimonial-quote">
                            "TiaraWash benar-benar professional! Mobil saya dicuci dengan teliti,
                            interior juga dibersihkan sampai bersih. Harganya juga reasonable."
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">SA</div>
                            <div class="testimonial-info">
                                <h5>Sari Andini</h5>
                                <small>Pelanggan Mobil</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card">
                        <p class="testimonial-quote">
                            "Sudah langganan di TiaraWash hampir 2 tahun. Kualitas konsisten,
                            pelayanan ramah, dan hasilnya selalu memuaskan. Highly recommended!"
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">AR</div>
                            <div class="testimonial-info">
                                <h5>Ahmad Rizki</h5>
                                <small>Pelanggan Setia</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="testimonial-card">
                        <p class="testimonial-quote">
                            "Truk perusahaan kami selalu dicuci di TiaraWash. Mereka punya
                            pengalaman menangani kendaraan besar dengan hasil yang sangat baik."
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">DW</div>
                            <div class="testimonial-info">
                                <h5>Dewi Wulandari</h5>
                                <small>Manager Fleet</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="testimonial-card">
                        <p class="testimonial-quote">
                            "Aplikasi booking-nya user friendly banget! Bisa pilih waktu dan
                            jenis layanan dengan mudah. Prosesnya juga cepat dan efisien."
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">RH</div>
                            <div class="testimonial-info">
                                <h5>Rina Hartanti</h5>
                                <small>Tech Enthusiast</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="testimonial-card">
                        <p class="testimonial-quote">
                            "Customer service nya responsif dan helpful. Ketika ada masalah,
                            langsung ditangani dengan baik. Benar-benar service excellent!"
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">MF</div>
                            <div class="testimonial-info">
                                <h5>Maya Fitri</h5>
                                <small>Entrepreneur</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="cta-title">Siap Merasakan Layanan Premium Kami?</h2>
            <p class="cta-subtitle">
                Bergabunglah dengan ribuan pelanggan yang telah merasakan kepuasan
                layanan cuci kendaraan terbaik di TiaraWash.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?= site_url('auth/register') ?>" class="btn-white-custom">
                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                </a>
                <a href="<?= site_url('auth') ?>" class="btn btn-outline-light rounded-pill px-4 py-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Sudah Punya Akun?
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="footer">
        <div class="container">
            <div class="row footer-content">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h3 class="footer-title">
                        <i class="fas fa-car-wash me-2"></i>TiaraWash
                    </h3>
                    <p class="mb-4">
                        Layanan cuci kendaraan premium dengan teknologi modern dan
                        pelayanan profesional untuk kepuasan pelanggan.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50 fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white-50 fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white-50 fs-4"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50 fs-4"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h4 class="footer-title">Layanan</h4>
                    <ul class="footer-links">
                        <li><a href="#layanan">Cuci Motor</a></li>
                        <li><a href="#layanan">Cuci Mobil</a></li>
                        <li><a href="#layanan">Cuci Truk</a></li>
                        <li><a href="#layanan">Detailing</a></li>
                        <li><a href="#layanan">Waxing</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h4 class="footer-title">Perusahaan</h4>
                    <ul class="footer-links">
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="#testimoni">Testimoni</a></li>
                        <li><a href="#">Karir</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <h4 class="footer-title">Kontak Kami</h4>
                    <ul class="footer-links">
                        <li>
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Jl. Rawang Jundul, Padang Utara, kota Padang
                        </li>
                        <li>
                            <i class="fas fa-phone me-2"></i>
                            +62 21 1234 5678
                        </li>
                        <li>
                            <i class="fas fa-envelope me-2"></i>
                            info@tiarawash.com
                        </li>
                        <li>
                            <i class="fas fa-clock me-2"></i>
                            Senin - Minggu: 06:00 - 22:00
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 TiaraWash. Semua hak dilindungi. | Dibuat dengan ❤️ untuk pelanggan terbaik</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add fade-in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        // Professional dropdown functions
        function handleNotifications() {
            // Show notifications panel or redirect to notifications page
            console.log('Opening notifications...');
            // You can implement a modal or redirect to notifications page
            // window.location.href = '<?= site_url('pelanggan/notifications') ?>';
        }

        function handleSettings() {
            // Redirect to settings page based on user role
            <?php if (isset($user) && $user): ?>
                <?php if ($user['role'] === 'pelanggan'): ?>
                    window.location.href = '<?= site_url('pelanggan/profile') ?>';
                <?php else: ?>
                    window.location.href = '<?= site_url('admin/settings') ?>';
                <?php endif; ?>
            <?php endif; ?>
        }

        function handleLogout() {
            // Use SweetAlert2 for professional logout confirmation
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar dari akun?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0088cc',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Logging out...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Redirect to logout
                        window.location.href = '<?= site_url('auth/logout') ?>';
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert2 is not available
                if (confirm('Apakah Anda yakin ingin logout?')) {
                    window.location.href = '<?= site_url('auth/logout') ?>';
                }
            }
        }

        // Professional dropdown hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown) {
                const dropdownItems = userDropdown.querySelectorAll('.dropdown-item-custom');

                dropdownItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(4px)';
                    });

                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                    });
                });
            }
        });
    </script>

    <!-- Add SweetAlert2 for professional alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>