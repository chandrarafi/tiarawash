<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .stats-cards .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }

    .stats-cards .card:hover {
        transform: translateY(-2px);
    }

    .stats-cards .card-body {
        padding: 1.5rem;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        line-height: 1;
    }

    .stats-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    .table-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
    }

    .table-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
    }

    .table-card .card-body {
        padding: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
        background-color: #fff;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
    }

    .table tbody td {
        padding: 0.875rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .badge-belum-bayar {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .badge-dibayar {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .badge-batal {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .filter-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .filter-btn {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .layanan-text {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .text-truncate-mobile {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .stats-cards .col-md-2 {
            margin-bottom: 1rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="h3 mb-0">Data Booking & Transaksi</h1>
                <p class="text-muted mb-0">Kelola booking pelanggan dan konfirmasi pembayaran</p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="<?= site_url('admin/booking/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Booking
                </a>
                <button class="btn btn-outline-secondary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <!-- Data Table -->
    <div class="table-card card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>Daftar Transaksi Booking
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($transactions)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum Ada Transaksi</h4>
                    <p class="text-muted">Belum ada transaksi booking yang tersedia saat ini.</p>
                    <a href="<?= site_url('admin/booking/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Booking Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="bookingTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">No Transaksi</th>
                                <th width="15%">Pelanggan</th>
                                <th width="12%">Tanggal</th>
                                <th width="20%">Layanan</th>
                                <th width="12%">Total</th>
                                <th width="10%">Status</th>
                                <th width="11%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($transactions as $item): ?>
                                <tr data-status="<?= $item['status_pembayaran'] ?>">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div>
                                            <strong class="text-primary"><?= esc($item['no_transaksi']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($item['tanggal_transaksi'])) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= esc($item['nama_pelanggan'] ?? 'Guest') ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($item['kode_booking']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= date('d M Y', strtotime($item['tanggal_booking'])) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= $item['jam_booking'] ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="layanan-text" title="<?= esc($item['layanan_list']) ?>">
                                            <?= esc($item['layanan_list']) ?>
                                        </div>
                                        <small class="text-muted"><?= $item['jumlah_layanan'] ?> layanan</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-success">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></strong>
                                            <br>
                                            <small class="text-muted"><?= ucfirst($item['metode_pembayaran']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($item['status_pembayaran']) {
                                            case 'belum_bayar':
                                                $statusClass = 'badge bg-secondary';
                                                $statusText = 'Belum Bayar';
                                                break;
                                            case 'dibayar':
                                                $statusClass = 'badge bg-success';
                                                $statusText = 'Dibayar';
                                                break;
                                            case 'batal':
                                                $statusClass = 'badge bg-danger';
                                                $statusText = 'Batal';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <!-- Detail Button -->
                                            <a href="<?= site_url('admin/booking/show/' . $item['transaksi_id']) ?>"
                                                class="btn btn-outline-primary btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>

                                            <!-- Edit Button - based on booking ID from kode_booking -->
                                            <?php if (!empty($item['kode_booking'])): ?>
                                                <?php
                                                // Get first booking ID for this kode_booking to enable edit
                                                $bookingModel = new \App\Models\BookingModel();
                                                $firstBooking = $bookingModel->where('kode_booking', $item['kode_booking'])->first();
                                                ?>
                                                <?php if ($firstBooking): ?>
                                                    <a href="<?= site_url('admin/booking/edit/' . $firstBooking['id']) ?>"
                                                        class="btn btn-outline-info btn-sm" title="Edit Booking">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Payment Actions -->
                                            <?php if ($item['status_pembayaran'] === 'belum_bayar' && $item['bukti_pembayaran']): ?>
                                                <button class="btn btn-outline-success btn-sm"
                                                    onclick="approvePayment(<?= $item['transaksi_id'] ?>)"
                                                    title="Konfirmasi Pembayaran">
                                                    <i class="fas fa-check"></i> Konfirmasi
                                                </button>

                                                <button class="btn btn-outline-danger btn-sm"
                                                    onclick="rejectPayment(<?= $item['transaksi_id'] ?>)"
                                                    title="Tolak Pembayaran">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            <?php endif; ?>

                                            <!-- Delete Button -->
                                            <button class="btn btn-outline-secondary btn-sm"
                                                onclick="deleteTransaction(<?= $item['transaksi_id'] ?>)"
                                                title="Hapus Transaksi">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        <?php if (!empty($transactions)): ?>
            var table = $('#bookingTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                language: {
                    "decimal": "",
                    "emptyTable": "Tidak ada data yang tersedia",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }]
            });

            // Filter functionality
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                var filter = $(this).data('filter');

                if (filter === 'all') {
                    table.search('').columns().search('').draw();
                } else {
                    table.column(6).search(filter, true, false).draw();
                }
            });
        <?php endif; ?>
    });

    function approvePayment(transaksiId) {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= site_url('admin/booking/approve-payment/') ?>${transaksiId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    function rejectPayment(transaksiId) {
        Swal.fire({
            title: 'Tolak Pembayaran',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Masukkan alasan mengapa pembayaran ditolak...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan harus diisi!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('alasan', result.value);

                fetch(`<?= site_url('admin/booking/reject-payment/') ?>${transaksiId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    function deleteTransaction(transaksiId) {
        Swal.fire({
            title: 'Hapus Transaksi',
            text: 'Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= site_url('admin/booking/delete-transaction/') ?>${transaksiId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses permintaan',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    // Auto refresh every 60 seconds for pending payments
    setInterval(() => {
        if (<?= $stats['pending_payments'] ?> > 0) {
            location.reload();
        }
    }, 60000);
</script>
<?= $this->endSection() ?>