<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card border-brutal bg-white p-4">
            <h2 class="font-mono fw-bold text-uppercase mb-4 text-center">Admin Challenge</h2>
            <?= $this->session->flashdata('message'); ?>

            <p class="text-muted mb-4 text-center">Please enter your private admin key to proceed.</p>

            <form action="<?= base_url('admin/login') ?>" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold">SECRET KEY</label>
                    <input type="password" name="secret_key" class="form-control border-brutal rounded-0 p-3"
                        placeholder="Enter key..." required autofocus>
                </div>
                <button type="submit" class="btn btn-dark border-brutal rounded-0 w-100 p-3 fw-bold tracking-wider">
                    VERIFY & ACCESS
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="<?= base_url('dashboard') ?>" class="text-black text-decoration-none fw-bold small">← BACK TO
                    DASHBOARD</a>
            </div>
        </div>
    </div>
</div>