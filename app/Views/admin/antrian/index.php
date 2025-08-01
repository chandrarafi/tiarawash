<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Realtime Indicator -->
<div class="realtime-indicator" id="realtimeIndicator" style="position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; z-index: 1000; display: none;">
    <i class="fas fa-sync-alt fa-spin"></i> Updating...
</div>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-list-ol me-2"></i><?= $title ?>
        </h1>
        <p class="text-muted mb-0"><?= $subtitle ?></p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <input type="date" id="dateFilter" class="form-control" style="width: auto;"
            value="<?= $tanggal ?>" onchange="filterByDate(this.value)">
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Antrian
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Menunggu
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['menunggu'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Diproses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['diproses'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Selesai
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['selesai'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Queue List -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-queue me-2"></i>Daftar Antrian - <?= date('d/m/Y', strtotime($tanggal)) ?>
                </h6>
                <button onclick="refreshData()" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="queueContainer">
                    <?php if (empty($antrian)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Tidak ada antrian hari ini</h5>
                            <p class="text-gray-500">Belum ada customer yang mengantri untuk tanggal ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($antrian as $item): ?>
                            <div class="card mb-3 border-left-primary">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 text-center">
                                            <div class="h4 font-weight-bold text-primary mb-1">
                                                <?= esc($item['nomor_antrian'] ?? '-') ?>
                                            </div>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($item['status']) {
                                                case 'menunggu':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Menunggu';
                                                    break;
                                                case 'diproses':
                                                    $statusClass = 'badge-info';
                                                    $statusText = 'Diproses';
                                                    break;
                                                case 'selesai':
                                                    $statusClass = 'badge-success';
                                                    $statusText = 'Selesai';
                                                    break;
                                                case 'batal':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Batal';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = 'Unknown';
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="font-weight-bold text-gray-800">
                                                <?= esc($item['nama_pelanggan'] ?? 'Walk-in Customer') ?>
                                            </div>
                                            <div class="text-gray-600">
                                                <?= esc($item['nama_layanan'] ?? '-') ?>
                                            </div>
                                            <div class="small text-gray-500">
                                                <i class="fas fa-car me-1"></i>
                                                <?= esc($item['no_plat'] ?? '-') ?> | <?= esc($item['jenis_kendaraan'] ?? '-') ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <?php if ($item['namakaryawan'] ?? false): ?>
                                                <div class="text-success">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    <?= esc($item['namakaryawan']) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-muted">
                                                    <i class="fas fa-user-slash me-1"></i>
                                                    Belum ditugaskan
                                                </div>
                                            <?php endif; ?>

                                            <div class="small text-gray-500">
                                                <?php if ($item['jam_mulai'] ?? false): ?>
                                                    Mulai: <?= date('H:i', strtotime($item['jam_mulai'])) ?>
                                                <?php endif; ?>
                                                <?php if ($item['jam_selesai'] ?? false): ?>
                                                    | Selesai: <?= date('H:i', strtotime($item['jam_selesai'])) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-end">
                                            <div class="btn-group-vertical" role="group">
                                                <a href="<?= site_url('admin/antrian/show/' . $item['id']) ?>"
                                                    class="btn btn-sm btn-outline-info mb-1">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>

                                                <?php if ($item['status'] == 'menunggu'): ?>
                                                    <button onclick="autoAssign(<?= $item['id'] ?>)"
                                                        class="btn btn-sm btn-success">
                                                        <i class="fas fa-play"></i> Proses
                                                    </button>
                                                <?php elseif ($item['status'] == 'diproses'): ?>
                                                    <button onclick="completeQueue(<?= $item['id'] ?>)"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-check"></i> Selesai
                                                    </button>
                                                <?php endif; ?>
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

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Next Queue -->
        <?php if ($nextQueue && isset($nextQueue['id'])): ?>
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-arrow-right me-2"></i>Antrian Selanjutnya
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="h4 mb-0 font-weight-bold text-primary">
                                <?= esc($nextQueue['nomor_antrian'] ?? '-') ?>
                            </div>
                        </div>
                        <div>
                            <div class="font-weight-bold text-gray-800">
                                <?= esc($nextQueue['nama_pelanggan'] ?? 'Walk-in') ?>
                            </div>
                            <div class="text-gray-600"><?= esc($nextQueue['nama_layanan'] ?? '-') ?></div>
                        </div>
                    </div>
                    <button onclick="autoAssign(<?= $nextQueue['id'] ?>)"
                        class="btn btn-warning w-100">
                        <i class="fas fa-play me-1"></i>Proses Cucian
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-check-circle me-2"></i>Status Antrian
                    </h6>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                    <h6 class="text-success">Semua Antrian Selesai</h6>
                    <p class="text-muted mb-0">Tidak ada antrian yang menunggu untuk diproses</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Employee Workload -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>Beban Kerja Karyawan
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($workload)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-users-slash fa-2x text-gray-300 mb-2"></i>
                        <p class="text-gray-500 mb-0">Tidak ada data karyawan</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($workload as $worker): ?>
                        <div class="border-left-primary border p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold text-gray-800">
                                        <?= esc($worker['namakaryawan']) ?>
                                    </div>
                                    <div class="small text-gray-600">
                                        Total: <?= $worker['total_antrian'] ?: 0 ?> antrian
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="badge badge-info mb-1">
                                        Aktif: <?= $worker['sedang_diproses'] ?: 0 ?>
                                    </div>
                                    <div class="badge badge-success">
                                        Selesai: <?= $worker['selesai'] ?: 0 ?>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto refresh every 30 seconds
    let autoRefreshInterval;

    $(document).ready(function() {
        startAutoRefresh();
    });

    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            refreshData();
        }, 30000); // 30 seconds
    }

    function refreshData() {
        showRealtimeIndicator();

        const currentDate = $('#dateFilter').val();

        fetch(`<?= site_url('admin/antrian/realtime-data') ?>?tanggal=${currentDate}`)
            .then(response => response.json())
            .then(data => {
                updateQueueList(data.antrian);
                updateStats(data.stats);
                updateWorkload(data.workload);
                updateNextQueue(data.nextQueue);
                hideRealtimeIndicator();
            })
            .catch(error => {
                console.error('Error fetching realtime data:', error);
                hideRealtimeIndicator();
            });
    }

    function updateQueueList(antrian) {
        const container = $('#queueContainer');

        if (antrian.length === 0) {
            container.html(`
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Tidak ada antrian hari ini</h5>
                <p class="text-gray-500">Belum ada customer yang mengantri untuk tanggal ini.</p>
            </div>
        `);
            return;
        }

        let html = '';
        antrian.forEach(item => {
            html += generateQueueCard(item);
        });

        container.html(html);
    }

    function generateQueueCard(item) {
        let statusClass = '';
        let statusText = '';
        switch (item.status) {
            case 'menunggu':
                statusClass = 'badge-warning';
                statusText = 'Menunggu';
                break;
            case 'diproses':
                statusClass = 'badge-info';
                statusText = 'Diproses';
                break;
            case 'selesai':
                statusClass = 'badge-success';
                statusText = 'Selesai';
                break;
            case 'batal':
                statusClass = 'badge-danger';
                statusText = 'Batal';
                break;
            default:
                statusClass = 'badge-secondary';
                statusText = 'Unknown';
        }

        return `
        <div class="card mb-3 border-left-primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <div class="h4 font-weight-bold text-primary mb-1">
                            ${item.nomor_antrian || '-'}
                        </div>
                        <span class="badge ${statusClass}">${statusText}</span>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="font-weight-bold text-gray-800">
                            ${item.nama_pelanggan || 'Walk-in Customer'}
                        </div>
                        <div class="text-gray-600">
                            ${item.nama_layanan || '-'}
                        </div>
                        <div class="small text-gray-500">
                            <i class="fas fa-car me-1"></i>
                            ${item.no_plat || '-'} | ${item.jenis_kendaraan || '-'}
                        </div>
                    </div>

                    <div class="col-md-3">
                        ${item.namakaryawan 
                            ? `<div class="text-success"><i class="fas fa-user-tie me-1"></i>${item.namakaryawan}</div>`
                            : `<div class="text-muted"><i class="fas fa-user-slash me-1"></i>Belum ditugaskan</div>`
                        }
                        
                        <div class="small text-gray-500">
                            ${item.jam_mulai ? `Mulai: ${formatTime(item.jam_mulai)}` : ''}
                            ${item.jam_selesai ? ` | Selesai: ${formatTime(item.jam_selesai)}` : ''}
                        </div>
                    </div>

                    <div class="col-md-3 text-end">
                        <div class="btn-group-vertical" role="group">
                            <a href="<?= site_url('admin/antrian/show/') ?>${item.id}" 
                               class="btn btn-sm btn-outline-info mb-1">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            
                            ${item.status === 'menunggu' 
                                ? `<button onclick="autoAssign(${item.id})" class="btn btn-sm btn-success">
                                     <i class="fas fa-play"></i> Proses
                                   </button>`
                                : ''
                            }
                            ${item.status === 'diproses' 
                                ? `<button onclick="completeQueue(${item.id})" class="btn btn-sm btn-primary">
                                     <i class="fas fa-check"></i> Selesai
                                   </button>`
                                : ''
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    function updateStats(stats) {
        $('.h5.mb-0.font-weight-bold.text-gray-800').each(function(index) {
            const values = [stats.total, stats.menunggu, stats.diproses, stats.selesai];
            $(this).text(values[index]);
        });
    }

    function updateWorkload(workload) {
        // Implementation for updating workload section
    }

    function updateNextQueue(nextQueue) {
        const container = $('.col-lg-4'); // Sidebar container
        const nextQueueSection = container.find('.card:first'); // First card in sidebar

        if (nextQueue && nextQueue.id) {
            // Update existing next queue card
            nextQueueSection.find('.h4').text(nextQueue.nomor_antrian || '-');
            nextQueueSection.find('.font-weight-bold.text-gray-800').text(nextQueue.nama_pelanggan || 'Walk-in');
            nextQueueSection.find('.text-gray-600').text(nextQueue.nama_layanan || '-');
            nextQueueSection.find('button').attr('onclick', `autoAssign(${nextQueue.id})`);

            // Ensure it shows as "next queue" style
            nextQueueSection.removeClass('border-left-success').addClass('border-left-warning');
            nextQueueSection.find('.card-header h6').removeClass('text-success').addClass('text-warning');
            nextQueueSection.find('.fa-check-circle').removeClass('fa-check-circle').addClass('fa-arrow-right');
            nextQueueSection.find('.card-header h6').html('<i class="fas fa-arrow-right me-2"></i>Antrian Selanjutnya');
        } else {
            // Show "all completed" message
            nextQueueSection.removeClass('border-left-warning').addClass('border-left-success');
            nextQueueSection.find('.card-header h6').removeClass('text-warning').addClass('text-success');
            nextQueueSection.find('.fa-arrow-right').removeClass('fa-arrow-right').addClass('fa-check-circle');
            nextQueueSection.find('.card-header h6').html('<i class="fas fa-check-circle me-2"></i>Status Antrian');

            nextQueueSection.find('.card-body').html(`
            <div class="text-center py-4">
                <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                <h6 class="text-success">Semua Antrian Selesai</h6>
                <p class="text-muted mb-0">Tidak ada antrian yang menunggu untuk diproses</p>
            </div>
        `);
        }
    }

    function formatTime(time) {
        if (!time) return '';
        return time.substring(0, 5); // Format HH:MM
    }

    function showRealtimeIndicator() {
        $('#realtimeIndicator').fadeIn();
    }

    function hideRealtimeIndicator() {
        $('#realtimeIndicator').fadeOut();
    }

    function filterByDate(date) {
        window.location.href = `<?= site_url('admin/antrian') ?>?tanggal=${date}`;
    }

    function autoAssign(antrianId) {
        Swal.fire({
            title: 'Proses Cucian?',
            text: 'Mulai proses cucian untuk antrian ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-play me-1"></i>Ya, Proses',
            cancelButtonText: '<i class="fas fa-times me-1"></i>Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menugaskan karyawan',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`<?= site_url('admin/antrian/auto-assign/') ?>${antrianId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Cucian telah diproses oleh karyawan',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            });
                            refreshData();
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Gagal memproses cucian',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
            }
        });
    }

    function completeQueue(antrianId) {
        Swal.fire({
            title: 'Selesai Cucian?',
            text: 'Tandai cucian ini sebagai selesai?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check me-1"></i>Ya, Selesai',
            cancelButtonText: '<i class="fas fa-times me-1"></i>Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menyelesaikan cucian',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`<?= site_url('admin/antrian/update-status/') ?>${antrianId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'status=selesai'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Cucian telah diselesaikan',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            });
                            refreshData();
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Gagal menyelesaikan cucian',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
            }
        });
    }

    // Pause auto refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(autoRefreshInterval);
        } else {
            startAutoRefresh();
        }
    });
</script>
<?= $this->endSection() ?>