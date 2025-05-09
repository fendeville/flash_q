<?php
session_start();

// Check if the user has admin access
if (!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = 'Reports';
$showHeader = false;
$extraCss = ['../assets/css/admin.css'];
$extraJs = ['../assets/js/admin.js'];

include '../includes/header.php';

// Include the database connection and functions
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Placeholder for fetching report data (if needed)
$reports = [
    [
        'name' => 'Daily Queue Report',
        'description' => 'Summary of today\'s queue activity',
        'endpoint' => 'generate_daily_report.php',
    ],
    [
        'name' => 'Monthly Performance',
        'description' => 'Overview of monthly queue performance',
        'endpoint' => 'generate_monthly_report.php',
    ],
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
            <a href="reports.php" class="active">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="settings.php">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <div class="admin-header-title">
                <h1>Reports</h1>
                <p>View system reports and analytics</p>
            </div>
        </header>

        <div class="admin-data-section">
            <div class="admin-section-header">
                <h2>Reports</h2>
                <button class="refresh-btn" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="3" class="empty-table">No reports available.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['name']) ?></td>
                                    <td><?= htmlspecialchars($report['description']) ?></td>
                                    <td>
                                        <button class="action-btn download-report-btn" data-endpoint="<?= htmlspecialchars($report['endpoint']) ?>">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const downloadButtons = document.querySelectorAll('.download-report-btn');

        downloadButtons.forEach(button => {
            button.addEventListener('click', function () {
                const endpoint = this.dataset.endpoint;

                // Trigger download
                fetch(endpoint)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to generate report.');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = endpoint.split('_')[1] + '_report.pdf'; // Example: daily_report.pdf
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        alert('An error occurred while downloading the report.');
                        console.error(error);
                    });
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>