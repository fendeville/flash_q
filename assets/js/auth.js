document.addEventListener('DOMContentLoaded', function () {
    // Registration form type toggle
    const quickForm = document.getElementById('quickRegistrationForm');
    const detailedForm = document.getElementById('detailedRegistrationForm');
    const toggleBtns = document.querySelectorAll('.toggle-btn');

    if (toggleBtns.length > 0) {
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                toggleBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const type = this.dataset.type;

                if (type === 'quick' && quickForm) {
                    quickForm.classList.remove('hidden');
                    detailedForm.classList.add('hidden');
                } else if (type === 'detailed' && detailedForm) {
                    detailedForm.classList.remove('hidden');
                    quickForm.classList.add('hidden');
                }
            });
        });
    }

    // Password toggle
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');

    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Add/Edit Organization Modal
    const addOrgBtn = document.querySelector('.add-btn');
    const orgModal = document.querySelector('#organizationModal');
    const closeOrgModalBtn = document.querySelector('#closeOrgModal');

    if (addOrgBtn && orgModal) {
        addOrgBtn.addEventListener('click', function () {
            orgModal.classList.remove('hidden');
        });

        closeOrgModalBtn.addEventListener('click', function () {
            orgModal.classList.add('hidden');
        });
    }

    // Refresh Reports
    const refreshReportsBtn = document.querySelector('.refresh-btn');
    if (refreshReportsBtn) {
        refreshReportsBtn.addEventListener('click', function () {
            location.reload(); // Reload the page to refresh reports
        });
    }

    // Save Settings via AJAX
    const settingsForm = document.querySelector('#settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(settingsForm);
            fetch('save_settings.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Settings saved successfully!');
                    } else {
                        alert('Failed to save settings. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving settings.');
                });
        });
    }

    // Input animation on focus
    const inputs = document.querySelectorAll('.form-input');

    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.3s ease';
        });

        input.addEventListener('blur', function () {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});