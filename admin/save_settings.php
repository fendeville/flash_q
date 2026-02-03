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
        // Define settings with their validation rules
        $settingsConfig = [
            'site_name' => [
                'value' => $_POST['site_name'] ?? '',
                'required' => true,
                'validator' => function($value) {
                    return !empty($value);
                },
                'error_message' => 'Site name is required'
            ],
            'admin_email' => [
                'value' => $_POST['admin_email'] ?? '',
                'required' => true,
                'validator' => function($value) {
                    return !empty($value) && filter_var($value, FILTER_VALIDATE_EMAIL);
                },
                'error_message' => 'Valid admin email is required'
            ]
        ];
        
        // Validate all settings
        foreach ($settingsConfig as $key => $config) {
            if ($config['required'] && !$config['validator']($config['value'])) {
                echo json_encode([
                    'success' => false,
                    'message' => $config['error_message']
                ]);
                exit;
            }
        }
        
        // Save all settings
        $failedSettings = [];
        foreach ($settingsConfig as $key => $config) {
            if (!updateSetting($key, $config['value'])) {
                $failedSettings[] = $key;
            }
        }
        
        if (empty($failedSettings)) {
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
