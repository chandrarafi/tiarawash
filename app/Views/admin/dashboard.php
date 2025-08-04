<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card glassmorphism welcome-card">
            <div class="card-body p-4">
                <div class="d-md-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Selamat datang kembali, Administrator!</h4>
                        <p class="text-secondary mb-md-0">Berikut adalah ringkasan operasional car wash Anda hari ini</p>
                    </div>
                    <div class="d-flex mt-3 mt-md-0">
                        <button class="btn btn-sm btn-primary px-3">
                            <i class="bi bi-calendar-check me-1"></i> Hari Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-primary fw-bold">Total Booking</div>
                        <div class="d-flex align-items-center">
                            <div class="h3 mb-0 fw-bold" id="totalBookings">0</div>
                            <div class="ms-2 badge bg-success-soft text-success px-2 rounded-pill">
                                <i class="bi bi-arrow-up me-1"></i>12%
                            </div>
                        </div>
                        <div class="mt-2 text-secondary small">
                            <span class="fw-bold">12%</span> meningkat dibanding bulan lalu
                        </div>
                    </div>
                    <div class="icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-success fw-bold">Antrian Aktif</div>
                        <div class="d-flex align-items-center">
                            <div class="h3 mb-0 fw-bold" id="activeQueues">0</div>
                            <div class="ms-2 badge bg-success-soft text-success px-2 rounded-pill">
                                <i class="bi bi-arrow-up me-1"></i>8%
                            </div>
                        </div>
                        <div class="mt-2 text-secondary small">
                            <span class="fw-bold">8%</span> meningkat dibanding minggu lalu
                        </div>
                    </div>
                    <div class="icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-warning fw-bold">Pendapatan Hari Ini</div>
                        <div class="d-flex align-items-center">
                            <div class="h3 mb-0 fw-bold" id="todayRevenue">Rp 0</div>
                            <div class="ms-2 badge bg-warning-soft text-warning px-2 rounded-pill">
                                <i class="bi bi-arrow-up me-1"></i>15%
                            </div>
                        </div>
                        <div class="mt-2 text-secondary small">
                            <span class="fw-bold">15%</span> meningkat dari kemarin
                        </div>
                    </div>
                    <div class="icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-danger fw-bold">Booking Dibatalkan</div>
                        <div class="d-flex align-items-center">
                            <div class="h3 mb-0 fw-bold" id="cancelledBookings">0</div>
                            <div class="ms-2 badge bg-danger-soft text-danger px-2 rounded-pill">
                                <i class="bi bi-arrow-down me-1"></i>5%
                            </div>
                        </div>
                        <div class="mt-2 text-secondary small">
                            <span class="fw-bold">5%</span> menurun dari minggu lalu
                        </div>
                    </div>
                    <div class="icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Table Row -->
<div class="row">
    <!-- Chart -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Statistik Booking & Pendapatan</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle px-3" type="button" id="chartRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        30 Hari Terakhir
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="chartRangeDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-week me-2"></i>7 Hari Terakhir</a></li>
                        <li><a class="dropdown-item active" href="#"><i class="bi bi-calendar2-month me-2"></i>30 Hari Terakhir</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar3 me-2"></i>90 Hari Terakhir</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-check me-2"></i>Tahun ini</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div id="bookingStatsChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Booking Terbaru</h6>
                <a href="<?= site_url('admin/booking') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-car-front me-1"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="recentBookings">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity & Tasks Row -->
