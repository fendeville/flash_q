<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Set headers for file download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="daily_queue_report.pdf"');

SELECT 
    o.name AS organization_name,
    q.name AS queue_name,
    COUNT(qt.id) AS total_tokens,
    SUM(CASE WHEN qt.status = 'completed' THEN 1 ELSE 0 END) AS completed_tokens,
    SUM(CASE WHEN qt.status = 'waiting' THEN 1 ELSE 0 END) AS waiting_tokens,
    SUM(CASE WHEN qt.status = 'serving' THEN 1 ELSE 0 END) AS serving_tokens,
    SUM(CASE WHEN qt.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_tokens
FROM organizations o
JOIN queues q ON q.organization_id = o.id
LEFT JOIN queue_tokens qt ON qt.queue_id = q.id
WHERE o.name = 'MTN Office'
GROUP BY o.id, q.id
ORDER BY o.name, q.name;

// Generate the report (placeholder logic)
echo "This is a placeholder for the daily queue report.";
?>