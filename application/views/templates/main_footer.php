</div> <!-- End content -->

<!-- Bottom Navigation -->
<div class="mobile-nav">
    <a href="<?= base_url('dashboard') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == '' || $this->uri->segment(2) == 'index') ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>HOME</span>
    </a>

    <a href="<?= base_url('dashboard/transactions') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'transactions') ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        <span>LIST</span>
    </a>

    <a href="<?= base_url('chat') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(1) == 'chat') ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span>CHAT</span>
    </a>

    <!-- Center slot is CHAT (plus button removed; see issue #17) -->

    <a href="<?= base_url('dashboard/stats') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'stats') ? 'active' : '' ?>" data-no-swup>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
        <span>STATS</span>
    </a>

    <a href="<?= base_url('dashboard/profile') ?>"
        class="nav-item-brutal <?= ($this->uri->segment(2) == 'profile') ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>PROFILE</span>
    </a>
</div>


</div> <!-- End App Container -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Icons via inline SVG -->
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"></script>
<!-- Page Initialization Script -->
<script src="<?= base_url('assets/js/page-init.js'); ?>"></script>
</body>

</html>
