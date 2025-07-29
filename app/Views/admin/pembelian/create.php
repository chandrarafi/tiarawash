<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Tambah Pembelian Perlengkapan</h1>
        <p class="mb-0 text-secondary">Tambahkan data pembelian perlengkapan baru</p>
    </div>
    <a href="<?= site_url('admin/pembelian') ?>" class="btn btn-secondary d-flex align-items-center">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="formPembelian">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="no_faktur" class="form-label">Nomor Faktur</label>
                                <input type="text" class="form-control" id="no_faktur" name="no_faktur" value="<?= $no_faktur ?>" readonly>
                                <div class="invalid-feedback" id="error-no_faktur"></div>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal Pembelian</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                                <div class="invalid-feedback" id="error-tanggal"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier" class="form-label">Supplier</label>
                                <input type="text" class="form-control" id="supplier" name="supplier" required>
                                <div class="invalid-feedback" id="error-supplier"></div>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5>Detail Perlengkapan</h5>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary mb-3" id="btnTambahItem">
                                <i class="bi bi-plus-lg"></i> Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="detailTable">
                            <thead>
                                <tr>
                                    <th>Perlengkapan</th>
                                    <th width="120">Jumlah</th>
                                    <th width="200">Harga Satuan</th>
                                    <th width="200">Subtotal</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detailItems">
                                <!-- Items will be added here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th id="totalHarga">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="button" class="btn btn-secondary me-md-2" id="btnBatal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan Pembelian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Item -->
