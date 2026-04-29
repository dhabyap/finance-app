</div> <!-- End swup container -->
</main>
</div> <!-- End admin-layout -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/73Y1"
        crossorigin="anonymous"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"
        integrity="sha384-GYcZF/Xz4/6ZHVch5eVcYcyWmSCvO3+ffsxF+B9hfRyc3XCkSws7SO5ZSGqHlUNH"
        crossorigin="anonymous"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"
        integrity="sha384-yzkU2LzN4yZh/Abp/DRUsN1AanVM6aQ8FPHmTpWgu6mzZiXf7L7I3jAWZ00h7fys"
        crossorigin="anonymous"></script>
<!-- Iconify -->
<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"
        integrity="sha384-9e9K0k6ENv0hFklsD5YQ5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5"
        crossorigin="anonymous"></script>
<!-- Swup (Page Transitions) -->
<script src="https://unpkg.com/swup@4"
        integrity="sha384-6nqK0k6ENv0hFklsD5YQ5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5Q5"
        crossorigin="anonymous"></script>
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