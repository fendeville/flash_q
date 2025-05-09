<?php
// Disable error display for production
ini_set('display_errors', 0);
error_reporting(0);

ob_start(); // Start output buffering

session_start();

$pageTitle = 'Login';
$showHeader = true;
$extraCss = ['assets/css/auth.css'];
$extraJs = ['assets/js/auth.js'];

require_once 'includes/config.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Initialize variables
$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Admin login logic
        if ($identifier === 'admin' && $password === '741074') {
            $_SESSION['admin_access'] = true;
            $_SESSION['user_name'] = 'Admin';
            error_log("User Name in Session: " . ($_SESSION['user_name'] ?? 'Not Set'));
            header('Location: admin/dashboard.php'); // Redirect to admin dashboard
            exit;
        }

        // User login logic (if implemented)
        $result = loginUser($identifier, $password);

        if ($result['success']) {
            $success = $result['message'];
            // Redirect to home page after successful login
            header('Refresh: 1; URL=index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

ob_end_flush(); // Flush the output buffer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="assets/images/logo.svg" alt="Flash.Q Logo" class="auth-logo">
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Login to access your Flash.Q account</p>
            </div>

            <!-- Display error or success messages -->
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- Login type toggle -->
            <div class="login-type-toggle">
                <button class="toggle-btn active" data-type="email">Email/Phone</button>
                <button class="toggle-btn" data-type="phone">Phone Number</button>
            </div>

            <!-- Login form -->
            <form method="POST" class="auth-form">
                <div class="form-group" id="emailPhoneGroup">
                    <label for="identifier">Email or Phone Number</label>
                    <div class="input-container">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="identifier" name="identifier" placeholder="Your email or phone number" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-container">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" placeholder="Your password" class="form-input" required>
                        <button type="button" class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">
                        Login
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </div>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>