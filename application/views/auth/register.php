<div class="container animate-up">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card card-brutal p-4 bg-white">
                <div class="text-center mb-4">
                    <h1 class="display-6 fw-bold">REGISTER</h1>
                    <p class="text-muted font-mono small">Join the club.</p>
                </div>

                <form method="post" action="<?= base_url('auth/register'); ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label font-mono fw-bold">FULL NAME</label>
                        <input type="text" class="form-control form-control-brutal" id="name" name="name"
                            value="<?= set_value('name'); ?>">
                        <?= form_error('name', '<small class="text-danger font-mono fw-bold">', '</small>'); ?>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label font-mono fw-bold">USERNAME</label>
                        <input type="text" class="form-control form-control-brutal" id="username" name="username"
                            value="<?= set_value('username'); ?>">
                        <?= form_error('username', '<small class="text-danger font-mono fw-bold">', '</small>'); ?>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label font-mono fw-bold">PASSWORD</label>
                        <input type="password" class="form-control form-control-brutal" id="password" name="password">
                        <?= form_error('password', '<small class="text-danger font-mono fw-bold">', '</small>'); ?>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary-brutal btn-lg py-3">
                            Join Now
                        </button>
                    </div>
                    <div class="text-center">
                        <a href="<?= base_url('auth/login'); ?>"
                            class="text-decoration-none text-black font-mono fw-bold">
                            <u>Already have an account?</u>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>