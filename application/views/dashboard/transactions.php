<div class="animate-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-mono fw-bold m-0">ALL TRANSACTIONS</h4>
        <button class="btn btn-sm btn-outline-dark border-brutal bg-white font-mono fw-bold" type="button"
            data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <span class="iconify" data-icon="lucide:filter"></span> FILTER
        </button>
    </div>

    <!-- Filter Section (Redesigned: Vertical/Spacious) -->
    <div class="collapse mb-4 <?= (!empty($filter['date']) || !empty($filter['month'])) ? 'show' : '' ?>"
        id="filterCollapse">
        <div class="card card-brutal p-3 bg-white">
            <form id="filterForm" action="<?= base_url('dashboard/transactions') ?>" method="get">
                <div class="mb-3">
                    <label class="font-mono small fw-bold d-block mb-1 text-uppercase">By Date</label>
                    <input type="date" name="date" class="form-control form-control-brutal font-mono"
                        value="<?= htmlspecialchars($filter['date'] ?? '') ?>">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="font-mono small fw-bold d-block mb-1 text-uppercase">By Month</label>
                        <select name="month" class="form-select form-select-brutal font-mono">
                            <option value="">-- All --</option>
                            <?php
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            foreach ($months as $idx => $name):
                                ?>
                                <option value="<?= $idx + 1 ?>" <?= $filter['month'] == ($idx + 1) ? 'selected' : '' ?>>
                                    <?= $name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="font-mono small fw-bold d-block mb-1 text-uppercase">By Year</label>
                        <select name="year" class="form-select form-select-brutal font-mono">
                            <option value="">-- All --</option>
                            <?php
                            $currentYear = date('Y');
                            for ($y = $currentYear; $y >= $currentYear - 5; $y--):
                                ?>
                                <option value="<?= $y ?>" <?= ($filter['year'] == $y || (empty($filter['year']) && !empty($filter['month']) && $y == $currentYear)) ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark border-brutal py-2 font-mono fw-bold">APPLY
                        FILTER</button>
                    <button type="button" data-ajax-reset
                        class="btn btn-outline-dark border-brutal py-2 font-mono fw-bold">RESET ALL</button>
                </div>
            </form>
        </div>
    </div>

    <?= $this->session->flashdata('message'); ?>

    <div id="transactionWrapper" class="position-relative">
        <!-- Local Loader for Filtering -->
        <div id="filterLoader"
            class="d-none position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-75"
            style="z-index: 10;">
            <div class="card card-brutal p-3 bg-pastel-yellow text-center shadow-none border-2">
                <span class="iconify animate-spin-custom" data-icon="lucide:loader-2" data-width="32"></span>
                <div class="font-mono small fw-bold mt-2">REFRESHING...</div>
            </div>
        </div>

        <div id="transactionContainer" class="list-group list-group-flush">
            <?php $this->load->view('dashboard/transaction_list_partial', ['transactions' => $transactions]); ?>
        </div>
    </div>

    <!-- Empty State for filters -->
    <div id="emptyState" class="card card-brutal p-4 text-center bg-white <?= !empty($transactions) ? 'd-none' : '' ?>">
        <p class="font-mono text-muted mb-0">No transactions matches your filter.</p>
    </div>

    <!-- Load More Button -->
    <div id="loadMoreContainer" class="mt-4 mb-5 text-center <?= (count($transactions) < $limit) ? 'd-none' : '' ?>">
        <button id="btnLoadMore"
            class="btn btn-dark border-brutal px-5 py-3 fw-bold font-mono shadow-none transition-all">
            LOAD MORE
        </button>
    </div>
</div>

<script>
    $(document).ready(function () {
        let offset = <?= $limit ?>;
        const limit = <?= $limit ?>;

        // Function to refresh the list via AJAX
        function refreshList(params = '', append = false) {
            const $container = $('#transactionContainer');
            const $loadMoreBtn = $('#btnLoadMore');
            const $loadMoreContainer = $('#loadMoreContainer');
            const $emptyState = $('#emptyState');
            const $filterLoader = $('#filterLoader');

            if (!append) {
                $filterLoader.removeClass('d-none').addClass('d-flex');
            } else {
                $loadMoreBtn.prop('disabled', true).html('<span class="iconify animate-spin-custom" data-icon="lucide:loader-2"></span> LOADING...');
            }

            $.ajax({
                url: '<?= base_url('dashboard/transactions') ?>' + (params ? '?' + params : ''),
                method: 'GET',
                success: function (html) {
                    if (!append) {
                        $container.html(html);
                        offset = limit; // Reset offset for new filter
                    } else {
                        const $newItems = $(html);
                        $container.append($newItems);
                        offset += limit;
                    }

                    // Handle visibility of Load More and Empty State
                    const totalItems = $container.find('.transaction-item').length;
                    const itemsInBatch = $(html).filter('.transaction-item').length;

                    if (totalItems === 0) {
                        $emptyState.removeClass('d-none');
                        $loadMoreContainer.addClass('d-none');
                    } else {
                        $emptyState.addClass('d-none');
                        if (itemsInBatch < limit) {
                            $loadMoreContainer.addClass('d-none');
                        } else {
                            $loadMoreContainer.removeClass('d-none');
                        }
                    }
                },
                error: function () {
                    // Fail silently or log
                    console.error('Data loading error');
                },
                complete: function () {
                    $filterLoader.addClass('d-none').removeClass('d-flex');
                    $loadMoreBtn.prop('disabled', false).html('LOAD MORE');
                }
            });
        }

        // Filters are now only applied on manual "APPLY FILTER" click as requested.

        // Handle Filter Form Submission
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            const params = $(this).serialize();

            // Update URL without reload
            const newUrl = window.location.pathname + '?' + params;
            window.history.pushState({ path: newUrl }, '', newUrl);

            refreshList(params);
        });

        // Handle AJAX Reset
        $('[data-ajax-reset]').on('click', function (e) {
            e.preventDefault();
            $('#filterForm')[0].reset();
            // Reset URL
            window.history.pushState({ path: window.location.pathname }, '', window.location.pathname);
            refreshList();
        });

        // Handle Load More
        $('#btnLoadMore').on('click', function () {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('offset', offset);

            refreshList(urlParams.toString(), true);
        });
    });
</script>