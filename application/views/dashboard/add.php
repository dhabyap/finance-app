<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0">ADD TRANSACTION</h4>
        <a href="<?= base_url('dashboard/transactions') ?>"
            class="btn btn-sm border-brutal bg-white font-mono fw-bold">BACK</a>
    </div>

    <!-- Choice Card -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card card-brutal bg-pastel-yellow h-100 p-3 text-center active-choice border-4">
                <span class="iconify mb-1" data-icon="lucide:keyboard" data-width="24"></span>
                <small class="d-block font-mono fw-bold">MANUAL</small>
            </div>
        </div>
        <div class="col-6">
            <a href="<?= base_url('import') ?>" class="text-decoration-none text-black" data-no-swup>
                <div class="card card-brutal bg-white h-100 p-3 text-center hover-lift">
                    <span class="iconify mb-1" data-icon="lucide:file-spreadsheet" data-width="24"></span>
                    <small class="d-block font-mono fw-bold">VIA EXCEL</small>
                </div>
            </a>
        </div>
    </div>

    <div class="card card-brutal p-4">
        <div id="ajax-message"></div>
        <form id="addTransactionForm" action="<?= base_url('dashboard/add') ?>" method="post">
            <div class="mb-3">
                <label class="form-label font-mono fw-bold">TYPE</label>
                <div class="d-flex gap-3">
                    <div class="form-check flex-fill">
                        <input class="form-check-input border-2 border-black" type="radio" name="type" id="type1"
                            value="income" checked>
                        <label class="form-check-label font-mono fw-bold" for="type1">
                            INCOME
                        </label>
                    </div>
                    <div class="form-check flex-fill">
                        <input class="form-check-input border-2 border-black" type="radio" name="type" id="type2"
                            value="expense">
                        <label class="form-check-label font-mono fw-bold" for="type2">
                            EXPENSE
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">TITLE</label>
                <input type="text" name="title" class="form-control form-control-brutal"
                    placeholder="e.g. Salary, Coffee" required>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">AMOUNT</label>
                <div class="input-group">
                    <span class="input-group-text border-brutal bg-white fw-bold">Rp</span>
                    <input type="text" name="amount" class="form-control form-control-brutal" placeholder="0"
                        inputmode="numeric" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">CATEGORY</label>
                <div id="category-select-wrapper">
                    <select id="categorySelect" name="category" class="form-select form-control-brutal" required>
                        <option value="">-- Select Category --</option>
                        <!-- Dynamic options -->
                        <option value="NEW_CATEGORY">+ ADD NEW CATEGORY...</option>
                    </select>
                </div>
                <div id="new-category-wrapper" class="mt-2 d-none">
                    <div class="input-group">
                        <input type="text" id="newCategoryInput" class="form-control form-control-brutal"
                            placeholder="Type new category name...">
                        <button type="button" id="btnCancelNew"
                            class="btn btn-outline-danger border-brutal bg-white px-2">
                            <span class="iconify" data-icon="lucide:x"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label font-mono fw-bold">PAYEE / RECEIVER</label>
                <input type="text" name="payee" class="form-control form-control-brutal"
                    placeholder="e.g. Starbucks, John Doe">
            </div>

            <!-- Description removed as per user request -->

            <div class="mb-4">
                <label class="form-label font-mono fw-bold">DATE</label>
                <input type="date" name="date" class="form-control form-control-brutal" value="<?= date('Y-m-d') ?>"
                    required>
            </div>

            <button type="submit" id="btnSave" class="btn btn-brutal btn-primary-brutal w-100 py-3 fw-bold">
                SAVE TRANSACTION
            </button>
        </form>
    </div>
</div>

