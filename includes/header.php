<?php
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/config.php';//require_once 'ademin/index.php';//
require_once __DIR__ . '/functions.php';

// Check if user is logged in
$isLoggedIn = isLoggedIn();
$isAdmin = isAdmin();

error_log("isAdmin: " . ($isAdmin ? 'true' : 'false')); // Debugging statement
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/logo.svg" type="image/svg+xml">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans transition-colors duration-200">
    <?php if (isset($showHeader) && $showHeader): ?>
    <header class="bg-white dark:bg-gray-800 shadow-md">
        <nav class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-2">
                <img src="assets/images/logo.svg" alt="Flash.Q Logo" class="h-10">
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">Flash.Q</span>
            </a>
            
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
                <a href="join_queue.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Join Queue</a>
                <a href="view_queue.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">View Queue</a>
                <?php if ($isAdmin): ?>
                    <a href="admin/dashboard.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Admin</a>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center space-x-4">
                <?php if ($isLoggedIn): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-1">
                            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="absolute right-0 w-48 mt-2 py-2 bg-white dark:bg-gray-800 rounded-md shadow-xl z-20 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                            <a href="?logout=1" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition-colors">Login</a>
                    <a href="register.php" class="hidden md:inline-block hover:text-primary-600 dark:hover:text-primary-400">Register</a>
                <?php endif; ?>
                
                <button id="darkModeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-moon hidden dark:inline-block"></i>
                    <i class="fas fa-sun inline-block dark:hidden"></i>
                </button>
            </div>
            
            <button id="mobileMenuButton" class="md:hidden p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
        
        <!-- Mobile menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-white dark:bg-gray-800 shadow-md">
            <div class="container mx-auto px-4 py-3 flex flex-col space-y-4">
                <a href="index.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
                <a href="join_queue.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Join Queue</a>
                <a href="view_queue.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">View Queue</a>
                <?php if ($isAdmin): ?>
                    <a href="../admin/index.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Admin</a>
                <?php endif; ?>
                <?php if (!$isLoggedIn): ?>
                    <a href="register.php" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php endif; ?>
    
    <!-- Check for logout request -->
    <?php
    if (isset($_GET['logout']) && $_GET['logout'] == 1) {
        logout();
    }
    ?>
    
    <main class="container mx-auto px-4 py-6">