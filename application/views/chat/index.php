<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0">AI CHAT</h4>
        <div class="d-flex gap-2">
            <a href="<?= base_url('chat/clear_history') ?>" class="btn btn-sm border-brutal bg-white font-mono fw-bold"
                onclick="return confirm('Clear all chat history?')">CLEAR</a>
        </div>
    </div>

    <div class="card card-brutal p-3">
        <div id="chatMessages" style="max-height: 60vh; overflow:auto;">
            <?php foreach ($messages as $m): ?>
                <?php
                $isUser = ($m['role'] === 'user');
                $meta = null;
                if (!empty($m['meta_json'])) {
                    $meta = json_decode($m['meta_json'], true);
                }
                ?>
                <div class="mb-3 d-flex <?= $isUser ? 'justify-content-end' : 'justify-content-start' ?>">
                    <div class="p-3 border-brutal <?= $isUser ? 'bg-pastel-blue text-black' : 'bg-white' ?>"
                        style="max-width: 85%;">
                        <div class="font-mono small fw-bold mb-1"><?= $isUser ? 'YOU' : 'ASSISTANT' ?></div>
                        <div class="font-mono" style="white-space: pre-wrap;"><?= htmlspecialchars($m['content']) ?></div>

                        <?php if (!$isUser && is_array($meta) && ($meta['intent'] ?? '') === 'create_transaction' && !empty($meta['draft'])): ?>
                            <?php $d = $meta['draft']; ?>
                            <div class="mt-3 p-3 border-brutal bg-pastel-yellow">
                                <div class="font-mono fw-bold mb-2">DRAFT</div>
                                <div class="font-mono small">Type: <b><?= htmlspecialchars($d['type']) ?></b></div>
                                <div class="font-mono small">Amount: <b>Rp <?= number_format((int)$d['amount'], 0, ',', '.') ?></b></div>
                                <div class="font-mono small">Date: <b><?= htmlspecialchars($d['transaction_date']) ?></b></div>
                                <div class="font-mono small">Category: <b><?= htmlspecialchars($d['category']) ?></b></div>
                                <div class="font-mono small">Title: <b><?= htmlspecialchars($d['title']) ?></b></div>
                                <div class="font-mono small">Payee: <b><?= htmlspecialchars($d['payee']) ?></b></div>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="button"
                                        class="btn btn-sm btn-brutal bg-white font-mono fw-bold btn-confirm-draft"
                                        data-draft='<?= htmlspecialchars(json_encode($d), ENT_QUOTES, "UTF-8") ?>'>
                                        CONFIRM
                                    </button>
                                    <a class="btn btn-sm border-brutal bg-white font-mono fw-bold"
                                        href="<?= base_url('dashboard/add') ?>?type=<?= urlencode($d['type']) ?>&title=<?= urlencode($d['title']) ?>&amount=<?= urlencode((string)$d['amount']) ?>&category=<?= urlencode($d['category']) ?>&payee=<?= urlencode($d['payee']) ?>&date=<?= urlencode($d['transaction_date']) ?>">
                                        EDIT
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr class="my-3">

        <form id="chatForm" class="d-flex gap-2">
            <input type="text" id="chatInput" class="form-control form-control-brutal font-mono"
                placeholder="Ketik transaksi... (contoh: beli kopi 25k kemarin)" autocomplete="off">
            <button type="submit" class="btn btn-brutal bg-pastel-green font-mono fw-bold">SEND</button>
        </form>

        <div id="chatError" class="mt-3 d-none"></div>
    </div>
</div>

<script>
    function initPageScripts() {
        const threadId = <?= (int)$thread['id'] ?>;
        const $messages = $('#chatMessages');
        const $form = $('#chatForm');
        const $input = $('#chatInput');
        const $error = $('#chatError');

        function scrollBottom() {
            $messages.scrollTop($messages[0].scrollHeight);
        }

        scrollBottom();

        function showError(msg) {
            $error.removeClass('d-none').html(
                '<div class="alert alert-danger border-brutal mb-0">' + msg + '</div>'
            );
        }

        function clearError() {
            $error.addClass('d-none').html('');
        }

        $form.on('submit', function (e) {
            e.preventDefault();
            clearError();
            const text = ($input.val() || '').trim();
            if (!text) return;

            $input.prop('disabled', true);
            $form.find('button[type="submit"]').prop('disabled', true);

            $.ajax({
                url: "<?= base_url('chat/send/' . (int)$thread['id']) ?>",
                method: "POST",
                dataType: "json",
                data: { message: text },
                success: function (res) {
                    if (res.status !== 'success') {
                        showError(res.message || 'Failed');
                        return;
                    }
                    // Simpler: reload thread page to render server-side meta/draft.
                    window.location.reload();
                },
                error: function (xhr) {
                    showError(xhr.responseText || 'Error');
                },
                complete: function () {
                    $input.prop('disabled', false).val('').focus();
                    $form.find('button[type="submit"]').prop('disabled', false);
                }
            });
        });

        window.confirmDraft = function (threadId, draft) {
            clearError();
            document.getElementById('loader-overlay')?.classList.add('active');

            $.ajax({
                url: "<?= base_url('chat/confirm/' . (int)$thread['id']) ?>",
                method: "POST",
                dataType: "json",
                data: {
                    type: draft.type,
                    title: draft.title,
                    amount: draft.amount,
                    category: draft.category,
                    payee: draft.payee,
                    transaction_date: draft.transaction_date
                },
                success: function (res) {
                    if (res.status !== 'success') {
                        showError(res.message || 'Failed');
                        return;
                    }
                    window.location.reload();
                },
                error: function (xhr) {
                    showError(xhr.responseText || 'Error');
                },
                complete: function () {
                    document.getElementById('loader-overlay')?.classList.remove('active');
                }
            });
        }

        // Avoid inline onclick; works on first load and after Swup transitions.
        $(document).off('click.btnConfirmDraft').on('click.btnConfirmDraft', '.btn-confirm-draft', function () {
            const raw = $(this).attr('data-draft') || '{}';
            let draft = null;
            try {
                draft = JSON.parse(raw);
            } catch (e) {
                showError('Invalid draft payload');
                return;
            }
            if ($(this).prop('disabled')) return;
            $(this).prop('disabled', true);
            window.confirmDraft(threadId, draft);
        });
    }
</script>

