<div class="animate-up">
    <div class="mb-4">
        <h2 class="fw-bold h3 uppercase">MAP COLUMNS</h2>
        <p class="text-muted font-mono">Select the correct columns from your file.</p>
    </div>

    <form action="<?= base_url('import/preview') ?>" method="post" data-no-swup>
        <div class="card card-brutal bg-white p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold font-mono small">DATE COLUMN</label>
                    <select name="col_date" class="form-select form-control-brutal" required>
                        <?php for ($i = 0; $i < $total_cols; $i++): ?>
                            <option value="<?= $i ?>" <?= ($i == 0) ? 'selected' : '' ?>>Column <?= $i + 1 ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold font-mono small">TITLE COLUMN</label>
                    <select name="col_title" class="form-select form-control-brutal" required>
                        <?php for ($i = 0; $i < $total_cols; $i++): ?>
                            <option value="<?= $i ?>" <?= ($i == 1) ? 'selected' : '' ?>>Column <?= $i + 1 ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold font-mono small">AMOUNT COLUMN</label>
                    <select name="col_amount" class="form-select form-control-brutal" required>
                        <?php for ($i = 0; $i < $total_cols; $i++): ?>
                            <option value="<?= $i ?>" <?= ($i == 2) ? 'selected' : '' ?>>Column <?= $i + 1 ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold font-mono small">TYPE COLUMN (Optional)</label>
                    <select name="col_type" class="form-select form-control-brutal">
                        <option value="">-- No Column (Default Expense) --</option>
                        <?php for ($i = 0; $i < $total_cols; $i++): ?>
                            <option value="<?= $i ?>">Column <?= $i + 1 ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold font-mono small">DESCRIPTION (Optional)</label>
                    <select name="col_description" class="form-select form-control-brutal">
                        <option value="">-- No Column --</option>
                        <?php for ($i = 0; $i < $total_cols; $i++): ?>
                            <option value="<?= $i ?>">Column <?= $i + 1 ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold font-mono small">PAYEE/RECEIVER (Optional)</label>
                    <select name="col_payee" class="form-select form-control-brutal">
                        <option value="">-- No Column --</option>
                        <?php for ($i = 0; $i < $total_cols; $i++): ?>
                            <option value="<?= $i ?>">Column <?= $i + 1 ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_header" value="1" id="hasHeader" checked>
                    <label class="form-check-label font-mono small fw-bold" for="hasHeader">
                        FIRST ROW IS HEADER (Skip it)
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-bold font-mono small text-uppercase mb-3">Data Preview (First 5 Rows)</h5>
            <div class="table-responsive">
                <table class="table table-bordered border-dark font-mono small bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <?php for ($i = 0; $i < $total_cols; $i++): ?>
                                <th class="text-center">Col <?= $i + 1 ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preview_rows as $row): ?>
                            <tr>
                                <?php foreach ($row as $val): ?>
                                    <td class="text-truncate" style="max-width: 150px;"><?= htmlspecialchars($val) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary-brutal btn-lg py-3">
                CONTINUE TO PREVIEW
            </button>
            <a href="<?= base_url('import') ?>" class="btn btn-outline-dark border-brutal rounded-0 fw-bold py-2"
                data-no-swup>
                START OVER
            </a>
        </div>
    </form>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('loader-overlay').classList.add('active');
    });
</script>
</div>