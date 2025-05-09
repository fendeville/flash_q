<?php
$pageTitle = 'Register';
$showHeader = true;
$extraCss = ['assets/css/auth.css'];
$extraJs = ['assets/js/auth.js'];

include 'includes/header.php';

// Handle registration form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine if it's quick or detailed registration
    $isQuickRegistration = isset($_POST['registration_type']) && $_POST['registration_type'] === 'quick';
    
    // Validate required fields
    if ($isQuickRegistration) {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone_number'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($fullName) || empty($phone) || empty($password)) {
            $error = 'Please fill in all required fields';
        }
    } else {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone_number'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
            $error = 'Please fill in all required fields';
        } else {
            $fullName = $firstName . ' ' . $lastName;
        }
    }
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    }
    
    // If no errors, proceed with registration
    if (empty($error)) {
        $result = registerUser(
            $fullName,
            $isQuickRegistration ? null : $email,
            $phone,
            $password,
            $isQuickRegistration ? null : $location
        );
        
        if ($result['success']) {
            $success = $result['message'];
            // Redirect to login after successful registration
            header('Refresh: 2; URL=login.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <img src="assets/images/logo.svg" alt="Flash.Q Logo" class="auth-logo">
            <h1 class="auth-title">Create an Account</h1>
            <p class="auth-subtitle">Join Flash.Q for a seamless queuing experience</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="registration-type-toggle">
            <button class="toggle-btn active" data-type="quick">Quick Registration</button>
            <button class="toggle-btn" data-type="detailed">Detailed Registration</button>
        </div>
        
        <!-- Quick Registration Form -->
        <form id="quickRegistrationForm" method="POST" class="auth-form">
            <input type="hidden" name="registration_type" value="quick">
            
            <div class="form-group">
                <label for="quick_full_name">Full Name</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" id="quick_full_name" name="full_name" placeholder="Enter your full name" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quick_phone_number">Phone Number</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-phone"></i></span>
                    <input type="tel" id="quick_phone_number" name="phone_number" placeholder="+237 651990298" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quick_password">Password</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="quick_password" name="password" placeholder="Create a password" class="form-input" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-meter-fill" data-strength="0"></div>
                    </div>
                    <span class="strength-text">Password Strength</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quick_confirm_password">Confirm Password</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="quick_confirm_password" name="confirm_password" placeholder="Confirm your password" class="form-input" required>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="submit-btn">
                    Create Account
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button type="button" class="cancel-btn">Cancel</button>
            </div>
        </form>
        
        <!-- Detailed Registration Form -->
        <form id="detailedRegistrationForm" method="POST" class="auth-form hidden">
            <input type="hidden" name="registration_type" value="detailed">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <div class="input-container">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="first_name" name="first_name" placeholder="First name" class="form-input" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <div class="input-container">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="last_name" name="last_name" placeholder="Last name" class="form-input" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" id="email" name="email" placeholder="Your email address" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-phone"></i></span>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="+237 651990298" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="location">Location/Region</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" id="location" name="location" placeholder="Your location" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" name="password" placeholder="Create a password" class="form-input" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-meter-fill" data-strength="0"></div>
                    </div>
                    <span class="strength-text">Password Strength</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-container">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" class="form-input" required>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="submit-btn">
                    Create Account
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button type="button" class="cancel-btn">Cancel</button>
            </div>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>