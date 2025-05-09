<?php
$pageTitle = 'Join Queue';
$showHeader = true;
$extraCss = ['assets/css/queue.css'];
$extraJs = ['assets/js/queue.js'];

include 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get all organizations
$organizations = getAllOrganizations();

// Check if an organization is selected
$selectedOrg = null;
$queues = [];
if (isset($_GET['org']) && is_numeric($_GET['org'])) {
    $selectedOrg = (int)$_GET['org'];
    $queues = getQueuesForOrganization($selectedOrg);
}

// Handle queue join
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_queue'])) {
    $queueId = $_POST['queue_id'] ?? 0;
    $userId = $_SESSION['user_id'];
    
    $result = addToQueue($queueId, $userId, $_SESSION['user_name'], '');
    
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}
?>

<div class="queue-container">
    <div class="queue-header">
        <div class="flex items-center justify-between">
            <div class="queue-logo">
                <img src="assets/images/logo.svg" alt="Flash.Q Logo">
                <h1>Flash.Q</h1>
            </div>
            <div class="queue-date-time">
                <div class="date" id="currentDate"></div>
                <div class="time" id="currentTime"></div>
            </div>
        </div>
    </div>
    
    <?php if ($message): ?>
        <div class="message message-<?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <div class="queue-content">
        <div class="queue-selector">
            <h2>Select Organization</h2>
            <div class="org-list">
                <?php foreach ($organizations as $org): ?>
                    <a href="?org=<?= $org['id'] ?>" class="org-item <?= $selectedOrg === $org['id'] ? 'active' : '' ?>">
                        <div class="org-icon">
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
                        <div class="org-info">
                            <div class="org-name"><?= htmlspecialchars($org['name']) ?></div>
                            <div class="org-category"><?= htmlspecialchars($org['category']) ?></div>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="queue-info">
            <?php if ($selectedOrg): ?>
                <div class="current-queue-container">
                    <h2>Available Queues</h2>
                    
                    <?php if (empty($queues)): ?>
                        <div class="no-queues-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>No active queues found for this organization.</p>
                        </div>
                    <?php else: ?>
                        <div class="queues-list">
                            <?php foreach ($queues as $queue): ?>
                                <div class="queue-option">
                                    <div class="queue-details">
                                        <h3><?= htmlspecialchars($queue['name']) ?></h3>
                                        <p>Join this queue to get service for <?= htmlspecialchars($queue['name']) ?></p>
                                    </div>
                                    <form method="POST" class="join-form">
                                        <input type="hidden" name="queue_id" value="<?= $queue['id'] ?>">
                                        <button type="submit" name="join_queue" class="join-queue-btn">
                                            <i class="fas fa-plus-circle"></i> Join Queue
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="select-org-prompt">
                    <i class="fas fa-hand-point-left"></i>
                    <p>Please select an organization from the list to view available queues</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            
            // Update date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);
            
            // Update time
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
        }
        
        // Initial update
        updateDateTime();
        
        // Update every second
        setInterval(updateDateTime, 1000);
    });
</script>

<?php include 'includes/footer.php'; ?>