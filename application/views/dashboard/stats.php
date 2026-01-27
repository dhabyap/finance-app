<div class="animate-up">
    <h4 class="font-mono fw-bold mb-4">STATISTICS</h4>

    <div class="card card-brutal p-4 mb-4">
        <h6 class="font-mono fw-bold text-center mb-4">INCOME VS EXPENSE</h6>

        <div class="progress border-brutal" style="height: 40px; border-radius: 0;">
            <?php
            $total = $total_income + $total_expense;
            $inc_pct = $total > 0 ? ($total_income / $total) * 100 : 0;
            $exp_pct = $total > 0 ? ($total_expense / $total) * 100 : 0;
            ?>
            <div class="progress-bar bg-pastel-green text-black fw-bold" role="progressbar"
                style="width: <?= $inc_pct ?>%" aria-valuenow="<?= $inc_pct ?>" aria-valuemin="0" aria-valuemax="100">
                <?= round($inc_pct) ?>%</div>
            <div class="progress-bar bg-pastel-red text-black fw-bold" role="progressbar"
                style="width: <?= $exp_pct ?>%" aria-valuenow="<?= $exp_pct ?>" aria-valuemin="0" aria-valuemax="100">
                <?= round($exp_pct) ?>%</div>
        </div>

        <div class="d-flex justify-content-between mt-3 font-mono fw-bold">
            <span class="text-success">INCOME: Rp <?= number_format($total_income, 0, ',', '.') ?></span>
            <span class="text-danger">EXPENSE: Rp <?= number_format($total_expense, 0, ',', '.') ?></span>
        </div>
    </div>

    <!-- Monthly breakdown could go here -->
    <div class="alert alert-info border-brutal font-mono">
        <span class="iconify" data-icon="lucide:info"></span>
        More detailed stats coming soon!
    </div>
</div>