<?php
session_start();
ob_start(); // Start output buffering

require_once __DIR__ . '/../includes/config.php'; // Correct path to config.php
require_once __DIR__ . '/../includes/functions.php'; // Correct path to functions.php

$pageTitle = 'Manage Queue';
$showHeader = false;
$extraCss = ['../assets/css/admin.css'];
$extraJs = ['../assets/js/admin.js'];

include '../includes/header.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

// Get all organizations
$organizations = getAllOrganizations();

// Check if an organization is selected
$selectedOrg = null;
$queues = [];
if (isset($_GET['org']) && !empty($_GET['org'])) {
    $orgName = $_GET['org'];

    // Find organization by name
    foreach ($organizations as $org) {
        if ($org['name'] === $orgName) {
            $selectedOrg = $org['id'];
            break;
        }
    }

    if ($selectedOrg) {
        $queues = getQueuesForOrganization($selectedOrg);
    }
}

// Check if a queue is selected
$selectedQueue = null;
$queueStatus = null;
$queueDetails = null;
$servingToken = null;
if (isset($_GET['queue']) && !empty($_GET['queue'])) {
    $queueName = $_GET['queue'];

    // Find queue by name
    foreach ($queues as $queue) {
        if ($queue['name'] === $queueName) {
            $selectedQueue = $queue['id'];
            break;
        }
    }

    if ($selectedQueue) {
        $queueDetails = getQueueDetails($selectedQueue);
        $queueStatus = getCurrentQueueStatus($selectedQueue);

        // Get serving token if any
        $servingToken = $db->selectOne(
            "SELECT qt.*, u.full_name 
             FROM queue_tokens qt 
             JOIN users u ON qt.user_id = u.id 
             WHERE qt.queue_id = :queue_id AND qt.status = 'serving'",
            ['queue_id' => $selectedQueue]
        );

        // Get waiting tokens
        $waitingTokens = $db->select(
            "SELECT qt.*, u.full_name, u.phone_number, u.email 
             FROM queue_tokens qt 
             JOIN users u ON qt.user_id = u.id 
             WHERE qt.queue_id = :queue_id AND qt.status = 'waiting' 
             ORDER BY qt.token_number ASC",
            ['queue_id' => $selectedQueue]
        );
    }
}

// Notify users in the queue
function notifyUsersInQueue($queueId) {
    global $db;

    // Get all waiting tokens for the queue
    $waitingTokens = $db->select(
        "SELECT qt.token_number, qt.user_id, u.full_name, u.phone_number, u.email 
         FROM queue_tokens qt 
         JOIN users u ON qt.user_id = u.id 
         WHERE qt.queue_id = :queue_id AND qt.status = 'waiting' 
         ORDER BY qt.token_number ASC",
        ['queue_id' => $queueId]
    );

    // Notify the first few users in the queue
    foreach ($waitingTokens as $index => $token) {
        $position = $index + 1; // Position in the queue (1-based index)

        // Notify the first 5 users in the queue
        if ($position <= 5) {
            $message = "Hello " . htmlspecialchars($token['full_name']) . ", ";
            if ($position === 1) {
                $message .= "it will be your turn soon. Please make your way to the counter.";
            } else {
                $message .= "there are $position persons ahead of you. Make haste!";
            }

            // Send SMS or Email
            sendNotification($token['phone_number'], $token['email'], $message);
        }
    }
}

// Function to send SMS or Email
function sendNotification($phoneNumber, $email, $message) {
    // Example: Sending SMS via Twilio
    if (!empty($phoneNumber)) {
        // Replace with your Twilio API credentials
        $accountSid = 'your_account_sid';
        $authToken = 'your_auth_token';
        $twilioNumber = 'your_twilio_number';

        $url = "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json";
        $data = [
            'From' => $twilioNumber,
            'To' => $phoneNumber,
            'Body' => $message,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_exec($ch);
        curl_close($ch);
    }

    // Example: Sending Email via PHPMailer
    if (!empty($email)) {
        require_once '../includes/PHPMailer.php'; // Include PHPMailer library

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // Replace with your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@example.com'; // Replace with your email
            $mail->Password = 'your_email_password'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@example.com', 'Flash.Q'); // Replace with your sender email and name
            $mail->addAddress($email); // Add recipient email

            $mail->isHTML(true);
            $mail->Subject = 'Queue Notification'; // Email subject
            $mail->Body = $message; // Email body (HTML or plain text)

            $mail->send(); // Send the email
        } catch (Exception $e) {
            error_log("Email could not be sent. Error: {$mail->ErrorInfo}"); // Log any errors
        }
    }
}

