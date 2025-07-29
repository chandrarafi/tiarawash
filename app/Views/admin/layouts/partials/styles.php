<!-- Custom CSS -->
<style>
    :root {
        --primary-color: #0088cc;
        --primary-gradient-start: #00aaff;
        --primary-gradient-end: #0077b6;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --info-color: #17a2b8;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --light-color: #f8f9fc;
        --dark-color: #0077b6;
        --border-radius: 0.5rem;
        --card-border-radius: 0.75rem;
        --box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
        color: #444;
        transition: all 0.3s ease;
        overflow-x: hidden;
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c5c5c5;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--primary-color);
    }

    /* Sidebar scrollbar styling */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Glassmorphism effect */
    .glassmorphism {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Sidebar styling */
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary-gradient-end) 0%, var(--primary-gradient-start) 100%);
        box-shadow: var(--box-shadow);
        z-index: 1040;
        position: fixed;
        width: 280px;
        transition: all 0.3s ease-in-out;
    }

    .sidebar-brand {
        height: 5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem;
    }

    .sidebar-brand h3 {
        color: white;
        font-weight: 700;
        font-size: 1.4rem;
        margin-bottom: 0;
        letter-spacing: 1px;
    }

    .sidebar-brand p {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0;
        letter-spacing: 1px;
    }

    .sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        margin: 0 1.5rem;
    }

    .nav-header {
        color: rgba(255, 255, 255, 0.5);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 1.5rem;
        padding-left: 1.5rem;
    }

    .nav-item {
        position: relative;
        padding: 0 0.5rem;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        padding: 1rem;
        border-radius: var(--border-radius);
        margin: 0.2rem 0;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background-color: white;
        transform: scaleY(0);
        transition: transform 0.3s, opacity 0.3s;
        transform-origin: top;
        opacity: 0;
        border-radius: 0 2px 2px 0;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        transform: translateX(5px);
    }

    .nav-link:hover::before {
        transform: scaleY(1);
        opacity: 1;
    }

    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.1);
    }

    .nav-link.active::before {
        transform: scaleY(1);
        opacity: 1;
    }

    .nav-link i {
        margin-right: 0.8rem;
        font-size: 1.1rem;
        width: 1.5rem;
        text-align: center;
        transition: all 0.3s;
    }

    /* Main content */
    .main-content {
        margin-left: 280px;
        transition: all 0.3s ease-in-out;
        min-height: 100vh;
        background-color: #f5f7fa;
        padding: 2rem;
        position: relative;
        z-index: 1;
    }

    .main-content::before {
        content: '';
        position: fixed;
        top: 0;
        left: 280px;
        right: 0;
        height: 100vh;
        background: radial-gradient(circle at top right, rgba(0, 119, 182, 0.1) 0%, transparent 70%);
        z-index: -1;
    }

    /* Topbar - Modern Design */
    .topbar {
        height: 70px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2rem;
        position: relative;
        overflow: visible;
        z-index: 9998;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 136, 204, 0.1);
    }

    .topbar::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #0088cc, #00aaff, #0077b6, #00c2ff, #003459);
        background-size: 500% 500%;
        animation: gradient 10s ease infinite;
        border-radius: 0 0 15px 15px;
    }

    @keyframes gradient {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .topbar h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0;
    }

    .topbar-divider {
        width: 1px;
        height: 30px;
        background: linear-gradient(to bottom, transparent, rgba(0, 136, 204, 0.2), transparent);
        margin: 0 15px;
    }

    .topbar-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .topbar-item {
        position: relative;
        z-index: 9999;
    }

    .topbar-nav .nav-link {
        color: var(--secondary-color);
        padding: 0.7rem;
        border-radius: 12px;
        height: 45px;
        width: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        overflow: visible;
        background: rgba(0, 136, 204, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        position: relative;
        z-index: 9999;
        backdrop-filter: blur(10px);
    }

    .topbar-nav .nav-link::before {
        display: none;
    }

    .topbar-nav .nav-link:hover {
        background: linear-gradient(135deg, #0088cc, #00aaff);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 136, 204, 0.3);
        text-decoration: none;
    }

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
        z-index: 99999;
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

    .user-profile {
        display: flex;
        align-items: center;
        margin-left: 1rem;
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        transition: all 0.3s;
    }

    .user-profile:hover {
        background-color: #f8f9fc;
    }

    .user-profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .user-info {
        margin-left: 0.8rem;
    }

    .user-info h6 {
        margin-bottom: 0;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .user-info small {
        color: var(--secondary-color);
        font-size: 0.75rem;
    }

    /* Cards */
    .card {
        border: none;
        border-radius: var(--card-border-radius);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.07);
        transition: all 0.3s ease-in-out;
        background-color: white;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(58, 59, 69, 0.15);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        border-top-left-radius: var(--card-border-radius) !important;
        border-top-right-radius: var(--card-border-radius) !important;
        font-weight: 600;
        color: var(--dark-color);
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Buttons */
    .btn {
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        border-radius: var(--border-radius);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .btn::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.1);
        z-index: -1;
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.3s;
    }

    .btn:hover::after {
        transform: scaleY(1);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        box-shadow: 0 5px 15px rgba(0, 136, 204, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
    }

    .btn-info {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border: none;
        color: white;
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        border: none;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar {
            width: 75px;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 1.5rem 0.5rem;
        }

        .sidebar .sidebar-brand h3,
        .sidebar .sidebar-brand p,
        .sidebar .nav-header,
        .sidebar .nav-link span {
            display: none;
        }

        .nav-link {
            padding: 1rem 0;
            display: flex;
            justify-content: center;
        }

        .nav-link i {
            margin: 0;
            font-size: 1.2rem;
        }

        .nav-link:hover {
            transform: none;
        }

        .main-content {
            margin-left: 75px;
        }

        .main-content::before {
            left: 75px;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1.5rem;
        }

        .topbar {
            margin-bottom: 1.5rem;
        }

        .user-profile {
            display: none;
        }

        .sidebar {
            width: 0;
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
        }

        .main-content::before {
            left: 0;
        }

        .sidebar.show {
            width: 240px;
            transform: translateX(0);
        }

        .sidebar.show+.main-content {
            margin-left: 0;
        }

        .sidebar.show .sidebar-brand h3,
        .sidebar.show .sidebar-brand p,
        .sidebar.show .nav-header,
        .sidebar.show .nav-link span {
            display: block;
        }

        .sidebar.show .nav-link {
            padding: 1rem;
            justify-content: flex-start;
        }

        .sidebar.show .nav-link i {
            margin-right: 0.8rem;
        }

        .sidebar-toggle {
            display: none !important;
        }
    }

    /* Sidebar toggle */
    .sidebar-toggle {
        display: none;
        background: linear-gradient(135deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
        border-radius: 50%;
        height: 3rem;
        width: 3rem;
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        z-index: 1050;
        text-align: center;
        line-height: 3rem;
        font-size: 1.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        cursor: pointer;
        transition: all 0.3s;
    }

    .sidebar-toggle:hover {
        transform: scale(1.1);
    }

    /* Navbar hamburger for sidebar */
    .navbar-toggler {
        background-color: transparent;
        border: none;
        padding: 0;
        margin-right: 1rem;
        display: none;
    }

    .navbar-toggler-icon {
        color: var(--dark-color);
        font-size: 1.5rem;
    }

    @media (max-width: 768px) {
        .navbar-toggler {
            display: block;
        }
    }

    /* Stat cards */
    .stat-card {
        border-left: 0.25rem solid var(--primary-color);
        border-radius: var(--card-border-radius);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), transparent);
    }

    .stat-card.primary {
        border-left-color: var(--primary-color);
    }

    .stat-card.primary::after {
        background: linear-gradient(90deg, var(--primary-color), transparent);
    }

    .stat-card.success {
        border-left-color: var(--success-color);
    }

    .stat-card.success::after {
        background: linear-gradient(90deg, var(--success-color), transparent);
    }

    .stat-card.warning {
        border-left-color: var(--warning-color);
    }

    .stat-card.warning::after {
        background: linear-gradient(90deg, var(--warning-color), transparent);
    }

    .stat-card.danger {
        border-left-color: var(--danger-color);
    }

    .stat-card.danger::after {
        background: linear-gradient(90deg, var(--danger-color), transparent);
    }

    .stat-card .icon {
        font-size: 2rem;
        color: rgba(0, 136, 204, 0.1);
        transition: all 0.3s;
    }

    .stat-card:hover .icon {
        transform: scale(1.2);
        color: rgba(0, 136, 204, 0.2);
    }

    /* Animations */
    .animate__animated {
        animation-duration: 0.5s;
    }

    .page-content {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Car wash theme elements */
    .bubble {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite;
        z-index: -1;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0) scale(1);
        }

        50% {
            transform: translateY(-20px) scale(1.05);
        }
    }

    .water-effect {
        position: relative;
        overflow: hidden;
    }

    .water-effect::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(ellipse at center, rgba(0, 170, 255, 0.3) 0%, rgba(0, 170, 255, 0) 70%);
        animation: water 15s linear infinite;
        opacity: 0.1;
        pointer-events: none;
    }

    @keyframes water {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Modern Dropdown Styling */
    .dropdown-menu {
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        padding: 0;
        z-index: 99999;
        position: absolute;
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
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

    .dropdown-header {
        background: linear-gradient(135deg, #f8f9fc, #e9ecef);
        border-radius: 15px 15px 0 0;
        padding: 20px;
        margin: 0;
        border: none;
        font-weight: 600;
    }

    .dropdown-item {
        padding: 15px 20px;
        border: none;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .dropdown-item:hover {
        background: rgba(0, 136, 204, 0.05);
        color: var(--primary-color);
        transform: translateX(5px);
    }

    .dropdown-divider {
        margin: 0;
        opacity: 0.1;
    }

    /* Ensure dropdown shows properly */
    .topbar-item.dropdown.show .dropdown-menu {
        display: block;
        z-index: 99999;
    }

    /* Fix for Bootstrap dropdown positioning */
    .dropdown-toggle::after {
        display: none;
    }

    /* Override Bootstrap's dropdown positioning */
    .topbar .dropdown-menu[data-bs-popper] {
        position: absolute !important;
        z-index: 99999 !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .topbar {
            padding: 0 1rem;
            height: 60px;
        }

        .topbar-nav .nav-link {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .dropdown-menu {
            width: 300px !important;
        }
    }
</style>