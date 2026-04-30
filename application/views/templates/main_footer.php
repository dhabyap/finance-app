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
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"
        integrity="sha384-GYcZF/Xz4/6ZHVch5eVcYcyWmSCvO3+ffsxF+B9hfRyc3XCkSws7SO5ZSGqHlUNH"
        crossorigin="anonymous"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"
        integrity="sha384-e6nUZLBkQ86NJ6TVVKAeSaK8jWa3NhkYWZFomE39AvDbQWeie9PlQqM3pmYW5d1g"
        crossorigin="anonymous"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"
        integrity="sha384-uhnasqDSrh2YgfIPWBe8VCLIyUBTjcVmqUR1nHVgfVTC08V56BICApRSxJhCoZf2"
        crossorigin="anonymous"></script>
<!-- Page Initialization Script -->
<script src="<?= base_url('assets/js/page-init.js'); ?>"></script>
</body>

</html>