// Handle admin actions
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $queueId = $_POST['queue_id'] ?? 0;

    if (isset($_POST['next_token'])) {
        $result = serveNextToken($queueId);

        if ($result['success']) {
            // Notify users in the queue
            notifyUsersInQueue($queueId);

            $message = $result['message'];
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    } elseif (isset($_POST['add_visitor'])) {
        $name = $_POST['visitor_name'] ?? '';
        $phone = $_POST['visitor_phone'] ?? '';

        if (empty($name) || empty($phone)) {
            $message = 'Please provide both name and phone number';
            $messageType = 'error';
        } else {
            // Find or create user
            $user = $db->selectOne("SELECT * FROM users WHERE phone_number = :phone", ['phone' => $phone]);

            if (!$user) {
                // Create temporary user
                $userId = $db->insert('users', [
                    'full_name' => $name,
                    'phone_number' => $phone,
                    'password' => password_hash('temp123', PASSWORD_DEFAULT)
                ]);
            } else {
                $userId = $user['id'];
            }

            $result = addToQueue($queueId, $userId, $name, $phone);

            if ($result['success']) {
                $message = 'Visitor added to queue: Token #' . $result['token_number'];
                $messageType = 'success';
            } else {
                $message = $result['message'];
                $messageType = 'error';
            }
        }
    }

    // Redirect to refresh the page data
    if (!empty($message)) {
        $redirect = "manage_queue.php?";

        if ($selectedOrg) {
            foreach ($organizations as $org) {
                if ($org['id'] === $selectedOrg) {
                    $redirect .= "org=" . urlencode($org['name']);
                    break;
                }
            }
        }

        if ($selectedQueue) {
            foreach ($queues as $queue) {
                if ($queue['id'] === $selectedQueue) {
                    $redirect .= "&queue=" . urlencode($queue['name']);
                    break;
                }
            }
        }

        $_SESSION['admin_message'] = $message;
        $_SESSION['admin_message_type'] = $messageType;

        header("Location: " . $redirect);
        exit;
    }
}

// Check for flash message
if (isset($_SESSION['admin_message'])) {
    $message = $_SESSION['admin_message'];
    $messageType = $_SESSION['admin_message_type'];
    unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
}