<script>
    function initPageScripts() {
        // Thousand Separator Formatter
        function formatNumber(val) {
            val = val.replace(/\D/g, '');
            return val ? new Intl.NumberFormat('id-ID').format(val) : '';
        }

        const $amountInput = $('input[name="amount"]');
        $amountInput.on('input', function () {
            $(this).val(formatNumber($(this).val()));
        });

        // Category Filtering Logic
        const allCategories = <?= json_encode($categories) ?>;
        const $typeRadios = $('input[name="type"]');
        const $categorySelect = $('#categorySelect');
        const $categoryWrapper = $('#category-select-wrapper');
        const $newCategoryWrapper = $('#new-category-wrapper');
        const $newCategoryInput = $('#newCategoryInput');
        const $btnCancelNew = $('#btnCancelNew');

        function updateCategoryList() {
            const selectedType = $('input[name="type"]:checked').val();
            const currentVal = $categorySelect.val();

            // Keep "NEW_CATEGORY" option, "Select Category" option, and add filtered ones
            $categorySelect.html('<option value="">-- Select Category --</option>');

            const filtered = allCategories.filter(cat => cat.type === selectedType);
            filtered.forEach(cat => {
                $categorySelect.append(`<option value="${cat.name}">${cat.name}</option>`);
            });

            $categorySelect.append('<option value="NEW_CATEGORY">+ ADD NEW CATEGORY...</option>');

            // Re-select if still valid
            if (currentVal && currentVal !== 'NEW_CATEGORY') {
                $categorySelect.val(currentVal);
            }
        }

        $categorySelect.on('change', function () {
            if ($(this).val() === 'NEW_CATEGORY') {
                $categoryWrapper.addClass('d-none');
                $newCategoryWrapper.removeClass('d-none');
                $newCategoryInput.focus();
                // Set the value of the actual name field to empty so user has to type
                $(this).val('');
            }
        });

        $btnCancelNew.on('click', function () {
            $newCategoryWrapper.addClass('d-none');
            $categoryWrapper.removeClass('d-none');
            $categorySelect.val('');
            $newCategoryInput.val('');
        });

        $typeRadios.on('change', updateCategoryList);
        updateCategoryList(); // Initial call

        $('#addTransactionForm').off('submit').on('submit', function (e) {
            e.preventDefault();

            // Prepare data
            const $form = $(this);
            const formData = $form.serializeArray();

            // Handle amount dots and category name
            let hasNewCategory = !$newCategoryWrapper.hasClass('d-none');

            for (let i = 0; i < formData.length; i++) {
                if (formData[i].name === 'amount') {
                    formData[i].value = formData[i].value.replace(/\./g, '');
                }
                if (formData[i].name === 'category' && hasNewCategory) {
                    formData[i].value = $newCategoryInput.val();
                }
            }

            // Validation for new category
            if (hasNewCategory && !$newCategoryInput.val().trim()) {
                alert('Please enter a category name');
                return;
            }

            // Show loader
            document.getElementById('loader-overlay').classList.add('active');

            const $btn = $('#btnSave');
            const $msg = $('#ajax-message');
            const originalBtnText = $btn.text();

            $btn.prop('disabled', true).text('SAVING...');
            $msg.html('');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $.param(formData),
                dataType: 'json',
                success: function (response) {
                    document.getElementById('loader-overlay').classList.remove('active');
                    if (response.status === 'success') {
                        $msg.html('<div class="alert alert-success border-brutal bg-pastel-green mb-3 d-flex justify-content-between align-items-center">' +
                            '<span>' + response.message + '</span>' +
                            '<button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $form[0].reset();
                        // Reset wrappers
                        $newCategoryWrapper.addClass('d-none');
                        $categoryWrapper.removeClass('d-none');
                        updateCategoryList();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        $msg.html('<div class="alert alert-danger border-brutal mb-3">' + response.message + '</div>');
                    }
                },
                error: function () {
                    document.getElementById('loader-overlay').classList.remove('active');
                    $msg.html('<div class="alert alert-danger border-brutal mb-3">Something went wrong. Please try again.</div>');
                },
                complete: function () {
                    $btn.prop('disabled', false).text(originalBtnText);
                }
            });
        });
    }

    $(document).ready(initPageScripts);
</script>