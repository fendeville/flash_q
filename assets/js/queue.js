document.addEventListener('DOMContentLoaded', function() {
    // Join Queue confirmation
    const joinQueueForms = document.querySelectorAll('.join-form');
    
    joinQueueForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to join this queue?')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-refresh for view queue page
    if (document.querySelector('.view-queue-container')) {
        // Refresh every 30 seconds
        setInterval(function() {
            // Preserve URL parameters
            location.reload();
        }, 30000);
    }
    
    // Animation for current user's token
    const userToken = document.querySelector('.user-token');
    if (userToken) {
        userToken.classList.add('animate__animated', 'animate__pulse');
    }
    
    // Hover effects for queue options
    const queueOptions = document.querySelectorAll('.queue-option');
    
    queueOptions.forEach(option => {
        option.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 15px rgba(0, 0, 0, 0.1)';
        });
        
        option.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
    
    // Button press effect
    const buttons = document.querySelectorAll('button');
    
    buttons.forEach(button => {
        button.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        button.addEventListener('mouseup', function() {
            this.style.transform = '';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
});