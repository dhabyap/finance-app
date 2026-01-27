<div class="animate-up">
    <h4 class="font-mono fw-bold mb-4">ALL TRANSACTIONS</h4>

    <?= $this->session->flashdata('message'); ?>

    <?php if (empty($transactions)): ?>
        <div class="alert alert-info border-brutal font-mono">No transactions found. Go spend some money!</div>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($transactions as $t): ?>
                <a href="<?= base_url('dashboard/detail/' . $t['id']) ?>" class="text-decoration-none text-black">
                    <div class="card card-brutal mb-3 hover-lift transition-all">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span
                                        class="badge rounded-0 border border-black text-black bg-<?= $t['type'] == 'income' ? 'pastel-green' : 'pastel-red' ?> font-mono mb-1"><?= strtoupper($t['type']) ?></span>
                                    <h6 class="fw-bold mb-0"><?= $t['title'] ?></h6>
                                    <small
                                        class="text-muted font-mono"><?= date('d M Y', strtotime($t['transaction_date'])) ?></small>
                                </div>
                                <div class="text-end">
                                    <h6 class="fw-bold mb-2 <?= $t['type'] == 'income' ? 'text-success' : 'text-danger' ?>">
                                        <?= $t['type'] == 'income' ? '+' : '-' ?>
                                        <?= number_format($t['amount'], 0, ',', '.') ?>
                                    </h6>
                                </div>
                            </div>
                            <div class="border-top border-dashed border-secondary pt-2 mt-2">
                                <small class="font-mono text-muted">Category: <?= $t['category'] ?></small>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>