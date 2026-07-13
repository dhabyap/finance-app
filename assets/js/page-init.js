// Page Initialization Script
// This script handles Swup page transitions and common page setup

const swup = new Swup();

// Run page initialization on first load as well (Swup hook only runs on transitions)
document.addEventListener('DOMContentLoaded', () => {
    if (typeof initPageScripts === 'function') {
        initPageScripts();
    }
});

// Re-initialize scripts after page transition
swup.hooks.on('content:replace', () => {
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
