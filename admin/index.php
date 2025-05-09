<?php
session_start(); // Start the session//

$pageTitle = 'Admin Login';
$showHeader = true;
$extraCss = ['../assets/css/auth.css', '../assets/css/admin.css'];
$extraJs = ['../assets/js/auth.js'];


// Handle admin login
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    // Simple admin password check (in a real app, this would be more secure)
    if ($password === '741074') {
        $_SESSION['admin_access'] = true;
        //error_log("Redirecting to dashboard..."); // Debugging statement
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid admin password';
    }
}

include '../includes/header.php'
?>

<div class="admin-auth-container">
    <div class="admin-auth-card">
        <div class="admin-header">
            <img src="../assets/images/logo.svg" alt="Flash.Q Logo" class="admin-logo">
            <h1>Admin Access</h1>
            <p>Enter the admin password to continue</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="password">Admin Password</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" name="password" placeholder="Enter admin password" class="form-input" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="submit-btn">
                    Access Admin Panel
                    <i class="fas fa-arrow-right"></i>
                </button>
                <a href="../index.php" class="cancel-btn">Back to Home</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>