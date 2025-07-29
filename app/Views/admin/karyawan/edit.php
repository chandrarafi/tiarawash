<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Edit Karyawan</h1>
        <p class="mb-0 text-secondary">Edit data karyawan: <?= $karyawan['namakaryawan'] ?></p>
    </div>
    <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/karyawan/update/' . $karyawan['idkaryawan']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="idkaryawan" class="form-label">ID Karyawan</label>
                        <input type="text" class="form-control" id="idkaryawan" name="idkaryawan"
                            value="<?= $karyawan['idkaryawan'] ?>" readonly>
                        <div class="form-text">ID karyawan tidak dapat diubah</div>
                    </div>

                    <div class="mb-3">
                        <label for="namakaryawan" class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="namakaryawan" name="namakaryawan"
                            value="<?= old('namakaryawan', $karyawan['namakaryawan']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nohp" class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nohp" name="nohp"
                            value="<?= old('nohp', $karyawan['nohp']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= old('alamat', $karyawan['alamat']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>