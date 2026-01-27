<div class="animate-up">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('dashboard/transactions') ?>" class="btn btn-sm border-2 border-black rounded-0 me-3">
            <span class="iconify" data-icon="lucide:arrow-left"></span>
        </a>
        <h4 class="font-mono fw-bold mb-0">DETAILS</h4>
    </div>

    <div class="card card-brutal p-4 mb-4">
        <div class="text-center mb-4">
            <div class="rounded-circle border-2 border-black d-flex align-items-center justify-content-center mx-auto mb-3 bg-<?= $transaction['type'] == 'income' ? 'pastel-green' : 'pastel-red' ?>"
                style="width: 80px; height: 80px;">
                <span class="iconify"
                    data-icon="<?= $transaction['type'] == 'income' ? 'lucide:arrow-down-left' : 'lucide:arrow-up-right' ?>"
                    data-width="40"></span>
            </div>
            <h2 class="fw-bold mb-1 <?= $transaction['type'] == 'income' ? 'text-success' : 'text-danger' ?>">
                <?= $transaction['type'] == 'income' ? '+' : '-' ?>
                <?= number_format($transaction['amount'], 0, ',', '.') ?>
            </h2>
            <span
                class="badge border-2 border-black text-black bg-white font-mono rounded-0"><?= strtoupper($transaction['type']) ?></span>
        </div>

        <div class="border-top border-2 border-black pt-4">
            <div class="mb-3">
                <small class="font-mono text-muted fw-bold">TITLE</small>
                <h5 class="fw-bold mt-1"><?= $transaction['title'] ?></h5>
            </div>
            <div class="mb-3">
                <small class="font-mono text-muted fw-bold">CATEGORY</small>
                <h5 class="fw-bold mt-1"><?= $transaction['category'] ?></h5>
            </div>
            <div class="mb-3">
                <small class="font-mono text-muted fw-bold">DATE</small>
                <h5 class="fw-bold mt-1"><?= date('d F Y', strtotime($transaction['transaction_date'])) ?></h5>
            </div>
        </div>
    </div>

    <a href="<?= base_url('dashboard/delete/' . $transaction['id']) ?>"
        class="btn btn-brutal w-100 py-3 fw-bold bg-pastel-red text-black"
        onclick="return confirm('Are you sure you want to delete this transaction?')">
        DELETE TRANSACTION
    </a>
</div>