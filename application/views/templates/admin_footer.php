</div> <!-- End swup container -->
</main>
</div> <!-- End admin-layout -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"></script>
<script>
    const swup = new Swup();
    swup.hooks.on('content:replace', () => {
        if (typeof Iconify !== 'undefined') {
            Iconify.scan();
        }
    });

    function closeSidebar() {
        $('#adminSidebar').removeClass('show-mobile');
        $('#sidebarOverlay').removeClass('show');
    }

    $(document).on('click', '#toggleSidebar', function () {
        const sidebar = $('#adminSidebar');
        const overlay = $('#sidebarOverlay');
        
        if ($(window).width() > 992) {
            sidebar.toggleClass('collapsed');
        } else {
            sidebar.toggleClass('show-mobile');
            overlay.toggleClass('show');
        }
    });

    $(document).on('click', '#sidebarOverlay', function () {
        closeSidebar();
    });

    swup.hooks.on('content:replace', () => {
        if (typeof Iconify !== 'undefined') {
            Iconify.scan();
        }
        if ($(window).width() <= 992) {
            closeSidebar();
        }
    });
</script>
</body>

</html>