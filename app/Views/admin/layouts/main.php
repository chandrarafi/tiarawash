<?= $this->include('admin/layouts/partials/header') ?>

<!-- Wrapper -->
<div class="water-effect">
    <?= $this->include('admin/layouts/partials/sidebar') ?>

    <!-- Main Content -->
    <div class="main-content">
        <?= $this->include('admin/layouts/partials/topbar') ?>

        <!-- Page Content -->
        <div class="container-fluid page-content animate__animated animate__fadeIn">
            <?= $this->renderSection('content') ?>
        </div>

        <?= $this->include('admin/layouts/partials/footer') ?>
    </div>
</div>

<?= $this->include('admin/layouts/partials/scripts') ?>