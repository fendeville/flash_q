<?php
session_start();

// Check if the user has admin access
if (!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== true) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Include the database connection and functions
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Set header for JSON response
header('Content-Type: application/json');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get POST data
        $siteName = $_POST['site_name'] ?? '';
        $adminEmail = $_POST['admin_email'] ?? '';
        
        // Validate inputs
        if (empty($siteName)) {
            echo json_encode([
                'success' => false,
                'message' => 'Site name is required'
            ]);
            exit;
        }
        
        if (empty($adminEmail) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Valid admin email is required'
            ]);
            exit;
        }
        
        // Save settings
        $success = true;
        
        // Update site name
        if (!updateSetting('site_name', $siteName)) {
            $success = false;
        }
        
        // Update admin email
        if (!updateSetting('admin_email', $adminEmail)) {
            $success = false;
        }
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Settings saved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save some settings'
            ]);
        }
    } catch (Exception $e) {
        // Log the detailed error for debugging (in production, use proper logging)
        error_log("Settings save error: " . $e->getMessage());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while saving settings. Please try again later.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
?>
