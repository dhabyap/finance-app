<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold h3 uppercase m-0">REVIEW IMPORT</h2>
        <div class="d-flex gap-2">
            <a href="<?= base_url('import/map') ?>" class="btn btn-outline-dark border-brutal rounded-0 fw-bold px-4"
                data-no-swup>BACK</a>
            <button type="submit" form="importForm"
                class="btn btn-dark border-brutal rounded-0 fw-bold px-4 shadow-none">
                IMPORT ALL NOW
            </button>
        </div>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <form action="<?= base_url('import/process') ?>" method="post" id="importForm" data-no-swup>
        <div class="card card-brutal bg-white p-0 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover mb-0 font-mono small">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="p-3 border-0">DATE</th>
                            <th class="p-3 border-0">TITLE</th>
                            <th class="p-3 border-0">PAYEE</th>
                            <th class="p-3 border-0">AMOUNT</th>
                            <th class="p-3 border-0">TYPE</th>
                            <th class="p-3 border-0">CATEGORY</th>
                            <th class="p-3 border-0 text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="importRows">
                        <?php foreach ($transactions as $i => $t): ?>
                            <tr class="align-middle">
                                <td class="p-3 border-bottom">
                                    <?= date('d M Y', strtotime($t['date'])) ?>
                                    <input type="hidden" name="dates[]" value="<?= $t['date'] ?>">
                                </td>
                                <td class="p-3 border-bottom">
                                    <?= htmlspecialchars($t['title']) ?>
                                    <input type="hidden" name="titles[]" value="<?= htmlspecialchars($t['title']) ?>">
                                </td>
                                <td class="p-3 border-bottom">
                                    <span class="text-muted"><?= $t['payee'] ? htmlspecialchars($t['payee']) : '-' ?></span>
                                    <input type="hidden" name="payees[]" value="<?= htmlspecialchars($t['payee']) ?>">
                                </td>
                                <td class="p-3 border-bottom fw-bold">
                                    Rp <?= number_format($t['amount'], 0, ',', '.') ?>
                                    <input type="hidden" name="amounts[]" value="<?= $t['amount'] ?>">
                                </td>
                                <td class="p-3 border-bottom">
                                    <span
                                        class="badge <?= $t['type'] == 'income' ? 'bg-pastel-green' : 'bg-pastel-red' ?> text-black border border-dark rounded-0 px-2 py-1">
                                        <?= strtoupper($t['type']) ?>
                                    </span>
                                    <input type="hidden" name="types[]" value="<?= $t['type'] ?>">
                                </td>
                                <td class="p-3 border-bottom">
                                    <span class="badge bg-pastel-blue text-black border border-dark rounded-0 px-2 py-1">
                                        <?= htmlspecialchars($t['category']) ?>
                                    </span>
                                    <input type="hidden" name="categories[]"
                                        value="<?= htmlspecialchars($t['category']) ?>">
                                </td>
                                <!-- Description removed -->
                                <td class="p-3 border-bottom text-center">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger border-2 border-dark rounded-0 remove-row">
                                        <span class="iconify" data-icon="lucide:trash-2"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <div class="card card-brutal bg-pastel-yellow p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">ALL SET?</h5>
                <p class="m-0 font-mono small text-muted">A total of <b id="rowCount"><?= count($transactions) ?></b>
                    items detected.</p>
            </div>
            <button type="submit" form="importForm"
                class="btn btn-dark border-brutal rounded-0 fw-bold px-5 py-3 shadow-none">
                IMPORT ALL NOW
            </button>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').fadeOut(300, function () {
            $(this).remove();
            updateCount();
        });
    });

    function updateCount() {
        const count = $('#importRows tr').length;
        $('#rowCount').text(count);
        if (count === 0) {
            window.location.href = '<?= base_url('import') ?>';
        }
    }

    document.getElementById('importForm').addEventListener('submit', function () {
        document.getElementById('loader-overlay').classList.add('active');
    });
</script>
<style>
    .bg-pastel-red {
        background-color: #fecaca;
    }
</style>