ob_end_flush(); // Flush the output buffer
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <img src="../assets/images/logo.svg" alt="Flash.Q Logo">
            <span>Flash.Q</span>
        </div>

        <nav class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="manage_queue.php" class="active"><i class="fas fa-users"></i><span>Manage Queue</span></a>
            <a href="organizations.php"><i class="fas fa-building"></i><span>Organizations</span></a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>

        <div class="admin-logout">
            <a href="../index.php?logout=1"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <div class="admin-header-title">
                <h1>Manage Queue</h1>
                <p>Control and monitor queues</p>
            </div>
            <div class="admin-user">
                <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                <div class="admin-avatar"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <?php if ($message): ?>
            <div class="admin-message admin-message-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
                <button class="close-message"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>

        <div class="admin-queue-manager">
            <div class="admin-queue-selector">
                <h2>Select Organization & Queue</h2>
                <div class="selector-group">
                    <label for="organization">Organization</label>
                    <select id="organization" onchange="organizationChanged(this.value)">
                        <option value="">-- Select Organization --</option>
                        <?php foreach ($organizations as $org): ?>
                            <option value="<?= htmlspecialchars($org['name']) ?>" <?= ($selectedOrg === $org['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($org['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($selectedOrg): ?>
                    <div class="selector-group">
                        <label for="queue">Queue</label>
                        <select id="queue" onchange="queueChanged(this.value)">
                            <option value="">-- Select Queue --</option>
                            <?php foreach ($queues as $queue): ?>
                                <option value="<?= htmlspecialchars($queue['name']) ?>" <?= ($selectedQueue === $queue['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($queue['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($selectedQueue && $queueDetails): ?>
                <div class="admin-queue-management">
                    <div class="queue-management-header">
                        <h2><?= htmlspecialchars($queueDetails['organization_name']) ?> - <?= htmlspecialchars($queueDetails['name']) ?></h2>
                        <div class="queue-timestamp">
                            <?= date('l, F j, Y') ?> | <span id="currentTime"></span>
                        </div>
                    </div>

                    <div class="queue-management-container">
                        <div class="queue-control-panel">
                            <div class="serving-info">
                                <div class="now-serving">
                                    <h3>Now Serving</h3>
                                    <div class="token-display">
                                        <?= $servingToken ? sprintf('%03d', $servingToken['token_number']) : '000' ?>
                                    </div>
                                </div>

                                <div class="total-served">
                                    <h3>Total Served Tokens</h3>
                                    <div class="served-display">
                                        <?= $queueStatus['total_served'] ?? 0 ?>
                                    </div>
                                </div>
                            </div>

                            <div class="serving-timer">
                                <h3>Serving Timer</h3>
                                <div id="servingTimer" data-start-time="<?= isset($servingToken) ? strtotime($servingToken['serving_time']) : 0 ?>">
                                     00:00:00
                                </div>
                            </div>

                            <div class="control-buttons">
                                <form method="POST" class="control-form">
                                    <input type="hidden" name="queue_id" value="<?= $selectedQueue ?>">

                                    <button type="submit" name="next_token" class="control-btn next-btn" <?= $queueStatus['queue_length'] === 0 ? 'disabled' : '' ?>>
                                        <i class="fas fa-forward"></i> Next
                                    </button>

                                    <button type="submit" name="recall_token" class="control-btn recall-btn" <?= !$servingToken ? 'disabled' : '' ?>>
                                        <i class="fas fa-redo"></i> Recall
                                    </button>

                                    <button type="submit" name="start_queue" class="control-btn start-btn" <?= $queueDetails['start_time'] ? 'disabled' : '' ?>>
                                        <i class="fas fa-play"></i> Start
                                    </button>

                                    <button type="submit" name="close_queue" class="control-btn close-btn" <?= $queueDetails['end_time'] ? 'disabled' : '' ?>>
                                        <i class="fas fa-stop"></i> Close
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="queue-status-panel">
                            <div class="queue-status-header">
                                <h3>Queue</h3>
                                <span><?= $queueStatus['queue_length'] ?> people waiting</span>
                            </div>

                            <div class="queue-list">
                                <?php if (empty($waitingTokens)): ?>
                                    <div class="empty-queue">
                                        <i class="fas fa-check-circle"></i>
                                        <p>Queue is empty</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($waitingTokens as $token): ?>
                                        <div class="queue-item">
                                            <div class="token-number"><?= sprintf('%03d', $token['token_number']) ?></div>
                                            <div class="token-name"><?= htmlspecialchars($token['full_name']) ?></div>
                                            <div class="token-time"><?= date('h:i A', strtotime($token['join_time'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-visitor-btn" onclick="toggleAddVisitorForm()">
                                <i class="fas fa-plus"></i> Add Visitor
                            </button>

                            <div id="addVisitorForm" class="add-visitor-form hidden">
                                <h3>Add New Visitor</h3>
                                <form method="POST">
                                    <input type="hidden" name="queue_id" value="<?= $selectedQueue ?>">

                                    <div class="form-group">
                                        <label for="visitor_name">Name</label>
                                        <input type="text" id="visitor_name" name="visitor_name" placeholder="Visitor's name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="visitor_phone">Phone Number</label>
                                        <input type="text" id="visitor_phone" name="visitor_phone" placeholder="Visitor's phone number" required>
                                    </div>

                                    <div class="form-buttons">
                                        <button type="submit" name="add_visitor" class="add-btn">Add to Queue</button>
                                        <button type="button" class="cancel-btn" onclick="toggleAddVisitorForm()">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    // Update serving timer dynamically
function updateServingTimer() {
    const servingTimer = document.getElementById('servingTimer');
    if (servingTimer && servingTimer.dataset.startTime) {
        const startTime = parseInt(servingTimer.dataset.startTime);
        const elapsed = Math.floor(Date.now() / 1000) - startTime;

        const hours = Math.floor(elapsed / 3600);
        const minutes = Math.floor((elapsed % 3600) / 60);
        const seconds = elapsed % 60;

        servingTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}

// Set interval to update serving timer every second
setInterval(updateServingTimer, 1000);

    // Update current time
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }

    // Set interval to update time
    //setInterval(updateTime, 1000);

    // Organization changed
    function organizationChanged(orgName) {
        if (orgName) {
            window.location.href = 'manage_queue.php?org=' + encodeURIComponent(orgName);
        } else {
            window.location.href = 'manage_queue.php';
        }
    }

    // Queue changed
    function queueChanged(queueName) {
        if (queueName) {
            const orgSelect = document.getElementById('organization');
            const orgName = orgSelect.value;
            window.location.href = 'manage_queue.php?org=' + encodeURIComponent(orgName) + '&queue=' + encodeURIComponent(queueName);
        } else {
            const orgSelect = document.getElementById('organization');
            const orgName = orgSelect.value;
            window.location.href = 'manage_queue.php?org=' + encodeURIComponent(orgName);
        }
    }

    // Toggle add visitor form
    function toggleAddVisitorForm() {
        const form = document.getElementById('addVisitorForm');
        form.classList.toggle('hidden');
    }

    // Toggle add visitor form
    function toggleAddVisitorForm() {
        const form = document.getElementById('addVisitorForm');
        form.classList.toggle('hidden');
    }

    // Update serving timer dynamically
    function updateServingTimer() {
        const servingTimer = document.getElementById('servingTimer');
        if (servingTimer && servingTimer.dataset.startTime) {
            const startTime = parseInt(servingTimer.dataset.startTime);
            const elapsed = Math.floor(Date.now() / 1000) - startTime;

            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;

            servingTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    }

    // Set interval to update serving timer every second
    setInterval(updateServingTimer, 1000);

    // Initialize serving timer on page load
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($servingToken) && $servingToken): ?>
        const servingTimer = document.getElementById('servingTimer');
        if (servingTimer) {
            servingTimer.dataset.startTime = '<?= strtotime($servingToken['serving_time']) ?>';
        }
        <?php endif; ?>
    });

    // Fetch updated queue data dynamically
    function fetchQueueData() {
        const queueId = <?= json_encode($selectedQueue ?? null) ?>;
        if (!queueId) return;

        fetch(`fetch_queue_data.php?queue_id=${queueId}`)
            .then(response => response.json())
            .then(data => {
                // Update the queue list
                const queueList = document.querySelector('.queue-list');
                if (queueList) {
                    queueList.innerHTML = '';
                    if (data.waitingTokens.length === 0) {
                        queueList.innerHTML = `
                            <div class="empty-queue">
                                <i class="fas fa-check-circle"></i>
                                <p>Queue is empty</p>
                            </div>
                        `;
                    } else {
                        data.waitingTokens.forEach(token => {
                            queueList.innerHTML += `
                                <div class="queue-item">
                                    <div class="token-number">${token.token_number.toString().padStart(3, '0')}</div>
                                    <div class="token-name">${token.full_name}</div>
                                    <div class="token-time">${new Date(token.join_time).toLocaleTimeString()}</div>
                                </div>
                            `;
                        });
                    }
                }

                // Update queue status
                const queueStatusHeader = document.querySelector('.queue-status-header span');
                if (queueStatusHeader) {
                    queueStatusHeader.textContent = `${data.queueLength} people waiting`;
                }

                // Update now serving token
                const nowServing = document.querySelector('.token-display');
                if (nowServing) {
                    nowServing.textContent = data.servingToken
                        ? data.servingToken.token_number.toString().padStart(3, '0')
                        : '000';
                }
            })
            .catch(error => console.error('Error fetching queue data:', error));
    }
// Update serving timer dynamically
function updateServingTimer() {
    const servingTimer = document.getElementById('servingTimer');
    if (servingTimer && servingTimer.dataset.startTime) {
        const startTime = parseInt(servingTimer.dataset.startTime);
        const elapsed = Math.floor(Date.now() / 1000) - startTime;

        const hours = Math.floor(elapsed / 3600);
        const minutes = Math.floor((elapsed % 3600) / 60);
        const seconds = elapsed % 60;

        servingTimer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}
    // Set interval to fetch updated queue data every 5 seconds
    setInterval(fetchQueueData, 5000);

</script>

<?php include '../includes/footer.php'; ?>