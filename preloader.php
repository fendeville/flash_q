<?php
$showHeader = false;
$pageTitle = 'Welcome';
$extraCss = ['assets/css/preloader.css'];
$extraJs = ['assets/js/preloader.js'];

include 'includes/header.php';
?>

<div class="preloader-container">
    <div class="preloader-content">
        <div class="logo-container">
            <img src="assets/images/logo.svg" alt="Flash.Q Logo" class="logo">
            <h1 class="logo-text">Flash.Q</h1>
        </div>
        <div class="animation-container">
            <div class="queue-animation">
                <div class="person person1"></div>
                <div class="person person2"></div>
                <div class="person person3"></div>
                <div class="counter"></div>
            </div>
        </div>
        <p class="tagline">No time wastage for time is money!</p>
        <button id="startButton" class="start-button">
            Get Started
            <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>