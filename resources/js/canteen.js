// resources/js/canteen.js

// Flash message handler
window.addEventListener('DOMContentLoaded', function() {
    // Check for Laravel flash messages from session
    const flashMessage = document.querySelector('meta[name="flash-message"]');
    if (flashMessage) {
        alert(flashMessage.getAttribute('content'));
    }
});