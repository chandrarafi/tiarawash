<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Laporan Pembelian Perlengkapan</h1>
        <p class="mb-0 text-secondary">Lihat dan cetak laporan pembelian perlengkapan</p>
    </div>
    <a href="<?= site_url('admin/pembelian') ?>" class="btn btn-secondary d-flex align-items-center">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
            </div>
            <div class="card-body">
                <form id="formFilter" class="row g-3">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-01') ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Data Laporan Pembelian</h6>
                <button type="button" class="btn btn-sm btn-success" id="btnExport" disabled>
                    <i class="bi bi-file-earmark-excel me-2"></i> Export Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="laporanTable">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>No Faktur</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total Harga</th>
                                <th>Petugas</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="laporanData">
                            <tr>
                                <td colspan="7" class="text-center">Pilih rentang tanggal dan klik tampilkan untuk melihat data</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th id="totalHarga">Rp 0</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Event submit form filter
        $('#formFilter').on('submit', function(e) {
            e.preventDefault();

            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            // Validasi tanggal
            if (new Date(startDate) > new Date(endDate)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
                });
                return;
            }

            // Tampilkan loading
            $('#laporanData').html('<tr><td colspan="7" class="text-center"><i class="spinner-border spinner-border-sm"></i> Memuat data...</td></tr>');

            // Ambil data laporan
            $.ajax({
                url: '<?= site_url('admin/pembelian/getLaporanData') ?>',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Enable tombol export
                        $('#btnExport').prop('disabled', false);

                        // Render data laporan
                        renderLaporanData(response.data);
                    } else {
                        $('#laporanData').html('<tr><td colspan="7" class="text-center">Terjadi kesalahan saat memuat data</td></tr>');
                        $('#totalHarga').text('Rp 0');
                        $('#btnExport').prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    $('#laporanData').html('<tr><td colspan="7" class="text-center">Terjadi kesalahan saat memuat data</td></tr>');
                    $('#totalHarga').text('Rp 0');
                    $('#btnExport').prop('disabled', true);
                }
            });
        });

        // Event klik tombol export
        $('#btnExport').on('click', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            // Redirect ke URL export dengan parameter tanggal
            window.location.href = `<?= site_url('admin/pembelian/exportExcel') ?>?start_date=${startDate}&end_date=${endDate}`;
        });

        // Fungsi untuk render data laporan
        function renderLaporanData(data) {
            if (data.length === 0) {
                $('#laporanData').html('<tr><td colspan="7" class="text-center">Tidak ada data pembelian pada rentang tanggal yang dipilih</td></tr>');
                $('#totalHarga').text('Rp 0');
                return;
            }

            let html = '';
            let totalHarga = 0;

            data.forEach(function(item, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.no_faktur}</td>
                        <td>${formatDate(item.tanggal)}</td>
                        <td>${item.supplier}</td>
                        <td>${formatRupiah(item.total_harga)}</td>
                        <td>${item.user_name || '-'}</td>
                        <td>
                            <a href="<?= site_url('admin/pembelian/detail/') ?>${item.id}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                `;

                totalHarga += parseFloat(item.total_harga);
            });

            $('#laporanData').html(html);
            $('#totalHarga').text(formatRupiah(totalHarga));
        }

        // Format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        // Format rupiah
        function formatRupiah(angka) {
            if (!angka || isNaN(angka)) return 'Rp 0';
            return 'Rp ' + parseFloat(angka).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    });
</script>
<?= $this->endSection() ?>