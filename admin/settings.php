<?php
session_start();

// Check if the user has admin access
if (!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = 'Settings';
$showHeader = false;
$extraCss = ['../assets/css/admin.css'];
$extraJs = ['../assets/js/admin.js'];

include '../includes/header.php';

// Include the database connection and functions
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Fetch current settings (placeholder for database integration)
$settings = [
    'site_name' => 'Flash.Q',
    'admin_email' => 'admin@flashq.com',
];
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <nav class="admin-nav">
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="manage_queue.php">
                <i class="fas fa-users"></i>
                <span>Manage Queue</span>
            </a>
            <a href="organizations.php">
                <i class="fas fa-building"></i>
                <span>Organizations</span>
            </a>
            <a href="reports.php">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="settings.php" class="active">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <div class="admin-header-title">
                <h1>Settings</h1>
                <p>Manage system settings</p>
            </div>
        </header>

        <div class="admin-data-section">
            <div class="admin-section-header">
                <h2>Settings</h2>
            </div>

            <form id="settingsForm" class="admin-form">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="admin_email">Admin Email</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?= htmlspecialchars($settings['admin_email']) ?>" class="form-input" required>
                </div>
                <button type="submit" class="submit-btn">
                    Save Settings
                </button>
            </form>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>

<?php include '../includes/footer.php'; ?>