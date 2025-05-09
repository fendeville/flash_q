<?php
session_start();

// Check if the user has admin access
if (!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = 'Organizations';
$showHeader = false;
$extraCss = ['../assets/css/admin.css'];
$extraJs = ['../assets/js/admin.js'];

include '../includes/header.php';

// Include the database connection and functions
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Fetch all organizations
$organizations = getAllOrganizations();
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
            <a href="organizations.php" class="active">
                <i class="fas fa-building"></i>
                <span>Organizations</span>
            </a>
            <a href="reports.php">
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
                <h1>Organizations</h1>
                <p>Manage all organizations in the system</p>
            </div>
        </header>

        <div class="admin-data-section">
            <div class="admin-section-header">
                <h2>Organizations</h2>
                <button class="add-btn" id="addOrganizationBtn">
                    <i class="fas fa-plus"></i> Add New
                </button>
            </div>

            <div class="admin-cards">
                <?php if (empty($organizations)): ?>
                    <p>No organizations found. Click "Add New" to create one.</p>
                <?php else: ?>
                    <?php foreach ($organizations as $org): ?>
                        <div class="org-card">
                            <div class="org-card-icon">
                                <?php if ($org['category'] === 'Healthcare'): ?>
                                    <i class="fas fa-hospital"></i>
                                <?php elseif ($org['category'] === 'Banking'): ?>
                                    <i class="fas fa-university"></i>
                                <?php elseif ($org['category'] === 'Telecommunication'): ?>
                                    <i class="fas fa-mobile-alt"></i>
                                <?php elseif ($org['category'] === 'Utility'): ?>
                                    <i class="fas fa-bolt"></i>
                                <?php else: ?>
                                    <i class="fas fa-building"></i>
                                <?php endif; ?>
                            </div>
                            <div class="org-card-info">
                                <h3><?= htmlspecialchars($org['name']) ?></h3>
                                <p><?= htmlspecialchars($org['category']) ?></p>
                            </div>
                            <div class="org-card-actions">
                                <button class="action-btn edit-btn" data-id="<?= $org['id'] ?>" data-name="<?= htmlspecialchars($org['name']) ?>" data-category="<?= htmlspecialchars($org['category']) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete-btn" data-id="<?= $org['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Add/Edit Organization Modal -->
<div id="organizationModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-xl font-bold mb-4" id="organizationModalTitle">Add Organization</h2>
        <form id="organizationForm">
            <input type="hidden" id="orgId" name="orgId">
            <div class="form-group">
                <label for="orgName">Organization Name</label>
                <input type="text" id="orgName" name="orgName" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="orgCategory">Category</label>
                <select id="orgCategory" name="orgCategory" class="form-input" required>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Banking">Banking</option>
                    <option value="Telecommunication">Telecommunication</option>
                    <option value="Utility">Utility</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="flex justify-end space-x-4 mt-4">
                <button type="button" id="closeOrgModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-xl font-bold mb-4">Delete Organization</h2>
        <p>Are you sure you want to delete this organization?</p>
        <div class="flex justify-end space-x-4 mt-4">
            <button type="button" id="cancelDeleteBtn" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
            <button type="button" id="confirmDeleteBtn" class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>