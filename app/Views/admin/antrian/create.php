<?= $this->extend('admin/layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title; ?></h6>
                    <a href="<?= base_url('admin/antrian'); ?>" class="btn btn-secondary btn-sm">
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

                    <form action="<?= base_url('admin/antrian/store'); ?>" method="post">
                        <?= csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= old('tanggal') ?: date('Y-m-d'); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="booking_id">Booking (Opsional)</label>
                                    <select class="form-control select2" id="booking_id" name="booking_id">
                                        <option value="">-- Pilih Booking --</option>
                                        <?php foreach ($booking as $b) : ?>
                                            <option value="<?= $b['id']; ?>" <?= old('booking_id') == $b['id'] ? 'selected' : ''; ?>>
                                                <?= $b['kode_booking']; ?> - <?= $b['nama_pelanggan'] ?? 'Pelanggan Walk-in'; ?> - <?= $b['no_plat']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Pilih booking yang sudah ada atau biarkan kosong untuk pelanggan walk-in</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="font-weight-bold">Petunjuk:</h6>
                                    <ul>
                                        <li>Untuk pelanggan yang sudah booking, pilih booking dari dropdown.</li>
                                        <li>Untuk pelanggan walk-in (tanpa booking), biarkan field booking kosong.</li>
                                        <li>Antrian akan otomatis dibuat dengan status "Menunggu".</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url('admin/antrian'); ?>" class="btn btn-secondary">Batal</a>
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
    });
</script>
<?= $this->endSection(); ?>