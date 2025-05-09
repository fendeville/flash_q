<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$queueId = $_GET['queue_id'] ?? null;

if (!$queueId) {
    echo json_encode(['error' => 'Invalid queue ID']);
    exit;
}

// Get waiting tokens
$waitingTokens = $db->select(
    "SELECT qt.token_number, u.full_name, qt.join_time 
     FROM queue_tokens qt 
     JOIN users u ON qt.user_id = u.id 
     WHERE qt.queue_id = :queue_id AND qt.status = 'waiting' 
     ORDER BY qt.token_number ASC",
    ['queue_id' => $queueId]
);

// Get currently serving token
$servingToken = $db->selectOne(
    "SELECT qt.token_number, u.full_name, qt.serving_time 
     FROM queue_tokens qt 
     JOIN users u ON qt.user_id = u.id 
     WHERE qt.queue_id = :queue_id AND qt.status = 'serving'",
    ['queue_id' => $queueId]
);

// Get queue length
$queueLength = count($waitingTokens);

echo json_encode([
    'waitingTokens' => $waitingTokens,
    'queueLength' => $queueLength,
    'servingToken' => $servingToken
]);