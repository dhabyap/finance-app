<div class="animate-up">
    <h4 class="font-mono fw-bold mb-4">USER PROFILE</h4>

    <?= $this->session->flashdata('message'); ?>

    <?php if (validation_errors()): ?>
        <div class="alert alert-danger border-brutal font-mono">
            <?= validation_errors() ?>
        </div>
    <?php endif; ?>

    <!-- Profile Info Card -->
    <div class="card card-brutal p-4 mb-4 text-center bg-pastel-yellow">
        <div class="rounded-circle border-2 border-black d-flex align-items-center justify-content-center mx-auto mb-3 bg-white"
            style="width: 80px; height: 80px;">
            <span class="iconify" data-icon="lucide:user" data-width="40"></span>
        </div>
        <h4 class="fw-bold mb-1"><?= $user['name'] ?></h4>
        <p class="font-mono text-muted mb-0">@<?= $user['username'] ?></p>
    </div>

    <!-- Edit Profile Form -->
    <div class="card card-brutal p-4 mb-4">
        <h5 class="font-mono fw-bold mb-4">EDIT DETAILS</h5>
        <form action="<?= base_url('dashboard/profile') ?>" method="post">
            <div class="mb-3">
                <label class="form-label font-mono fw-bold">DISPLAY NAME</label>
                <input type="text" name="name" class="form-control form-control-brutal"
                    value="<?= set_value('name', $user['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">FINANCIAL FREEDOM GOAL (TARGET)</label>
                <div class="input-group">
                    <span class="input-group-text border-brutal bg-white fw-bold">Rp</span>
                    <input type="text" name="goal_amount" class="form-control form-control-brutal"
                        value="<?= set_value('goal_amount', $user['goal_amount']) ?>" placeholder="0"
                        inputmode="numeric">
                </div>
                <small class="text-muted font-mono mt-1 d-block">This is the total savings/balance you aim to reach for
                    your financial freedom.</small>
            </div>

            <hr class="border-2 border-black my-4">

            <h5 class="font-mono fw-bold mb-3">CHANGE PASSWORD</h5>
            <p class="small text-muted mb-3">Leave blank if you don't want to change it.</p>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">NEW PASSWORD</label>
                <input type="password" name="password" class="form-control form-control-brutal"
                    placeholder="Min. 6 characters">
            </div>

            <div class="mb-4">
                <label class="form-label font-mono fw-bold">CONFIRM PASSWORD</label>
                <input type="password" name="confirm_password" class="form-control form-control-brutal"
                    placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn btn-brutal btn-primary-brutal w-100 py-3 fw-bold">
                SAVE CHANGES
            </button>
        </form>
    </div>

    <!-- Secondary Actions -->
    <a href="<?= base_url('auth/logout') ?>" class="btn btn-brutal w-100 py-3 fw-bold bg-pastel-red text-black mb-5">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <span class="iconify" data-icon="lucide:log-out" data-width="20"></span>
            LOGOUT ACCOUNT
        </div>
    </a>
</div>

<script>
    function initPageScripts() {
        // Thousand Separator Formatter
        const $goalInput = $('input[name="goal_amount"]');

        // Initial format if value exists
        if ($goalInput.val()) {
            $goalInput.val(new Intl.NumberFormat('id-ID').format($goalInput.val()));
        }

        $goalInput.on('input', function () {
            let val = $(this).val().replace(/\D/g, ''); // Remove non-digits
            if (val !== '') {
                // Format with dots
                $(this).val(new Intl.NumberFormat('id-ID').format(val));
            }
        });

        $('form').on('submit', function () {
            // Strip dots before submitting (standard form submit)
            $goalInput.val($goalInput.val().replace(/\./g, ''));
        });
    }

    $(document).ready(initPageScripts);
</script>