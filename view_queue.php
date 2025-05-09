<?php
$pageTitle = 'View Queue';
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

// Check if a queue is selected
$selectedQueue = null;
$queueStatus = null;
if (isset($_GET['queue']) && is_numeric($_GET['queue'])) {
    $selectedQueue = (int)$_GET['queue'];
    $queueStatus = getCurrentQueueStatus($selectedQueue);
}

// Handle leave queue
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_queue'])) {
    $tokenId = $_POST['token_id'] ?? 0;
    
    // Update token status to cancelled
    $db->update('queue_tokens', 
        ['status' => 'cancelled'],
        "id = " . $tokenId . " AND user_id = " . $_SESSION['user_id']
    );
    
    $message = 'You have left the queue.';
    $messageType = 'success';
    
    // Redirect to remove the queue parameter
    header('Location: view_queue.php?org=' . $selectedOrg);
    exit;
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
            <?php if ($selectedOrg && empty($queues)): ?>
                <div class="no-queues-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>No active queues found for this organization.</p>
                </div>
            <?php elseif ($selectedOrg && !$selectedQueue): ?>
                <div class="queue-list-container">
                    <h2>Available Queues</h2>
                    <div class="queues-list">
                        <?php foreach ($queues as $queue): ?>
                            <a href="?org=<?= $selectedOrg ?>&queue=<?= $queue['id'] ?>" class="queue-option">
                                <div class="queue-details">
                                    <h3><?= htmlspecialchars($queue['name']) ?></h3>
                                    <p>View current queue status</p>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($selectedQueue): ?>
                <?php 
                    // Get queue details
                    $queueDetails = getQueueDetails($selectedQueue);
                    
                    // Check if user is in this queue
                    $userToken = $db->selectOne(
                        "SELECT * FROM queue_tokens 
                         WHERE queue_id = :queue_id AND user_id = :user_id AND status IN ('waiting', 'serving')",
                        ['queue_id' => $selectedQueue, 'user_id' => $_SESSION['user_id']]
                    );
                ?>
                
                <div class="view-queue-container">
                    <h2>Current Queue</h2>
                    
                    <div class="queue-stats">
                        <div class="queue-stat">
                            <div class="stat-label">Now Serving</div>
                            <div class="stat-value serving-number">
                                <?= $queueStatus['current_serving'] ? sprintf('%03d', $queueStatus['current_serving']['token_number']) : '000' ?>
                            </div>
                        </div>
                        
                        <div class="queue-stat">
                            <div class="stat-label">In Queue</div>
                            <div class="stat-value"><?= $queueStatus['queue_length'] ?></div>
                        </div>
                        
                        <div class="queue-stat">
                            <div class="stat-label">Avg. Wait Time</div>
                            <div class="stat-value">
                                <?= formatTime($queueStatus['estimated_wait_time']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($userToken): ?>
                        <div class="user-token">
                            <div class="user-token-header">
                                <h3>Your Position</h3>
                            </div>
                            <div class="user-token-content">
                                <div class="token-number"><?= sprintf('%03d', $userToken['token_number']) ?></div>
                                <div class="token-status">
                                    <?php if ($userToken['status'] === 'serving'): ?>
                                        <span class="status-badge serving">Now Serving</span>
                                    <?php else: ?>
                                        <?php 
                                            $position = 0;
                                            foreach ($queueStatus['waiting_tokens'] as $index => $token) {
                                                if ($token['id'] === $userToken['id']) {
                                                    $position = $index + 1;
                                                    break;
                                                }
                                            }
                                        ?>
                                        <span class="status-badge waiting">Position: <?= $position ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="token-info">
                                    <div class="info-item">
                                        <span class="info-label">Join Time:</span>
                                        <span class="info-value"><?= date('h:i A', strtotime($userToken['join_time'])) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Estimated Wait:</span>
                                        <span class="info-value">
                                            <?php
                                                $waitTime = 0;
                                                if ($position && $queueStatus['estimated_wait_time']) {
                                                    $waitTime = $position * ($queueStatus['estimated_wait_time'] / max(1, $queueStatus['queue_length']));
                                                }
                                                echo formatTime($waitTime);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to leave the queue?');">
                                    <input type="hidden" name="token_id" value="<?= $userToken['id'] ?>">
                                    <button type="submit" name="leave_queue" class="leave-queue-btn">
                                        <i class="fas fa-sign-out-alt"></i> Leave Queue
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="queue-table">
                        <div class="queue-table-header">
                            <div class="queue-header-cell">Position</div>
                            <div class="queue-header-cell">Name</div>
                            <div class="queue-header-cell">Time</div>
                        </div>
                        
                        <?php if ($queueStatus['current_serving']): ?>
                            <?php
                                $servingUser = $db->selectOne(
                                    "SELECT full_name FROM users WHERE id = :user_id",
                                    ['user_id' => $queueStatus['current_serving']['user_id']]
                                );
                            ?>
                            <div class="queue-table-row serving">
                                <div class="queue-cell"><?= sprintf('%03d', $queueStatus['current_serving']['token_number']) ?></div>
                                <div class="queue-cell"><?= htmlspecialchars($servingUser['full_name']) ?></div>
                                <div class="queue-cell"><?= date('h:i A', strtotime($queueStatus['current_serving']['serving_time'])) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach ($queueStatus['waiting_tokens'] as $index => $token): ?>
                            <div class="queue-table-row <?= $userToken && $token['id'] === $userToken['id'] ? 'current-user' : '' ?>">
                                <div class="queue-cell"><?= sprintf('%03d', $token['token_number']) ?></div>
                                <div class="queue-cell"><?= htmlspecialchars($token['full_name']) ?></div>
                                <div class="queue-cell"><?= date('h:i A', strtotime($token['join_time'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($queueStatus['waiting_tokens']) && !$queueStatus['current_serving']): ?>
                            <div class="queue-table-row empty">
                                <div class="queue-cell" colspan="3">No one is currently in the queue</div>
                            </div>
                        <?php endif; ?>
                    </div>
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
        
        // Auto-refresh the queue status every 30 seconds
        <?php if ($selectedQueue): ?>
        setInterval(function() {
            location.reload();
        }, 30000);
        <?php endif; ?>
    });
</script>

<?php include 'includes/footer.php'; ?>