<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
        <p class="mb-0 text-secondary">Kelola informasi profil pelanggan Anda</p>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <div class="card-body">
                <!-- Tampilkan pesan sukses -->
                <?php if (session()->has('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Tampilkan pesan error -->
                <?php if (session()->has('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pelanggan)) : ?>
                    <form action="<?= site_url('pelanggan/updateProfile') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Kode Pelanggan</label>
                            <p class="form-control-static bg-light p-2 rounded"><?= $pelanggan['kode_pelanggan'] ?></p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-static bg-light p-2 rounded"><?= session()->get('email') ?></p>
                            <small class="text-muted">Email tidak dapat diubah. Hubungi admin untuk mengubah email.</small>
                        </div>

                        <div class="mb-3">
                            <label for="nama_pelanggan" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control <?= (session('errors.nama_pelanggan')) ? 'is-invalid' : '' ?>"
                                id="nama_pelanggan" name="nama_pelanggan" value="<?= old('nama_pelanggan', $pelanggan['nama_pelanggan']) ?>">
                            <div class="invalid-feedback"><?= session('errors.nama_pelanggan') ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label fw-bold">Nomor HP</label>
                            <input type="text" class="form-control <?= (session('errors.no_hp')) ? 'is-invalid' : '' ?>"
                                id="no_hp" name="no_hp" value="<?= old('no_hp', $pelanggan['no_hp']) ?>">
                            <div class="invalid-feedback"><?= session('errors.no_hp') ?></div>
                        </div>

                        <div class="mb-4">
                            <label for="alamat" class="form-label fw-bold">Alamat</label>
                            <textarea class="form-control <?= (session('errors.alamat')) ? 'is-invalid' : '' ?>"
                                id="alamat" name="alamat" rows="3"><?= old('alamat', $pelanggan['alamat']) ?></textarea>
                            <div class="invalid-feedback"><?= session('errors.alamat') ?></div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Profil Belum Lengkap</h5>
                        <p>Anda belum terdaftar sebagai pelanggan. Silakan hubungi admin untuk melengkapi profil Anda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bagian penjelasan atau bantuan -->
<div class="row mt-4">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <i class="bi bi-info-circle me-2"></i>Informasi
                </h5>
                <p class="card-text">Silakan lengkapi dan perbarui data profil Anda. Data ini akan digunakan untuk keperluan reservasi dan pengiriman informasi terkait layanan Tiara Wash.</p>
                <p class="card-text">Jika Anda memiliki pertanyaan atau kesulitan, silakan hubungi customer service kami.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>