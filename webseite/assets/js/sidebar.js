document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    // Function to handle sidebar toggle
    function toggleSidebar() {
        sidebar.classList.toggle('active');

        // Add overlay when sidebar is active on mobile/tablet
        if (window.innerWidth <= 1024) {
            if (sidebar.classList.contains('active')) {
                addOverlay();
            } else {
                removeOverlay();
            }
        }
    }

    // Function to add overlay
    function addOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 30;
                transition: opacity 0.3s ease;
            `;
        document.body.appendChild(overlay);

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            removeOverlay();
        });

        // Fade in
        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
        });
    }

    // Function to remove overlay
    function removeOverlay() {
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 300);
        }
    }

    // Add click event to menu toggle
    menuToggle.addEventListener('click', toggleSidebar);

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('active');
                removeOverlay();
            }
        }, 250);
    });
});

// Add this right after your existing toggleSidebar function
function updateMenuVisibility() {
    const menuToggle = document.querySelector('.menu-toggle');
    if (window.innerWidth <= 1024) { // Smartphone breakpoint
        menuToggle.style.display = 'flex';
    } else {
        menuToggle.style.display = 'none';
    }
}

// Add event listeners
window.addEventListener('resize', updateMenuVisibility);