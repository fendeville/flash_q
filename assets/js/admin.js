document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    const flashMessage = document.querySelector('.admin-message');
    if (flashMessage) {
        setTimeout(function() {
            flashMessage.style.transition = 'opacity 10s ease';
            flashMessage.style.opacity = '0';
            setTimeout(function() {
                flashMessage.remove();
            }, 500);
        }, 5000);
    }
    
    // Toggle add visitor form
    window.toggleAddVisitorForm = function() {
        const form = document.getElementById('addVisitorForm');
        if (form) {
            form.classList.toggle('hidden');
            
            // Focus on first input if form is shown
            if (!form.classList.contains('hidden')) {
                const firstInput = form.querySelector('input');
                if (firstInput) {
                    firstInput.focus();
                }
            }
        }
    };
    
    // Auto-refresh page every 30 seconds when managing a queue
    const queueManagement = document.querySelector('.admin-queue-management');
    if (queueManagement) {
        setInterval(function() {
            location.reload();
        }, 30000);
    }
    
    // Hover effect for action buttons
    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Update serving timer
    const servingTimer = document.getElementById('servingTimer');
    if (servingTimer && servingTimer.dataset.startTime) {
        const updateTimer = function() {
            const startTime = parseInt(servingTimer.dataset.startTime);
            const elapsed = Math.floor(Date.now() / 1000) - startTime;
            
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            
            servingTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        };
        
        // Update immediately
        updateTimer();
        
        // Update every second
        setInterval(updateTimer, 1000);
    }
    
    // Button click effect
    const buttons = document.querySelectorAll('button:not([disabled])');
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
    
    // Form validation for adding visitor
    const addVisitorForm = document.querySelector('form[name="add_visitor"]');
    if (addVisitorForm) {
        addVisitorForm.addEventListener('submit', function(e) {
            const nameInput = this.querySelector('input[name="visitor_name"]');
            const phoneInput = this.querySelector('input[name="visitor_phone"]');
            
            let isValid = true;
            
            // Validate name
            if (nameInput.value.trim() === '') {
                nameInput.style.borderColor = '#ef4444';
                isValid = false;
            } else {
                nameInput.style.borderColor = '';
            }
            
            // Validate phone
            const phoneRegex = /^\+?[0-9\s\-\(\)]{6,20}$/;
            if (!phoneRegex.test(phoneInput.value.trim())) {
                phoneInput.style.borderColor = '#ef4444';
                isValid = false;
            } else {
                phoneInput.style.borderColor = '';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all fields correctly');
            }
        });
    }
    
    // Confirmation for control buttons
    const controlForm = document.querySelector('.control-form');
    if (controlForm) {
        controlForm.addEventListener('submit', function(e) {
            const submitButton = document.activeElement;
            
            if (submitButton.name === 'next_token') {
                if (!confirm('Are you sure you want to call the next token?')) {
                    e.preventDefault();
                }
            } else if (submitButton.name === 'close_queue') {
                if (!confirm('Are you sure you want to close the queue? This will cancel all waiting tokens.')) {
                    e.preventDefault();
                }
            }
        });
    }
});