<div class="mb-5">
    <h2 class="font-mono fw-bold text-uppercase m-0">Admin Dashboard</h2>
    <p class="text-muted">System Overview & Quick Access</p>
</div>

<?= $this->session->flashdata('message'); ?>

<!-- Overview Stats -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-brutal bg-pastel-blue p-4">
            <div class="iconify mb-2" data-icon="lucide:users" data-width="32"></div>
            <h4 class="fw-bold mb-1"><?= $total_users ?></h4>
            <p class="m-0 font-mono small fw-bold">TOTAL USERS</p>
            <a href="<?= base_url('admin/users') ?>" class="stretched-link"></a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-brutal bg-pastel-green p-4">
            <div class="iconify mb-2" data-icon="lucide:arrow-right-left" data-width="32"></div>
            <h4 class="fw-bold mb-1"><?= $total_transactions ?></h4>
            <p class="m-0 font-mono small fw-bold">TOTAL TRANSACTIONS</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-brutal bg-pastel-yellow p-4">
            <div class="iconify mb-2" data-icon="lucide:tags" data-width="32"></div>
            <h4 class="fw-bold mb-1"><?= $total_categories ?></h4>
            <p class="m-0 font-mono small fw-bold">TOTAL CATEGORIES</p>
            <a href="<?= base_url('admin/categories') ?>" class="stretched-link"></a>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-brutal bg-white p-4 h-100">
            <h5 class="fw-bold font-mono text-uppercase mb-3 border-bottom border-black pb-2">User Management</h5>
            <p>Manage application users, view their activities, and control access permissions.</p>
            <a href="<?= base_url('admin/users') ?>"
                class="btn btn-dark border-brutal rounded-0 fw-bold mt-auto align-self-start">MANAGE USERS</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-brutal bg-white p-4 h-100">
            <h5 class="fw-bold font-mono text-uppercase mb-3 border-bottom border-black pb-2">Global Categories</h5>
            <p>Define global income and expense categories that will be available to all users.</p>
            <a href="<?= base_url('admin/categories') ?>"
                class="btn btn-dark border-brutal rounded-0 fw-bold mt-auto align-self-start">MANAGE CATEGORIES</a>
        </div>
    </div>
</div>
</div>