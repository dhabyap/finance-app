</div> <!-- End content -->

<!-- Bottom Navigation -->
<div class="mobile-nav">
    <a href="<?= base_url('dashboard') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'active' : '' ?>">
        <span class="iconify" data-icon="lucide:home"></span>
        <span>HOME</span>
    </a>

    <a href="<?= base_url('dashboard/transactions') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'transactions') ? 'active' : '' ?>">
        <span class="iconify" data-icon="lucide:list"></span>
        <span>LIST</span>
    </a>

    <a href="<?= base_url('chat') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(1) == 'chat') ? 'active' : '' ?>">
        <span class="iconify" data-icon="lucide:message-square"></span>
        <span>CHAT</span>
    </a>

    <!-- Center slot is CHAT (plus button removed; see issue #17) -->

    <a href="<?= base_url('dashboard/stats') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'stats') ? 'active' : '' ?>" data-no-swup>
        <span class="iconify" data-icon="lucide:pie-chart"></span>
        <span>STATS</span>
    </a>

    <a href="<?= base_url('dashboard/profile') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'profile') ? 'active' : '' ?>">
        <span class="iconify" data-icon="lucide:user"></span>
        <span>PROFILE</span>
    </a>
</div>


</div> <!-- End App Container -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/73Y1"
        crossorigin="anonymous"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"
        integrity="sha384-GYcZF/Xz4/6ZHVch5eVcYcyWmSCvO3+ffsxF+B9hfRyc3XCkSws7SO5ZSGqHlUNH"
        crossorigin="anonymous"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"
        integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ"
        crossorigin="anonymous"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"
        integrity="sha384-yzkU2LzN4yZh/Abp/DRUsN1AanVM6aQ8FPHmTpWgu6mzZiXf7L7I3jAWZ00h7fys"
        crossorigin="anonymous"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"
        integrity="sha384-9e9K0z2Ql1M5fY2zq7K1FfyWd0qLz5v5r5Y5Q5Q5Q5Q5Q5Q5Q5Q5Q5"
        crossorigin="anonymous"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"
        integrity="sha384-+zmW1TzXEXy9W3W7F5k5n5n5n5n5n5n5n5n5n5n5n5n5n5n5n5"
        crossorigin="anonymous"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"
        integrity="sha384-6nqK0k6ENv0hFklsD5YQ5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5"
        crossorigin="anonymous"></script>
<script>
    const swup = new Swup();

    // Run page initialization on first load as well (Swup hook only runs on transitions).
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof initPageScripts === 'function') {
            initPageScripts();
        }
    });

    // Re-initialize scripts after page transition
    swup.hooks.on('content:replace', () => {
        // Re-run Iconify icons scan
        if (typeof Iconify !== 'undefined') {
            Iconify.scan();
        }

        // Trigger page-specific initialization if defined
        if (typeof initPageScripts === 'function') {
            initPageScripts();
        }
    });

    // Add logic to hide loader if it gets stuck
    swup.hooks.on('visit:start', () => {
        // Ensure some visual feedback that transition started if needed
    });

    swup.hooks.on('visit:end', () => {
        // Cleanup or scroll
        window.scrollTo(0, 0);
    });
</script>
</body>

</html>
