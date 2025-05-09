document.addEventListener('DOMContentLoaded', function() {
    // Simulate loading time (2 seconds)
    setTimeout(function() {
        const startButton = document.getElementById('startButton');
        
        if (startButton) {
            // Add event listener to navigate to registration page
            startButton.addEventListener('click', function() {
                // Animate out
                const preloaderContainer = document.querySelector('.preloader-container');
                preloaderContainer.style.transition = 'opacity 0.5s ease';
                preloaderContainer.style.opacity = '0';
                
                // Redirect after animation
                setTimeout(function() {
                    window.location.href = 'register.php';
                }, 500);
            });
        }
    }, 2000);
});