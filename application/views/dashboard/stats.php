<?php
/* ===================== PREPARE DATA ===================== */
$exp_labels = [];
$exp_totals = [];

if (!empty($category_expense)) {
    foreach ($category_expense as $c) {
        $exp_labels[] = $c->category;
        $exp_totals[] = (int) $c->total;
    }
}

$inc_labels = [];
$inc_totals = [];

if (!empty($category_income)) {
    foreach ($category_income as $c) {
        $inc_labels[] = $c->category;
        $inc_totals[] = (int) $c->total;
    }
}
?>

<div class="animate-up">
    <h4 class="font-mono fw-bold mb-4">STATISTICS</h4>

    <!-- ================= INCOME VS EXPENSE ================= -->
    <div class="card card-brutal p-4 mb-5">
        <h6 class="font-mono fw-bold text-center mb-4">INCOME VS EXPENSE</h6>

        <?php
        $total = $total_income + $total_expense;
        $inc_pct = $total > 0 ? ($total_income / $total) * 100 : 0;
        $exp_pct = $total > 0 ? ($total_expense / $total) * 100 : 0;
        ?>

        <div class="progress border-brutal mb-3" style="height:40px">
            <div class="progress-bar bg-pastel-green text-black fw-bold" style="width:<?= $inc_pct ?>%">
                <?= round($inc_pct) ?>%
            </div>
            <div class="progress-bar bg-pastel-red text-black fw-bold" style="width:<?= $exp_pct ?>%">
                <?= round($exp_pct) ?>%
            </div>
        </div>

        <div class="d-flex justify-content-between font-mono fw-bold">
            <span class="text-success">
                INCOME: Rp <?= number_format($total_income, 0, ',', '.') ?>
            </span>
            <span class="text-danger">
                EXPENSE: Rp <?= number_format($total_expense, 0, ',', '.') ?>
            </span>
        </div>
    </div>

    <!-- ================= CATEGORY CHARTS ================= -->
    <div class="row g-4 mb-5">

        <!-- EXPENSE BY CATEGORY -->
        <div class="col-12">
            <div class="card card-brutal p-4">
                <h6 class="font-mono fw-bold text-center mb-4">EXPENSE BY CATEGORY</h6>

                <div id="expenseLoading" class="text-center py-3 font-mono">
                    <div class="spinner-border mb-2"></div><br>Loading...
                </div>

                <div style="height:260px">
                    <canvas id="expenseChart" style="display:none;"></canvas>
                </div>
            </div>
        </div>

        <!-- INCOME BY CATEGORY -->
        <div class="col-12">
            <div class="card card-brutal p-4">
                <h6 class="font-mono fw-bold text-center mb-4">INCOME BY CATEGORY</h6>

                <div id="incomeLoading" class="text-center py-3 font-mono">
                    <div class="spinner-border mb-2"></div><br>Loading...
                </div>

                <div style="height:260px">
                    <canvas id="incomeChart" style="display:none;"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="alert alert-info border-brutal font-mono">
        <span class="iconify" data-icon="lucide:info"></span>
        More detailed stats coming soon!
    </div>
</div>

<?php if (!empty($exp_totals) || !empty($inc_totals)): ?>
    <script>
    $(document).ready(function () {

        $.getScript('https://cdn.jsdelivr.net/npm/chart.js').done(function () {

            const colors = [
                '#f5b6b6',
                '#b6f5c9',
                '#b6d4f5',
                '#f5e3b6',
                '#e0b6f5',
                '#f5c7e6'
            ];

            function createDonutChart(canvasId, labels, data, loadingId) {

                new Chart(document.getElementById(canvasId), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors.slice(0, data.length),
                            borderColor: '#000',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                align: 'center',
                                labels: {
                                    boxWidth: 18,
                                    boxHeight: 18,
                                    padding: 14,
                                    font: {
                                        family: 'monospace',
                                        weight: 'bold',
                                        size: 12
                                    },
                                    color: '#000'
                                }
                            }
                        }
                    }
                });

                $('#' + loadingId).fadeOut(200, function () {
                    $('#' + canvasId).fadeIn(300);
                });
            }

            /* ================= CREATE CHARTS ================= */
            <?php if (!empty($exp_totals)): ?>
                    createDonutChart(
                        'expenseChart',
                        <?= json_encode($exp_labels) ?>,
                        <?= json_encode($exp_totals) ?>,
                        'expenseLoading'
                    );
            <?php endif; ?>

            <?php if (!empty($inc_totals)): ?>
                    createDonutChart(
                        'incomeChart',
                        <?= json_encode($inc_labels) ?>,
                        <?= json_encode($inc_totals) ?>,
                        'incomeLoading'
                    );
            <?php endif; ?>

        });

    });
    </script>
<?php endif; ?>
