// Admin Panel Initialization Script
// This script handles admin-specific interactions and sidebar management

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
