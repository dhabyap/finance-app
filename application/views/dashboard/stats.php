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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0">STATISTICS</h4>
        <div class="dropdown">
            <button class="btn btn-sm border-brutal bg-white font-mono fw-bold dropdown-toggle shadow-none"
                type="button" data-bs-toggle="dropdown">
                <?= date('F', mktime(0, 0, 0, $filter['month'], 10)) ?> <?= $filter['year'] ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-brutal p-2">
                <form action="<?= base_url('dashboard/stats') ?>" method="get">
                    <div class="mb-2">
                        <select name="month" class="form-select form-select-sm border-brutal font-mono">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $filter['month'] == $m ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <select name="year" class="form-select form-select-sm border-brutal font-mono">
                            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                <option value="<?= $y ?>" <?= $filter['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-dark w-100 border-brutal fw-bold">FILTER</button>
                </form>
            </ul>
        </div>
    </div>

    <!-- ================= FINANCIAL FREEDOM PROGRESS ================= -->
    <?php if ($goal_amount > 0): ?>
        <?php
        $progress = ($overall_balance / $goal_amount) * 100;
        $progress = max(0, min(100, $progress));
        ?>
        <div class="card card-brutal p-4 mb-5 bg-pastel-yellow">
            <h6 class="font-mono fw-bold mb-3">PROGRESS TO FINANCIAL FREEDOM</h6>
            <div class="progress border-brutal mb-2" style="height:30px; background: white;">
                <div class="progress-bar bg-pastel-green text-black fw-bold" style="width:<?= $progress ?>%">
                    <?= round($progress, 1) ?>%
                </div>
            </div>
            <div class="d-flex justify-content-between font-mono fw-bold small">
                <span>SAVINGS: Rp <?= number_format($overall_balance, 0, ',', '.') ?></span>
                <span>TARGET: Rp <?= number_format($goal_amount, 0, ',', '.') ?></span>
            </div>
        </div>
    <?php endif; ?>

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

    <!-- ================= HEALTH METRICS ================= -->
    <div class="row g-3 mb-5">
        <div class="col-6">
            <div class="card card-brutal p-3 h-100 bg-pastel-blue">
                <small class="font-mono fw-bold d-block mb-1">SAVING RATE</small>
                <h4 class="fw-bold m-0"><?= round($saving_rate, 1) ?>%</h4>
                <?php
                if ($saving_rate >= 50)
                    $sr_color = 'text-success';
                elseif ($saving_rate >= 20)
                    $sr_color = 'text-dark';
                else
                    $sr_color = 'text-danger';
                ?>
                <small class="font-mono small mt-2 d-block <?= $sr_color ?>">
                    <?= $saving_rate >= 20 ? 'Healthy' : 'Needs attention' ?>
                </small>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-brutal p-3 h-100 bg-pastel-green">
                <small class="font-mono fw-bold d-block mb-1">EMERGENCY FUND</small>
                <h4 class="fw-bold m-0"><?= round($ef_progress) ?>%</h4>
                <small class="font-mono small mt-2 d-block">Target: 6 Mo Exp</small>
            </div>
        </div>
    </div>

    <!-- ================= FREEDOM FORECAST ================= -->
    <div class="card card-brutal p-4 mb-5 bg-white border-4" style="border-style: double !important;">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-black text-white p-2 me-3 d-flex align-items-center justify-content-center"
                style="width:40px; height:40px;">
                <span class="iconify" data-icon="lucide:arrow-up-right" data-width="24"></span>
            </div>
            <h6 class="font-mono fw-bold m-0">FREEDOM FORECAST</h6>
        </div>

        <?php if (isset($months_to_freedom)): ?>
            <?php if ($months_to_freedom > 0): ?>
                <h2 class="fw-bold mb-1"><?= $months_to_freedom ?> <small class="fs-6 font-mono">MONTHS</small></h2>
                <div class="mb-3 font-mono small text-muted">
                    Approx. <strong><?= date('M Y', strtotime("+{$months_to_freedom} months")) ?></strong> until freedom.
                </div>
                <div class="d-flex gap-2">
                    <span class="badge border border-black text-black bg-white rounded-0 font-mono">AVG SAVINGS: Rp
                        <?= number_format($avg_savings, 0, ',', '.') ?></span>
                </div>
            <?php else: ?>
                <h2 class="fw-bold mb-1 text-success">GOAL REACHED!</h2>
                <p class="font-mono small text-muted">Your current balance exceeds your Financial Freedom target.</p>
            <?php endif; ?>
        <?php else: ?>
            <h2 class="fw-bold mb-1 text-danger">ADJUST HABITS</h2>
            <p class="font-mono small text-muted">Your average monthly savings are negative. Boost income or cut expenses to
                see a forecast.</p>
        <?php endif; ?>
    </div>

    <!-- ================= CATEGORY CHARTS (RESTORED) ================= -->
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
                    <div id="noExpenseData" class="text-center py-5 font-mono text-muted" style="display:none;">
                        No expense data for this month.
                    </div>
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
                    <div id="noIncomeData" class="text-center py-5 font-mono text-muted" style="display:none;">
                        No income data for this month.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TREND ANALYSIS ================= -->
    <?php if (!empty($monthly_summary) && count($monthly_summary) > 1): ?>
        <div class="card card-brutal p-4 mb-5">
            <h6 class="font-mono fw-bold text-center mb-4">6-MONTH TREND</h6>
            <div id="trendLoading" class="text-center py-3 font-mono">
                <div class="spinner-border mb-2"></div><br>Loading...
            </div>
            <div style="height:200px">
                <canvas id="trendChart" style="display:none;"></canvas>
            </div>
        </div>
    <?php endif; ?>

    <!-- ================= SMART INSIGHTS ================= -->
    <?php if (!empty($insights)): ?>
        <h5 class="font-mono fw-bold mb-3">SMART INSIGHTS</h5>
        <?php foreach ($insights as $insight): ?>
            <div
                class="card card-brutal mb-3 bg-<?= $insight['type'] == 'dark' ? 'white' : 'pastel-' . $insight['type'] ?> p-3 border-<?= $insight['type'] == 'dark' ? 'black' : $insight['type'] ?>">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-black text-white p-2 d-flex align-items-center justify-content-center"
                        style="width: 35px; height: 35px;">
                        <span class="iconify" data-icon="<?= $insight['icon'] ?>"></span>
                    </div>
                    <p class="font-mono fw-bold m-0 small"><?= $insight['text'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php if (!empty($exp_totals) || !empty($inc_totals) || !empty($monthly_summary)): ?>
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
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    new Chart(canvas, {
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
                if (<?= !empty($exp_totals) ? 'true' : 'false' ?>) {
                    createDonutChart(
                        'expenseChart',
                        <?= json_encode($exp_labels) ?>,
                        <?= json_encode($exp_totals) ?>,
                        'expenseLoading'
                    );
                } else {
                    $('#expenseLoading').fadeOut(200, function () {
                        $('#noExpenseData').fadeIn(300);
                    });
                }

                if (<?= !empty($inc_totals) ? 'true' : 'false' ?>) {
                    createDonutChart(
                        'incomeChart',
                        <?= json_encode($inc_labels) ?>,
                        <?= json_encode($inc_totals) ?>,
                        'incomeLoading'
                    );
                } else {
                    $('#incomeLoading').fadeOut(200, function () {
                        $('#noIncomeData').fadeIn(300);
                    });
                }

                /* ================= TREND CHART ================= */
                <?php if (!empty($monthly_summary) && count($monthly_summary) > 1): ?>
                    const trendLabels = <?= json_encode(array_column($monthly_summary, 'month_year')) ?>;
                    const trendIncome = <?= json_encode(array_column($monthly_summary, 'total_income')) ?>;
                    const trendExpense = <?= json_encode(array_column($monthly_summary, 'total_expense')) ?>;

                    const trendCanvas = document.getElementById('trendChart');
                    if (trendCanvas) {
                        new Chart(trendCanvas, {
                            type: 'line',
                            data: {
                                labels: trendLabels,
                                datasets: [
                                    {
                                        label: 'Income',
                                        data: trendIncome,
                                        borderColor: '#b6f5c9',
                                        backgroundColor: '#b6f5c9',
                                        tension: 0.3,
                                        fill: false,
                                        borderWidth: 4
                                    },
                                    {
                                        label: 'Expense',
                                        data: trendExpense,
                                        borderColor: '#f5b6b6',
                                        backgroundColor: '#f5b6b6',
                                        tension: 0.3,
                                        fill: false,
                                        borderWidth: 4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { font: { family: 'monospace', weight: 'bold' } }
                                    },
                                    y: {
                                        grid: { color: '#eee' },
                                        ticks: { display: false }
                                    }
                                }
                            }
                        });

                        $('#trendLoading').fadeOut(200, function () {
                            $('#trendChart').fadeIn(300);
                        });
                    }
                <?php endif; ?>

            });

        });
    </script>
<?php endif; ?>