<div class="row">
    <!-- Recent Activity -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Aktivitas Terbaru</h6>
                <span class="badge bg-primary-soft text-primary rounded-pill px-3">Hari ini</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-gradient-primary rounded-circle text-white p-2 me-3">
                                <i class="bi bi-car-front"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">Booking Baru</div>
                                    <small class="text-muted">30 menit lalu</small>
                                </div>
                                <div class="small text-secondary">Honda Civic - B 1234 ABC booking cuci komplit</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-gradient-success rounded-circle text-white p-2 me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">Cuci Selesai</div>
                                    <small class="text-muted">1 jam lalu</small>
                                </div>
                                <div class="small text-secondary">Toyota Avanza - D 5678 EFG cuci selesai</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-gradient-warning rounded-circle text-white p-2 me-3">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">Pembayaran Diterima</div>
                                    <small class="text-muted">2 jam lalu</small>
                                </div>
                                <div class="small text-secondary">Pembayaran Rp 35.000 untuk cuci dan wax</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-gradient-danger rounded-circle text-white p-2 me-3">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">Booking Dibatalkan</div>
                                    <small class="text-muted">3 jam lalu</small>
                                </div>
                                <div class="small text-secondary">Suzuki Ertiga - B 9999 XYZ dibatalkan pelanggan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center p-3">
                <a href="#" class="btn btn-sm btn-outline-primary px-4">Lihat Semua Aktivitas</a>
            </div>
        </div>
    </div>

    <!-- Quick Tasks -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Tugas Cepat</h6>
                <button class="btn btn-sm btn-success px-3" data-bs-toggle="modal" data-bs-target="#taskModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </button>
            </div>
            <div class="card-body">
                <div class="task-list">
                    <div class="task-item d-flex align-items-center p-3 border-start border-3 border-success rounded mb-3 bg-light-hover">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" value="" id="task1">
                        </div>
                        <div class="flex-grow-1">
                            <label class="w-100 mb-0" for="task1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Konfirmasi booking menunggu</span>
                                    <span class="badge bg-success rounded-pill">Tinggi</span>
                                </div>
                                <div class="text-secondary small my-1">5 booking memerlukan konfirmasi</div>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Tugas untuk: Admin</small>
                                    <small class="text-muted">Deadline: Hari ini</small>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="task-item d-flex align-items-center p-3 border-start border-3 border-warning rounded mb-3 bg-light-hover">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" value="" id="task2">
                        </div>
                        <div class="flex-grow-1">
                            <label class="w-100 mb-0" for="task2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Update stok perlengkapan</span>
                                    <span class="badge bg-warning rounded-pill">Sedang</span>
                                </div>
                                <div class="text-secondary small my-1">Shampo dan wax hampir habis</div>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Tugas untuk: Staff</small>
                                    <small class="text-muted">Deadline: Minggu ini</small>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="task-item d-flex align-items-center p-3 border-start border-3 border-danger rounded mb-3 bg-light-hover">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" value="" id="task3">
                        </div>
                        <div class="flex-grow-1">
                            <label class="w-100 mb-0" for="task3">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Maintenance peralatan</span>
                                    <span class="badge bg-danger rounded-pill">Penting</span>
                                </div>
                                <div class="text-secondary small my-1">Vacuum dan jet cleaner perlu servis</div>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Tugas untuk: Teknisi</small>
                                    <small class="text-muted">Deadline: Besok</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-center p-3">
                <a href="#" class="btn btn-sm btn-outline-primary px-4">Kelola Semua Tugas</a>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="taskModalLabel">Tambah Tugas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Judul Tugas</label>
                        <input type="text" class="form-control" id="taskTitle" placeholder="Masukkan judul tugas" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="taskDescription" rows="3" placeholder="Masukkan deskripsi tugas"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="taskAssignee" class="form-label">Ditugaskan Kepada</label>
                            <select class="form-select" id="taskAssignee">
                                <option selected>Admin</option>
                                <option>Manager</option>
                                <option>User</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="taskPriority" class="form-label">Prioritas</label>
                            <select class="form-select" id="taskPriority">
                                <option value="high">Tinggi</option>
                                <option value="medium" selected>Sedang</option>
                                <option value="low">Rendah</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="taskDueDate" class="form-label">Tanggal Deadline</label>
                            <input type="date" class="form-control" id="taskDueDate">
                        </div>
                        <div class="col-md-6">
                            <label for="taskStatus" class="form-label">Status</label>
                            <select class="form-select" id="taskStatus">
                                <option value="pending" selected>Pending</option>
                                <option value="in-progress">Dalam Proses</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveTask">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for this page -->
