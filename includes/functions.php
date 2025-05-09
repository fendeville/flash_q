<?php
require_once 'db.php';

// User authentication functions
function registerUser($fullName, $email, $phone, $password, $location = null) {
    global $db;
    
    // Check if email or phone already exists
    $existingUser = $db->selectOne(
        "SELECT * FROM users WHERE email = :email OR phone_number = :phone",
        ['email' => $email, 'phone' => $phone]
    );
    
    if ($existingUser) {
        return [
            'success' => false,
            'message' => 'Email or phone number already registered'
        ];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $userId = $db->insert('users', [
        'full_name' => $fullName,
        'email' => $email,
        'phone_number' => $phone,
        'password' => $hashedPassword,
        'location' => $location
    ]);
    
    if ($userId) {
        return [
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $userId
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Registration failed'
        ];
    }
}

function loginUser($identifier, $password) {
    global $db;
    
    // Check if identifier is email or phone
    $user = $db->selectOne(
        "SELECT * FROM users WHERE email = :identifier OR phone_number = :identifier",
        ['identifier' => $identifier]
    );
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === true;
}

function logout() {
    // Start output buffering to prevent premature output
    ob_start();

    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: login.php');
    exit;

    // Flush the output buffer
    ob_end_flush();
}

// Queue management functions
function getAllOrganizations() {
    global $db;
    return $db->select("SELECT * FROM organizations ORDER BY name ASC");
}

function getQueuesForOrganization($orgId) {
    global $db;
    return $db->select(
        "SELECT * FROM queues WHERE organization_id = :org_id AND is_active = 1",
        ['org_id' => $orgId]
    );
}

function getQueueDetails($queueId) {
    global $db;
    return $db->selectOne(
        "SELECT q.*, o.name as organization_name 
         FROM queues q 
         JOIN organizations o ON q.organization_id = o.id 
         WHERE q.id = :queue_id",
        ['queue_id' => $queueId]
    );
}

function addToQueue($queueId, $userId, $name, $phoneNumber) {
    global $db;
    
    // Get the last token number for the queue
    $lastToken = $db->selectOne(
        "SELECT MAX(token_number) as last_token FROM queue_tokens WHERE queue_id = :queue_id",
        ['queue_id' => $queueId]
    );
    
    $tokenNumber = 1;
    if ($lastToken && $lastToken['last_token']) {
        $tokenNumber = $lastToken['last_token'] + 1;
    }
    
    // Add to queue
    $tokenId = $db->insert('queue_tokens', [
        'queue_id' => $queueId,
        'user_id' => $userId,
        'token_number' => $tokenNumber
    ]);
    
    if ($tokenId) {
        // Create notification
        $queueDetails = getQueueDetails($queueId);
        $message = "You have been added to the queue for " . $queueDetails['organization_name'] . " - " . $queueDetails['name'] . ". Your token number is " . $tokenNumber;
        
        $db->insert('notifications', [
            'user_id' => $userId,
            'token_id' => $tokenId,
            'message' => $message
        ]);
        
        return [
            'success' => true,
            'message' => 'Added to queue successfully',
            'token_number' => $tokenNumber,
            'token_id' => $tokenId
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to add to queue'
        ];
    }
}

function getCurrentQueueStatus($queueId) {
    global $db;
    
    // Get current serving token
    $currentServing = $db->selectOne(
        "SELECT * FROM queue_tokens 
         WHERE queue_id = :queue_id AND status = 'serving' 
         ORDER BY token_number ASC LIMIT 1",
        ['queue_id' => $queueId]
    );
    
    // Get waiting tokens
    $waitingTokens = $db->select(
        "SELECT qt.*, u.full_name 
         FROM queue_tokens qt 
         JOIN users u ON qt.user_id = u.id 
         WHERE qt.queue_id = :queue_id AND qt.status = 'waiting' 
         ORDER BY qt.token_number ASC",
        ['queue_id' => $queueId]
    );
    
    // Get estimated wait time
    $avgWaitTime = $db->selectOne(
        "SELECT average_wait_time FROM statistics 
         WHERE queue_id = :queue_id AND date = CURDATE()",
        ['queue_id' => $queueId]
    );
    
    $estimatedWaitTime = 0;
    if ($avgWaitTime && $avgWaitTime['average_wait_time']) {
        $estimatedWaitTime = $avgWaitTime['average_wait_time'] * count($waitingTokens);
    }
    
    return [
        'current_serving' => $currentServing,
        'waiting_tokens' => $waitingTokens,
        'queue_length' => count($waitingTokens),
        'estimated_wait_time' => $estimatedWaitTime
    ];
}

function notifyUsers($queueId, $currentToken) {
    global $db;
    
    // Find users that need to be notified (when <= 10 people ahead)
    $usersToNotify = $db->select(
        "SELECT qt.*, u.id as user_id 
         FROM queue_tokens qt 
         JOIN users u ON qt.user_id = u.id 
         WHERE qt.queue_id = :queue_id 
           AND qt.status = 'waiting' 
           AND qt.token_number > :current_token 
           AND qt.token_number <= :notify_threshold
         ORDER BY qt.token_number ASC",
        [
            'queue_id' => $queueId,
            'current_token' => $currentToken,
            'notify_threshold' => $currentToken + 10
        ]
    );
    
    $queueDetails = getQueueDetails($queueId);
    
    foreach ($usersToNotify as $user) {
        $peopleAhead = $user['token_number'] - $currentToken;
        $message = "You are " . $peopleAhead . " people away from being served at " . 
                   $queueDetails['organization_name'] . " - " . $queueDetails['name'] . ". " .
                   "Your token number is " . $user['token_number'];
        
        $db->insert('notifications', [
            'user_id' => $user['user_id'],
            'token_id' => $user['id'],
            'message' => $message
        ]);
    }
}

function getNotificationsForUser($userId) {
    global $db;
    return $db->select(
        "SELECT * FROM notifications 
         WHERE user_id = :user_id 
         ORDER BY created_at DESC",
        ['user_id' => $userId]
    );
}

// Admin functions
function serveNextToken($queueId) {
    global $db;
    
    // Complete currently serving token if any
    $currentServing = $db->selectOne(
        "SELECT * FROM queue_tokens 
         WHERE queue_id = :queue_id AND status = 'serving'",
        ['queue_id' => $queueId]
    );
    
    if ($currentServing) {
        $db->update('queue_tokens', 
            [
                'status' => 'completed',
                'completion_time' => date('Y-m-d H:i:s')
            ],
            "id = " . $currentServing['id']
        );
        
        // Update statistics
        updateStatistics($queueId, $currentServing);
    }
    
    // Get next waiting token
    $nextToken = $db->selectOne(
        "SELECT * FROM queue_tokens 
         WHERE queue_id = :queue_id AND status = 'waiting' 
         ORDER BY token_number ASC LIMIT 1",
        ['queue_id' => $queueId]
    );
    
    if ($nextToken) {
        // Update to serving
        $db->update('queue_tokens', 
            [
                'status' => 'serving',
                'serving_time' => date('Y-m-d H:i:s')
            ],
            "id = " . $nextToken['id']
        );
        
        // Notify user
        $queueDetails = getQueueDetails($queueId);
        $message = "It's your turn! You are now being served at " . 
                   $queueDetails['organization_name'] . " - " . $queueDetails['name'];
        
        $db->insert('notifications', [
            'user_id' => $nextToken['user_id'],
            'token_id' => $nextToken['id'],
            'message' => $message
        ]);
        
        // Notify other users in queue
        notifyUsers($queueId, $nextToken['token_number']);
        
        return [
            'success' => true,
            'message' => 'Next token is now being served',
            'token' => $nextToken
        ];
    } else {
        return [
            'success' => false,
            'message' => 'No more tokens in queue'
        ];
    }
}

function updateStatistics($queueId, $token) {
    global $db;
    
    // Calculate wait time
    $waitTime = strtotime($token['serving_time']) - strtotime($token['join_time']);
    
    // Get current stats for today
    $stats = $db->selectOne(
        "SELECT * FROM statistics 
         WHERE queue_id = :queue_id AND date = CURDATE()",
        ['queue_id' => $queueId]
    );
    
    if ($stats) {
        // Update existing stats
        $newTotalServed = $stats['total_served'] + 1;
        $newAvgWaitTime = (($stats['average_wait_time'] * $stats['total_served']) + $waitTime) / $newTotalServed;
        
        $db->update('statistics', 
            [
                'total_served' => $newTotalServed,
                'average_wait_time' => $newAvgWaitTime
            ],
            "id = " . $stats['id']
        );
    } else {
        // Create new stats record
        $db->insert('statistics', [
            'queue_id' => $queueId,
            'date' => date('Y-m-d'),
            'total_served' => 1,
            'average_wait_time' => $waitTime
        ]);
    }
}

// Helper functions
function formatTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    if ($hours > 0) {
        return $hours . " hr " . $minutes . " min";
    } else {
        return $minutes . " min";
    }
}
?>