<div class="modal fade" id="modalItem" tabindex="-1" aria-labelledby="modalItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalItemLabel">Tambah Item Perlengkapan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchPerlengkapan" placeholder="Cari perlengkapan...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tablePerlengkapan">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="perlengkapanItems">
                            <!-- Data perlengkapan akan ditampilkan disini -->
                        </tbody>
                    </table>
                </div>
                <hr>
                <form id="formItem">
                    <input type="hidden" id="perlengkapan_id" name="perlengkapan_id">
                    <input type="hidden" id="perlengkapan_nama" name="perlengkapan_nama">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selectedPerlengkapan" class="form-label">Perlengkapan Terpilih</label>
                                <input type="text" class="form-control" id="selectedPerlengkapan" readonly>
                                <div class="invalid-feedback" id="error-perlengkapan_id"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" value="1" required>
                                <div class="invalid-feedback" id="error-jumlah"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" min="0" step="100" required>
                                </div>
                                <div class="invalid-feedback" id="error-harga_satuan"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtotal" class="form-label">Subtotal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="subtotal" name="subtotal" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanItem">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Variabel untuk menyimpan data item
        let items = [];
        let totalHarga = 0;
        let itemModal = new bootstrap.Modal(document.getElementById('modalItem'));
        let perlengkapanData = []; // Untuk menyimpan data perlengkapan

        // Load data perlengkapan
        loadPerlengkapan();

        // Event untuk pencarian perlengkapan
        $('#searchPerlengkapan').on('keyup', function() {
            const keyword = $(this).val().toLowerCase();
            filterPerlengkapan(keyword);
        });

        // Event untuk menghitung subtotal saat jumlah atau harga berubah
        $('#jumlah, #harga_satuan').on('input', function() {
            hitungSubtotal();
        });

        // Event klik tombol tambah item
        $('#btnTambahItem').on('click', function() {
            resetFormItem();
            itemModal.show();
        });

        // Event klik tombol pilih perlengkapan
        $(document).on('click', '.btn-pilih-perlengkapan', function() {
            const id = $(this).data('id');
            const perlengkapan = perlengkapanData.find(item => item.id == id);

            if (perlengkapan) {
                $('#perlengkapan_id').val(perlengkapan.id);
                $('#perlengkapan_nama').val(perlengkapan.nama);
                $('#selectedPerlengkapan').val(perlengkapan.nama + ' - ' + perlengkapan.kategori);
                $('#harga_satuan').val(perlengkapan.harga);
                hitungSubtotal();

                // Highlight baris yang dipilih
                $('#tablePerlengkapan tbody tr').removeClass('table-primary');
                $(this).closest('tr').addClass('table-primary');
            }
        });

        // Event klik tombol simpan item
        $('#btnSimpanItem').on('click', function() {
            // Validasi form
            if (!validateFormItem()) {
                return;
            }

            // Ambil data dari form
            const perlengkapanId = $('#perlengkapan_id').val();
            const perlengkapanText = $('#selectedPerlengkapan').val();
            const jumlah = parseInt($('#jumlah').val());
            const hargaSatuan = parseFloat($('#harga_satuan').val());
            const subtotal = jumlah * hargaSatuan;

            // Tambahkan ke array items
            const item = {
                perlengkapan_id: perlengkapanId,
                perlengkapan_text: perlengkapanText,
                jumlah: jumlah,
                harga_satuan: hargaSatuan,
                subtotal: subtotal
            };

            items.push(item);

            // Update tampilan tabel
            renderItems();

            // Tutup modal
            itemModal.hide();
        });

        // Event untuk mengubah jumlah item langsung di tabel
        $(document).on('change', '.item-jumlah', function() {
            const index = $(this).data('index');
            const newJumlah = parseInt($(this).val()) || 1;

            // Pastikan nilai minimal adalah 1
            if (newJumlah < 1) {
                $(this).val(1);
                return;
            }

            // Update jumlah dan subtotal di array items
            const item = items[index];
            item.jumlah = newJumlah;
            item.subtotal = newJumlah * item.harga_satuan;

            // Update tampilan tabel (hanya subtotal dan total)
            updateSubtotalAndTotal();
        });

        // Event klik tombol hapus item
        $(document).on('click', '.btn-delete-item', function() {
            const index = $(this).data('index');
            items.splice(index, 1);
            renderItems();
        });

        // Event klik tombol batal
        $('#btnBatal').on('click', function() {
            window.location.href = '<?= site_url('admin/pembelian') ?>';
        });

        // Event submit form pembelian
        $('#formPembelian').on('submit', function(e) {
            e.preventDefault();

            // Validasi form
            if (!validateFormPembelian()) {
                return;
            }

            // Ambil data dari form
            const formData = {
                no_faktur: $('#no_faktur').val(),
                tanggal: $('#tanggal').val(),
                supplier: $('#supplier').val(),
                keterangan: $('#keterangan').val(),
                items: JSON.stringify(items)
            };

            // Kirim data ke server
            $.ajax({
                url: '<?= site_url('admin/pembelian/save') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#btnSimpan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = '<?= site_url('admin/pembelian') ?>';
                        });
                    } else {
                        showErrorAlert(response.message);

                        // Tampilkan error validasi
                        if (response.messages) {
                            Object.keys(response.messages).forEach(function(key) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#error-${key}`).text(response.messages[key]);
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    showErrorAlert('Terjadi kesalahan saat menyimpan data');
                },
                complete: function() {
                    $('#btnSimpan').prop('disabled', false).html('Simpan Pembelian');
                }
            });
        });

        // Fungsi untuk memuat data perlengkapan
        function loadPerlengkapan() {
            $.ajax({
                url: '<?= site_url('admin/pembelian/getPerlengkapan') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    perlengkapanData = response;
                    renderPerlengkapanTable(response);
                },
                error: function(xhr, status, error) {
                    showErrorAlert('Gagal memuat data perlengkapan');
                }
            });
        }

        // Fungsi untuk merender tabel perlengkapan
        function renderPerlengkapanTable(data) {
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="6" class="text-center">Tidak ada data perlengkapan</td></tr>';
            } else {
                data.forEach(function(item, index) {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.nama}</td>
                            <td>${item.kategori}</td>
                            <td>${item.stok}</td>
                            <td>${formatRupiah(item.harga)}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary btn-pilih-perlengkapan" data-id="${item.id}">
                                    <i class="bi bi-check-lg"></i> Pilih
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#perlengkapanItems').html(html);
        }

        // Fungsi untuk filter perlengkapan berdasarkan keyword
        function filterPerlengkapan(keyword) {
            if (!keyword) {
                renderPerlengkapanTable(perlengkapanData);
                return;
            }

            const filteredData = perlengkapanData.filter(item => {
                return item.nama.toLowerCase().includes(keyword) ||
                    item.kategori.toLowerCase().includes(keyword);
            });

            renderPerlengkapanTable(filteredData);
        }

        // Fungsi untuk menghitung subtotal
        function hitungSubtotal() {
            const jumlah = parseInt($('#jumlah').val()) || 0;
            const hargaSatuan = parseFloat($('#harga_satuan').val()) || 0;
            const subtotal = jumlah * hargaSatuan;
            $('#subtotal').val(subtotal);
        }

        // Fungsi untuk merender items ke tabel
        function renderItems() {
            let html = '';
            totalHarga = 0;

            if (items.length === 0) {
                html = '<tr><td colspan="5" class="text-center">Belum ada item</td></tr>';
            } else {
                items.forEach(function(item, index) {
                    html += `
                        <tr>
                            <td>${item.perlengkapan_text}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm item-jumlah" data-index="${index}" value="${item.jumlah}" min="1">
                            </td>
                            <td>${formatRupiah(item.harga_satuan)}</td>
                            <td>${formatRupiah(item.subtotal)}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-item" data-index="${index}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    totalHarga += item.subtotal;
                });
            }

            $('#detailItems').html(html);
            $('#totalHarga').text(formatRupiah(totalHarga));
        }

        // Fungsi untuk mereset form item
        function resetFormItem() {
            $('#formItem')[0].reset();
            $('#perlengkapan_id').val('');
            $('#perlengkapan_nama').val('');
            $('#selectedPerlengkapan').removeClass('is-invalid');
            $('#jumlah').removeClass('is-invalid');
            $('#harga_satuan').removeClass('is-invalid');
            $('#error-perlengkapan_id').text('');
            $('#error-jumlah').text('');
            $('#error-harga_satuan').text('');
            $('#tablePerlengkapan tbody tr').removeClass('table-primary');
        }

        // Fungsi untuk validasi form item
        function validateFormItem() {
            let isValid = true;

            // Validasi perlengkapan
            if (!$('#perlengkapan_id').val()) {
                $('#selectedPerlengkapan').addClass('is-invalid');
                $('#error-perlengkapan_id').text('Perlengkapan harus dipilih');
                isValid = false;
            } else {
                $('#selectedPerlengkapan').removeClass('is-invalid');
                $('#error-perlengkapan_id').text('');
            }

            // Validasi jumlah
            if (!$('#jumlah').val() || parseInt($('#jumlah').val()) <= 0) {
                $('#jumlah').addClass('is-invalid');
                $('#error-jumlah').text('Jumlah harus lebih dari 0');
                isValid = false;
            } else {
                $('#jumlah').removeClass('is-invalid');
                $('#error-jumlah').text('');
            }

            // Validasi harga satuan
            if (!$('#harga_satuan').val() || parseFloat($('#harga_satuan').val()) <= 0) {
                $('#harga_satuan').addClass('is-invalid');
                $('#error-harga_satuan').text('Harga satuan harus lebih dari 0');
                isValid = false;
            } else {
                $('#harga_satuan').removeClass('is-invalid');
                $('#error-harga_satuan').text('');
            }

            return isValid;
        }

        // Fungsi untuk validasi form pembelian
        function validateFormPembelian() {
            let isValid = true;

            // Validasi no faktur
            if (!$('#no_faktur').val()) {
                $('#no_faktur').addClass('is-invalid');
                $('#error-no_faktur').text('Nomor faktur harus diisi');
                isValid = false;
            } else {
                $('#no_faktur').removeClass('is-invalid');
                $('#error-no_faktur').text('');
            }

            // Validasi tanggal
            if (!$('#tanggal').val()) {
                $('#tanggal').addClass('is-invalid');
                $('#error-tanggal').text('Tanggal harus diisi');
                isValid = false;
            } else {
                $('#tanggal').removeClass('is-invalid');
                $('#error-tanggal').text('');
            }

            // Validasi supplier
            if (!$('#supplier').val()) {
                $('#supplier').addClass('is-invalid');
                $('#error-supplier').text('Supplier harus diisi');
                isValid = false;
            } else {
                $('#supplier').removeClass('is-invalid');
                $('#error-supplier').text('');
            }

            // Validasi items
            if (items.length === 0) {
                showErrorAlert('Minimal harus ada 1 item perlengkapan');
                isValid = false;
            }

            return isValid;
        }

        // Fungsi untuk update subtotal dan total tanpa merender ulang seluruh tabel
        function updateSubtotalAndTotal() {
            totalHarga = 0;

            // Update setiap baris dan hitung total
            items.forEach(function(item, index) {
                const row = $(`#detailItems tr:eq(${index})`);
                row.find('td:eq(3)').text(formatRupiah(item.subtotal));
                totalHarga += item.subtotal;
            });

            // Update total harga
            $('#totalHarga').text(formatRupiah(totalHarga));
        }

        // Format rupiah
        function formatRupiah(angka) {
            if (!angka || isNaN(angka)) return 'Rp 0';
            return 'Rp ' + parseFloat(angka).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Fungsi untuk menampilkan alert error
        function showErrorAlert(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        }

        // Inisialisasi tampilan
        renderItems();
    });
</script>
<?= $this->endSection() ?>