<?php if (!empty($transactions)): ?>
    <?php foreach ($transactions as $t): ?>
        <a href="<?= base_url('dashboard/detail/' . $t['id']) ?>" class="text-decoration-none text-black transaction-item">
            <div class="card card-brutal mb-3 hover-lift transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span
                                class="badge rounded-0 border border-black text-black bg-<?= $t['type'] == 'income' ? 'pastel-green' : 'pastel-red' ?> font-mono mb-1"><?= strtoupper($t['type']) ?></span>
                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($t['title']) ?></h6>
                            <small class="text-muted font-mono"><?= date('d M Y', strtotime($t['transaction_date'])) ?></small>
                        </div>
                        <div class="text-end">
                            <h6 class="fw-bold mb-2 <?= $t['type'] == 'income' ? 'text-success' : 'text-danger' ?>">
                                <?= $t['type'] == 'income' ? '+' : '-' ?>
                                <?= number_format($t['amount'], 0, ',', '.') ?>
                            </h6>
                        </div>
                    </div>
                    <div class="border-top border-dashed border-secondary pt-2 mt-2">
                        <div class="d-flex justify-content-between">
                            <small class="font-mono text-muted">Cat: <?= htmlspecialchars($t['category']) ?></small>
                            <?php if ($t['payee']): ?>
                                <small class="font-mono text-muted">To: <?= htmlspecialchars($t['payee']) ?></small>
                            <?php endif; ?>
                        </div>
                        <?php if ($t['description']): ?>
                            <div class="mt-1">
                                <small class="font-mono text-muted italic">"<?= htmlspecialchars($t['description']) ?>"</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>