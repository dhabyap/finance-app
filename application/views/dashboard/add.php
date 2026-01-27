<div class="animate-up">
    <h4 class="font-mono fw-bold mb-4">ADD TRANSACTION</h4>

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
                <select name="category" class="form-select form-control-brutal">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['name'] ?>"><?= $cat['name'] ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="Others">Others</option>
                    <?php endif; ?>
                </select>
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
                    if (response.status === 'success') {
                        $msg.html('<div class="alert alert-success border-brutal bg-pastel-green mb-3">' + response.message + '</div>');
                        $('#addTransactionForm')[0].reset();
                    } else {
                        $msg.html('<div class="alert alert-danger border-brutal mb-3">' + response.message + '</div>');
                    }
                },
                error: function () {
                    $msg.html('<div class="alert alert-danger border-brutal mb-3">Something went wrong. Please try again.</div>');
                },
                complete: function () {
                    $btn.prop('disabled', false).text(originalBtnText);
                }
            });
        });
    });
</script>