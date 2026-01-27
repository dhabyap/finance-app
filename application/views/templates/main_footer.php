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

    <!-- PLUS BUTTON -->
    <a href="<?= base_url('dashboard/add') ?>" class="nav-item-brutal nav-plus">
        <div class="plus-btn">
            <span class="iconify" data-icon="lucide:plus"></span>
        </div>
    </a>

    <a href="<?= base_url('dashboard/stats') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'stats') ? 'active' : '' ?>">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<script>
    // Add active class handling if needed via JS, though PHP handles it mostly
</script>
</body>

</html>