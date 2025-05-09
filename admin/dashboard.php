<?php
session_start(); //start the session

$pageTitle = 'Admin Dashboard';
$showHeader = true;
$extraCss = ['../assets/css/admin.css'];
$extraJs = ['../assets/js/admin.js'];

include '../includes/header.php';

// Check if user is logged in and is admin
//if (!isLoggedIn() || !isAdmin()) {
   // header('Location: ../index.php');
    //exit;
//}

// Check for admin access
if (!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Include the database connection
require_once '../includes/functions.php';
require_once '../includes/db.php';
$db = new Database();

// Fetch/Get all organizations
$organizations = getAllOrganizations();

// Get statistics
try {
    $stats = $db->select("
        SELECT 
            o.name as organization_name, 
            q.name as queue_name, 
            COUNT(qt.id) as total_tokens,
            SUM(CASE WHEN qt.status = 'completed' THEN 1 ELSE 0 END) as completed_tokens,
            SUM(CASE WHEN qt.status = 'waiting' THEN 1 ELSE 0 END) as waiting_tokens,
            SUM(CASE WHEN qt.status = 'serving' THEN 1 ELSE 0 END) as serving_tokens,
            SUM(CASE WHEN qt.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_tokens
        FROM organizations o
        JOIN queues q ON q.organization_id = o.id
        LEFT JOIN queue_tokens qt ON qt.queue_id = q.id
        WHERE DATE(qt.join_time) = CURDATE() OR qt.join_time IS NULL
        GROUP BY o.id, q.id
        ORDER BY o.name, q.name
    ");
} catch (Exception $e) {
    error_log("Error fetching statistics: " . $e->getMessage());
    $stats = [];
}
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <img src="../assets/images/logo.svg" alt="Flash.Q Logo">
            <span>Flash.Q</span>
        </div>
        
        <nav class="admin-nav">
            <a href="dashboard.php" class="active">
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
            <a href="settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
        
        <div class="admin-logout">
            <a href="../index.php?logout=1">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
    
    <main class="admin-content">
        <header class="admin-header">
            <div class="admin-header-title">
                <h1>Admin Dashboard</h1>
                <p>Overview of queue management system</p>
            </div>
            
            <div class="admin-user">
                <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                <div class="admin-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>
        
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <h3>Organizations</h3>
                    <p><?= count($organizations) ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-list-ol"></i>
                </div>
                <div class="stat-info">
                    <h3>Active Queues</h3>
                    <p><?= count($stats) ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>People in Queues</h3>
                    <p>
                    <?php
                            $totalWaiting = array_sum(array_column($stats, 'waiting_tokens'));
                            echo $totalWaiting;
                        ?>
                    </p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Served Today</h3>
                    <p>
                    <?php
                            $totalCompleted = array_sum(array_column($stats, 'completed_tokens'));
                            echo $totalCompleted;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="admin-data-section">
            <div class="admin-section-header">
                <h2>Today's Queue Status</h2>
                <button class="refresh-btn" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Queue</th>
                            <th>Waiting</th>
                            <th>Serving</th>
                            <th>Completed</th>
                            <th>Cancelled</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats)): ?>
                            <tr>
                                <td colspan="8" class="empty-table">No queue data available for today</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats as $stat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stat['organization_name']) ?></td>
                                    <td><?= htmlspecialchars($stat['queue_name']) ?></td>
                                    <td><?= $stat['waiting_tokens'] ?></td>
                                    <td><?= $stat['serving_tokens'] ?></td>
                                    <td><?= $stat['completed_tokens'] ?></td>
                                    <td><?= $stat['cancelled_tokens'] ?></td>
                                    <td><?= $stat['total_tokens'] ?></td>
                                    <td>
                                        <a href="manage_queue.php?org=<?= $stat['organization_name'] ?>&queue=<?= $stat['queue_name'] ?>" class="action-btn">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-data-section">
            <div class="admin-section-header">
                <h2>Organizations</h2>
                <button class="add-btn">
                    <i class="fas fa-plus"></i> Add New
                </button>
            </div>
            
            <div class="admin-cards">
                <?php foreach ($organizations as $org): ?>
                    <div class="org-card">
                        <div class="org-card-icon">
                            <?php if ($org['category'] === 'Healthcare'): ?>
                                <i class="fas fa-hospital"></i>
                            <?php elseif ($org['category'] === 'Banking'): ?>
                                <i class="fas fa-university"></i>
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
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>