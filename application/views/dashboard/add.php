<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0">ADD TRANSACTION</h4>
        <a href="<?= base_url('dashboard/transactions') ?>"
            class="btn btn-sm border-brutal bg-white font-mono fw-bold">BACK</a>
    </div>

    <!-- Choice Card -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card card-brutal bg-pastel-yellow h-100 p-3 text-center active-choice border-4">
                <span class="iconify mb-1" data-icon="lucide:keyboard" data-width="24"></span>
                <small class="d-block font-mono fw-bold">MANUAL</small>
            </div>
        </div>
        <div class="col-6">
            <a href="<?= base_url('import') ?>" class="text-decoration-none text-black">
                <div class="card card-brutal bg-white h-100 p-3 text-center hover-lift">
                    <span class="iconify mb-1" data-icon="lucide:file-spreadsheet" data-width="24"></span>
                    <small class="d-block font-mono fw-bold">VIA EXCEL</small>
                </div>
            </a>
        </div>
    </div>

    <div class="card card-brutal p-4">
        <div id="ajax-message"></div>
        <form id="addTransactionForm" action="<?= base_url('dashboard/add') ?>" method="post">
            <div class="mb-3">
                <label class="form-label font-mono fw-bold">TYPE</label>
                <div class="d-flex gap-3">
                    <div class="form-check flex-fill">
                        <input class="form-check-input border-2 border-black" type="radio" name="type" id="type1"
                            value="income" checked>
                        <label class="form-check-label font-mono fw-bold" for="type1">
                            INCOME
                        </label>
                    </div>
                    <div class="form-check flex-fill">
                        <input class="form-check-input border-2 border-black" type="radio" name="type" id="type2"
                            value="expense">
                        <label class="form-check-label font-mono fw-bold" for="type2">
                            EXPENSE
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">TITLE</label>
                <input type="text" name="title" class="form-control form-control-brutal"
                    placeholder="e.g. Salary, Coffee" required>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">AMOUNT</label>
                <input type="number" name="amount" class="form-control form-control-brutal" placeholder="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">CATEGORY</label>
                <input list="categoryList" name="category" class="form-control form-control-brutal"
                    placeholder="Pick or type new category..." required>
                <datalist id="categoryList">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['name'] ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                </datalist>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">PAYEE / RECEIVER</label>
                <input type="text" name="payee" class="form-control form-control-brutal"
                    placeholder="e.g. Starbucks, John Doe">
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">DESCRIPTION</label>
                <textarea name="description" class="form-control form-control-brutal" rows="2"
                    placeholder="Note about this transaction..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label font-mono fw-bold">DATE</label>
                <input type="date" name="date" class="form-control form-control-brutal" value="<?= date('Y-m-d') ?>"
                    required>
            </div>

            <button type="submit" id="btnSave" class="btn btn-brutal btn-primary-brutal w-100 py-3 fw-bold">
                SAVE TRANSACTION
            </button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#addTransactionForm').on('submit', function (e) {
            e.preventDefault();

            // Show loader
            document.getElementById('loader-overlay').classList.add('active');

            const $btn = $('#btnSave');
            const $msg = $('#ajax-message');
            const originalBtnText = $btn.text();

            $btn.prop('disabled', true).text('SAVING...');
            $msg.html('');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    document.getElementById('loader-overlay').classList.remove('active');
                    if (response.status === 'success') {
                        $msg.html('<div class="alert alert-success border-brutal bg-pastel-green mb-3">' + response.message + '</div>');
                        $('#addTransactionForm')[0].reset();
                        // Scroll to top to see message
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        $msg.html('<div class="alert alert-danger border-brutal mb-3">' + response.message + '</div>');
                    }
                },
                error: function () {
                    document.getElementById('loader-overlay').classList.remove('active');
                    $msg.html('<div class="alert alert-danger border-brutal mb-3">Something went wrong. Please try again.</div>');
                },
                complete: function () {
                    $btn.prop('disabled', false).text(originalBtnText);
                }
            });
        });
    });
</script>