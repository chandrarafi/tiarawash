<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <a href="<?= base_url('admin/booking'); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/booking/update/' . $booking['id']); ?>" method="post">
                        <?= csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_booking">Kode Booking</label>
                                    <input type="text" class="form-control" id="kode_booking" value="<?= $booking['kode_booking']; ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="pelanggan_id">Pelanggan</label>
                                    <select class="form-control select2" id="pelanggan_id" name="pelanggan_id">
                                        <option value="">-- Pilih Pelanggan --</option>
                                        <?php foreach ($pelanggan as $p) : ?>
                                            <option value="<?= $p['kode_pelanggan']; ?>" <?= old('pelanggan_id', $booking['pelanggan_id']) == $p['kode_pelanggan'] ? 'selected' : ''; ?>>
                                                <?= $p['kode_pelanggan'] . ' - ' . $p['nama_pelanggan']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Opsional untuk pelanggan walk-in</small>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= old('tanggal', $booking['tanggal']); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="jam">Jam <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="jam" name="jam" required value="<?= old('jam', $booking['jam']); ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_kendaraan">Jenis Kendaraan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_kendaraan" name="jenis_kendaraan" required>
                                        <option value="">-- Pilih Jenis Kendaraan --</option>
                                        <option value="motor" <?= old('jenis_kendaraan', $booking['jenis_kendaraan']) == 'motor' ? 'selected' : ''; ?>>Motor</option>
                                        <option value="mobil" <?= old('jenis_kendaraan', $booking['jenis_kendaraan']) == 'mobil' ? 'selected' : ''; ?>>Mobil</option>
                                        <option value="lainnya" <?= old('jenis_kendaraan', $booking['jenis_kendaraan']) == 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="no_plat">Nomor Plat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_plat" name="no_plat" required value="<?= old('no_plat', $booking['no_plat']); ?>" placeholder="Contoh: AB 1234 XY">
                                </div>

                                <div class="form-group">
                                    <label for="merk_kendaraan">Merk Kendaraan</label>
                                    <input type="text" class="form-control" id="merk_kendaraan" name="merk_kendaraan" value="<?= old('merk_kendaraan', $booking['merk_kendaraan']); ?>" placeholder="Contoh: Honda Vario / Toyota Avanza">
                                </div>

                                <div class="form-group">
                                    <label for="layanan_id">Layanan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="layanan_id" name="layanan_id" required>
                                        <option value="">-- Pilih Layanan --</option>
                                        <?php foreach ($layanan as $l) : ?>
                                            <option value="<?= $l['id']; ?>" data-jenis="<?= $l['jenis_kendaraan']; ?>" <?= old('layanan_id', $booking['layanan_id']) == $l['id'] ? 'selected' : ''; ?>>
                                                <?= $l['nama_layanan']; ?> - Rp <?= number_format($l['harga'], 0, ',', '.'); ?> (<?= ucfirst($l['jenis_kendaraan']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="menunggu" <?= old('status', $booking['status']) == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                        <option value="diproses" <?= old('status', $booking['status']) == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                        <option value="selesai" <?= old('status', $booking['status']) == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="batal" <?= old('status', $booking['status']) == 'batal' ? 'selected' : ''; ?>>Batal</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="catatan">Catatan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= old('catatan', $booking['catatan']); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="<?= base_url('admin/booking'); ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
        });

        // Filter layanan berdasarkan jenis kendaraan
        $('#jenis_kendaraan').change(function() {
            const jenisKendaraan = $(this).val();

            // Reset layanan dropdown
            $('#layanan_id').empty().append('<option value="">-- Pilih Layanan --</option>');

            if (jenisKendaraan) {
                // Ajax request untuk mendapatkan layanan berdasarkan jenis kendaraan
                $.ajax({
                    url: '<?= base_url('admin/layanan/getLayananByJenis'); ?>',
                    type: 'POST',
                    data: {
                        jenis_kendaraan: jenisKendaraan
                    },
                    dataType: 'json',
                    success: function(response) {
                        $.each(response, function(index, layanan) {
                            $('#layanan_id').append(
                                $('<option></option>')
                                .attr('value', layanan.id)
                                .attr('data-jenis', layanan.jenis_kendaraan)
                                .text(layanan.nama_layanan + ' - Rp ' + formatNumber(layanan.harga) + ' (' + capitalize(layanan.jenis_kendaraan) + ')')
                            );
                        });

                        // Restore selected value if exists
                        const oldLayananId = '<?= old('layanan_id', $booking['layanan_id']); ?>';
                        if (oldLayananId) {
                            $('#layanan_id').val(oldLayananId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching layanan:', error);
                    }
                });
            }
        });

        // Helper function untuk format angka
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Helper function untuk kapitalisasi
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
</script>
<?= $this->endSection(); ?>