<style>
    .bg-primary-soft {
        background-color: rgba(44, 62, 80, 0.1);
    }

    .bg-success-soft {
        background-color: rgba(39, 174, 96, 0.1);
    }

    .bg-warning-soft {
        background-color: rgba(243, 156, 18, 0.1);
    }

    .bg-danger-soft {
        background-color: rgba(231, 76, 60, 0.1);
    }

    .bg-secondary-soft {
        background-color: rgba(127, 140, 141, 0.1);
    }

    .text-primary {
        color: var(--primary-color) !important;
    }

    .text-success {
        color: var(--success-color) !important;
    }

    .text-warning {
        color: var(--warning-color) !important;
    }

    .text-danger {
        color: var(--danger-color) !important;
    }

    .text-secondary {
        color: var(--secondary-color) !important;
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }

    .bg-light-hover {
        transition: all 0.3s;
    }

    .bg-light-hover:hover {
        background-color: #f8f9fc !important;
        transform: translateX(5px);
    }

    .task-item {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s;
    }

    .task-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }

    /* Modal backdrop overlay fix */
    .modal-backdrop.show {
        opacity: 0.7;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th,
        .table td {
            white-space: nowrap;
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        /* Card adjustments */
        .card-footer {
            text-align: center;
            padding: 0.75rem;
        }

        /* Welcome card adjustments */
        .welcome-card h4 {
            font-size: 1.2rem;
        }

        .welcome-card p {
            font-size: 0.85rem;
        }

        /* Task items adjustments */
        .task-item {
            padding: 0.75rem;
        }

        .task-item .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start;
        }

        .task-item .badge {
            margin-top: 5px;
        }

        .task-item .small {
            font-size: 0.75rem;
        }

        /* Stats card adjustments */
        .stat-card .d-flex {
            flex-direction: column;
        }

        .stat-card .icon {
            margin-top: 0.5rem;
            text-align: start;
        }

        .stat-card .h3 {
            font-size: 1.3rem;
        }

        .stat-card .badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }

        .stat-card .text-xs {
            font-size: 0.7rem;
        }

        /* Chart adjustments */
        #bookingStatsChart {
            height: 250px !important;
        }

        /* Activity list adjustments */
        .list-group-item {
            padding: 0.75rem 0.5rem;
        }

        .list-group-item .small {
            font-size: 0.75rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load dashboard data
        loadDashboardStats();

        // Initialize DataTable for recent bookings
        loadRecentBookings();

        // Initialize Booking Stats Chart
        var options = {
            series: [{
                name: 'Total Booking',
                data: [31, 40, 28, 51, 42, 65, 59, 80, 81, 56, 55, 40]
            }, {
                name: 'Booking Selesai',
                data: [25, 32, 25, 40, 39, 55, 50, 70, 75, 50, 48, 35]
            }, {
                name: 'Pendapatan (juta)',
                data: [5, 8, 6, 10, 9, 12, 11, 15, 16, 12, 11, 8]
            }],
            chart: {
                height: 320,
                type: 'area',
                fontFamily: 'Nunito, sans-serif',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                labels: {
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return Math.round(value);
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value, opts) {
                        if (opts.seriesIndex === 2) {
                            return "Rp " + value + " juta";
                        }
                        return value + " booking";
                    }
                },
                theme: 'dark',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Nunito, sans-serif'
                }
            },
            colors: ['#2c3e50', '#27ae60', '#f39c12'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                padding: {
                    left: 15,
                    right: 15
                }
            },
            markers: {
                size: 4,
                strokeWidth: 0,
                hover: {
                    size: 6
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                offsetY: -30,
                fontSize: '13px',
                fontFamily: 'Nunito, sans-serif',
                markers: {
                    width: 10,
                    height: 10,
                    radius: 100
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                }
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        offsetY: 0
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#bookingStatsChart"), options);
        chart.render();

        // Handle task checkbox behavior
        $('.form-check-input').on('change', function() {
            var label = $(this).parent().next('div').find('.fw-bold');
            if (this.checked) {
                label.css('text-decoration', 'line-through');
                label.css('opacity', '0.5');
            } else {
                label.css('text-decoration', 'none');
                label.css('opacity', '1');
            }
        });

        // Handle save task
        $('#saveTask').on('click', function() {
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            $(this).attr('disabled', true);

            setTimeout(function() {
                $('#saveTask').html('<i class="bi bi-save me-1"></i> Simpan');
                $('#saveTask').attr('disabled', false);

                $('#taskModal').modal('hide');

                Swal.fire({
                    title: 'Sukses',
                    text: 'Tugas berhasil ditambahkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }, 1000);
        });

        // Add animation on scroll for task items
        function animateOnScroll() {
            $('.task-item').each(function(i) {
                setTimeout(function() {
                    $('.task-item').eq(i).addClass('animate__animated animate__fadeInRight');
                }, 300 * i);
            });
        }

        animateOnScroll();
    });

    // Function to load dashboard statistics
    function loadDashboardStats() {
        // Mock data - replace with actual AJAX calls to get real data
        $('#totalBookings').text('156');
        $('#activeQueues').text('8');
        $('#todayRevenue').text('Rp 2.350.000');
        $('#cancelledBookings').text('3');

        // TODO: Replace with actual AJAX calls
        /*
        $.ajax({
            url: '<?= site_url('admin/getDashboardStats') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#totalBookings').text(response.totalBookings || 0);
                $('#activeQueues').text(response.activeQueues || 0);
                $('#todayRevenue').text('Rp ' + (response.todayRevenue || 0).toLocaleString());
                $('#cancelledBookings').text(response.cancelledBookings || 0);
            }
        });
        */
    }

    // Function to load recent bookings
    function loadRecentBookings() {
        // Mock data - replace with actual DataTable
        const mockBookings = [
            ['BK-001', 'John Doe', 'Cuci + Wax', '<span class="badge bg-success">Selesai</span>'],
            ['BK-002', 'Jane Smith', 'Cuci Biasa', '<span class="badge bg-warning">Proses</span>'],
            ['BK-003', 'Bob Wilson', 'Cuci Komplit', '<span class="badge bg-info">Dikonfirmasi</span>'],
            ['BK-004', 'Alice Brown', 'Cuci + Vacuum', '<span class="badge bg-primary">Menunggu</span>'],
            ['BK-005', 'Charlie Davis', 'Cuci Biasa', '<span class="badge bg-danger">Dibatalkan</span>']
        ];

        const tbody = $('#recentBookings tbody');
        tbody.empty();

        mockBookings.forEach(booking => {
            tbody.append(`
                <tr>
                    <td>${booking[0]}</td>
                    <td>${booking[1]}</td>
                    <td>${booking[2]}</td>
                    <td>${booking[3]}</td>
                </tr>
            `);
        });

        // TODO: Replace with actual DataTable initialization
        /*
        $('#recentBookings').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('admin/getRecentBookings') ?>',
                type: 'GET'
            },
            columns: [
                { data: 'kode_booking' },
                { data: 'nama_pelanggan' },
                { data: 'layanan' },
                { 
                    data: 'status',
                    render: function(data) {
                        const statusClass = {
                            'menunggu_konfirmasi': 'bg-warning',
                            'dikonfirmasi': 'bg-info', 
                            'proses': 'bg-primary',
                            'selesai': 'bg-success',
                            'dibatalkan': 'bg-danger'
                        };
                        return `<span class="badge ${statusClass[data] || 'bg-secondary'}">${data}</span>`;
                    }
                }
            ],
            pageLength: 5,
            dom: 't',
            responsive: true,
            language: {
                emptyTable: "Tidak ada booking terbaru",
                zeroRecords: "Tidak ada booking yang cocok"
            }
        });
        */
    }
</script>
<?= $this->endSection() ?>