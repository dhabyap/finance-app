<div class="animate-up">
    <!-- Balance Card -->
    <div class="card card-brutal bg-pastel-blue mb-4">
        <div class="card-body">
            <h6 class="font-mono text-uppercase mb-1 fw-bold">Total Balance</h6>
            <h2 class="display-4 fw-bold mb-0">Rp <?= number_format($balance, 0, ',', '.') ?></h2>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card card-brutal bg-pastel-green h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="iconify me-1" data-icon="lucide:arrow-down-left"></span>
                        <small class="font-mono fw-bold">INCOME</small>
                    </div>
                    <h5 class="fw-bold mb-0 text-success">+ <?= number_format($total_income, 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-brutal bg-pastel-red h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="iconify me-1" data-icon="lucide:arrow-up-right"></span>
                        <small class="font-mono fw-bold">EXPENSE</small>
                    </div>
                    <h5 class="fw-bold mb-0 text-danger">- <?= number_format($total_expense, 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-mono fw-bold mb-0">RECENT ACTIVITY</h5>
        <a href="<?= base_url('dashboard/transactions') ?>"
            class="text-black font-mono small fw-bold text-decoration-none">VIEW ALL -></a>
    </div>

    <?php if (empty($recent_transactions)): ?>
        <div class="card card-brutal p-4 text-center bg-light">
            <p class="font-mono text-muted mb-0">No transactions yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($recent_transactions as $t): ?>
            <a href="<?= base_url('dashboard/detail/' . $t['id']) ?>" class="text-decoration-none text-black">
                <div class="card card-brutal mb-3 hover-lift transition-all">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-<?= $t['type'] == 'income' ? 'pastel-green' : 'pastel-red' ?> border border-black p-2 d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <span class="iconify"
                                    data-icon="<?= $t['type'] == 'income' ? 'lucide:arrow-down-left' : 'lucide:arrow-up-right' ?>"></span>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;"><?= $t['title'] ?></h6>
                                <small class="text-muted font-mono"
                                    style="font-size: 0.7rem;"><?= date('d M Y', strtotime($t['transaction_date'])) ?></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <h6 class="fw-bold mb-0 <?= $t['type'] == 'income' ? 'text-success' : 'text-danger' ?>">
                                <?= $t['type'] == 'income' ? '+' : '-' ?>         <?= number_format($t['amount'], 0, ',', '.') ?>
                            </h6>
                            <span class="badge rounded-0 border border-dark text-black bg-white font-mono"
                                style="font-size: 0.6rem;"><?= strtoupper($t['category']) ?></span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>