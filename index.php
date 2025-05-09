<?php
$pageTitle = 'Home';
$showHeader = true;
$extraCss = [];
$extraJs = ['assets/js/main.js'];

include 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get organizations for the services section
$organizations = getAllOrganizations();
?>

<!-- Hero Section -->
<section class="relative bg-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="slider-container">
            <div class="slider-track">
                <div class="slide bg-cover bg-conter" style="background-image: url('https://www.telecomreviewafrica.com/images/stories/2024/12/MTN_Group_to_Invest_300_Million_in_Cameroons_Digital_Future.jpg')"></div>

                

                <div class="slide bg-cover bg-conter" style="background-image:url('https://i0.wp.com/media.premiumtimesng.com/wp-content/files/2022/09/1662485435003blob-1.png?resize=650,399&ssl=1')"></div>
            </div>
        </div>
        <div class="absolute inset-0 bg-black opacity-60"></div>
    </div>
    
    <div class="container mx-auto px-4 py-24 relative z-10">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                NO TIME WASTAGE FOR TIME IS MONEY
            </h1>
            <h2 class="text-2xl md:text-3xl font-bold mb-6 text-primary-400">
                FLASH.Q, GATEWAY TO QLESS SERVICES
            </h2>
            <p class="text-lg mb-8 text-gray-300">
                Say goodbye to physical queues and waiting for hours. Join our digital queue management system for a seamless experience.
            </p>
            <a href="#services" class="px-8 py-3 bg-primary-600 hover:bg-primary-700 rounded-full text-white font-medium transition-all transform hover:-translate-y-1 hover:shadow-lg inline-block">
                Learn More
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">OUR SERVICES</h2>
            <p class="text-xl text-primary-600 dark:text-primary-400">What We Offer</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Service 1 -->
            <div class="service-card bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center transition-all hover:transform hover:-translate-y-2">
                <div class="service-icon bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Join Queue</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Join any service queue remotely from the comfort of your home or office.
                </p>
                <a href="join_queue.php" class="inline-block px-6 py-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-600 transition-colors">
                    Join Now
                </a>
            </div>
            
            <!-- Service 2 -->
            <div class="service-card bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center transition-all hover:transform hover:-translate-y-2">
                <div class="service-icon bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">View Queue</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Monitor your position in the queue and get estimated waiting times.
                </p>
                <a href="view_queue.php" class="inline-block px-6 py-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-600 transition-colors">
                    View Queue
                </a>
            </div>
            
            <!-- Service 3 -->
            <div class="service-card bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center transition-all hover:transform hover:-translate-y-2">
                <div class="service-icon bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Notifications</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Get real-time notifications as you move closer to the front of the queue.
                </p>
                <a href="#" class="inline-block px-6 py-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-600 transition-colors">
                    Learn More
                </a>
            </div>
            
            <!-- Service 4 -->
            <div class="service-card bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center transition-all hover:transform hover:-translate-y-2">
                <div class="service-icon bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">Customer Support</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Get assistance and support for any issues you might encounter.
                </p>
                <a href="#contact" class="inline-block px-6 py-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-600 transition-colors">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="py-16 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">OUR PARTNERS</h2>
            <p class="text-xl text-primary-600 dark:text-primary-400">Organizations You Can Queue For</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($organizations as $org): ?>
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 transition-all hover:shadow-lg">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 mr-4">
                        <?php if ($org['category'] === 'Healthcare'): ?>
                            <i class="fas fa-hospital"></i>
                        <?php elseif ($org['category'] === 'Banking'): ?>
                            <i class="fas fa-university"></i>
                        <?php elseif ($org['category'] === 'Utility'): ?>
                            <i class="fas fa-bolt"></i>
                        <?php else: ?>
                            <i class="fas fa-building"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white"><?= htmlspecialchars($org['name']) ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($org['category']) ?></p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-4"><?= htmlspecialchars($org['description']) ?></p>
                <a href="join_queue.php?org=<?= $org['id'] ?>" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">
                    Join Queue <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">HOW IT WORKS</h2>
            <p class="text-xl text-primary-600 dark:text-primary-400">Simple Steps to Save Your Time</p>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="w-full md:w-1/2 mb-8 md:mb-0 md:pr-8">
                <div class="relative">
                    <div class="absolute inset-0 bg-primary-600 rounded-lg transform rotate-3"></div>
                    <img src="../flash_q/assets/images/2708-7347-mtn-cameroon-lines-up-poor-performances-in-first-6-months-2017_L.jpg" alt="How it works" class="relative rounded-lg shadow-lg w-full">
                </div>
            </div>
            
            <div class="w-full md:w-1/2 md:pl-8">
                <div class="space-y-6">
                    <div class="flex">
                        <div class="flex-shrink-0 h-12 w-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 mr-4">
                            <span class="text-xl font-bold">1</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Register & Login</h3>
                            <p class="text-gray-600 dark:text-gray-400">Create an account and log in to access our queue management system.</p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="flex-shrink-0 h-12 w-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 mr-4">
                            <span class="text-xl font-bold">2</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Select Organization</h3>
                            <p class="text-gray-600 dark:text-gray-400">Choose from our list of partner organizations that you want to queue for.</p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="flex-shrink-0 h-12 w-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 mr-4">
                            <span class="text-xl font-bold">3</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Join the Queue</h3>
                            <p class="text-gray-600 dark:text-gray-400">Add yourself to the queue and get your virtual token number.</p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="flex-shrink-0 h-12 w-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 mr-4">
                            <span class="text-xl font-bold">4</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">Receive Notifications</h3>
                            <p class="text-gray-600 dark:text-gray-400">Get updates as you move closer to the front of the queue, so you know when to arrive.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">TESTIMONIALS</h2>
            <p class="text-xl text-primary-600 dark:text-primary-400">What Our Users Say</p>
        </div>
        
        <div class="testimonial-slider">
            <div class="testimonial-track">
                <!-- Testimonial 1 -->
                <div class="testimonial-slide px-4">
                    <div class="bg-white dark:bg-gray-700 p-8 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Sarah Johnson</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bank Customer</p>
                            </div>
                            <div class="ml-auto text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">
                            "Flash.Q has transformed my banking experience. I no longer need to wait in long lines - I can join the queue remotely and arrive just when it's my turn!"
                        </p>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-slide px-4">
                    <div class="bg-white dark:bg-gray-700 p-8 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Michael Lee</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Hospital Patient</p>
                            </div>
                            <div class="ml-auto text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">
                            "As someone with a busy schedule, Flash.Q helps me plan my hospital visits efficiently. The notifications are very helpful and let me know exactly when I should arrive."
                        </p>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="testimonial-slide px-4">
                    <div class="bg-white dark:bg-gray-700 p-8 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Emily Watson</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Utility Customer</p>
                            </div>
                            <div class="ml-auto text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">
                            "Paying bills at the utility office used to take hours of my day. With Flash.Q, I can join the queue from home and arrive just when it's my turn. This is revolutionary!"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-primary-600 relative overflow-hidden">
    <div class="absolute right-0 top-0 h-full w-1/3 opacity-10">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <path fill="#FFFFFF" d="M39.9,-65.7C52.8,-59.6,65.2,-51.2,71.8,-39.1C78.3,-27,79,-11.1,76.2,3.3C73.5,17.6,67.3,30.4,58.5,40.9C49.6,51.4,38.2,59.6,25.7,64.5C13.3,69.4,-0.2,71.1,-14.3,69.7C-28.5,68.4,-43.2,64.1,-54.5,54.9C-65.9,45.8,-73.8,31.7,-78.3,15.9C-82.7,0.1,-83.6,-17.4,-76.8,-30.9C-70,-44.3,-55.5,-53.7,-41.4,-59.4C-27.3,-65.1,-13.6,-67,-0.5,-66.1C12.7,-65.2,27,-71.8,39.9,-65.7Z" transform="translate(100 100)" />
        </svg>
    </div>
    
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Ready to Skip the Line?</h2>
        <p class="text-xl text-white opacity-90 mb-8 max-w-2xl mx-auto">
            Join Flash.Q today and never waste time in physical queues again. Our digital queue management system is designed to make your life easier.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="join_queue.php" class="px-8 py-3 bg-white text-primary-600 font-semibold rounded-full hover:bg-gray-100 transition-colors">
                Join a Queue
            </a>
            <a href="#contact" class="px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-primary-600 transition-colors">
                Contact Us
            </a>
        </div>
    </div>
</section>

<script>
    // Hero slider animation
    document.addEventListener('DOMContentLoaded', function() {
        const sliderTrack = document.querySelector('.slider-track');
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlider();
        }
        
        function updateSlider() {
            const offset = currentSlide * -100;
            sliderTrack.style.transform = `translateX(${offset}%)`;
        }
        
        // Initialize slider
        sliderTrack.style.width = `${slides.length * 100}%`;
        slides.forEach(slide => {
            slide.style.width = `${100 / slides.length}%`;
        });
        
        // Start auto-sliding
        setInterval(nextSlide, 5000);
        
        // Testimonial slider
        const testimonialTrack = document.querySelector('.testimonial-track');
        const testimonialSlides = document.querySelectorAll('.testimonial-slide');
        let currentTestimonial = 0;
        
        function nextTestimonial() {
            currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length;
            updateTestimonialSlider();
        }
        
        function updateTestimonialSlider() {
            const offset = currentTestimonial * -100;
            testimonialTrack.style.transform = `translateX(${offset}%)`;
        }
        
        // Initialize testimonial slider
        testimonialTrack.style.width = `${testimonialSlides.length * 100}%`;
        testimonialSlides.forEach(slide => {
            slide.style.width = `${100 / testimonialSlides.length}%`;
        });
        
        // Start auto-sliding for testimonials
        setInterval(nextTestimonial, 6000);
    });
</script>

<style>
    /* Hero Slider Styles */
    .slider-container {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    
    .slider-track {
        height: 100%;
        display: flex;
        transition: transform 0.5s ease;
    }
    
    .slide {
        flex-shrink: 0;
        height: 100%;
        width: 100%;
    }
    
    /* Testimonial Slider */
    .testimonial-slider {
        overflow: hidden;
        width: 100%;
    }
    
    .testimonial-track {
        display: flex;
        transition: transform 0.5s ease;
    }
    
    .testimonial-slide {
        flex-shrink: 0;
        width: 100%;
    }
    
    /* Service Cards Animation */
    .service-card {
        transition: all 0.3s ease;
    }
    
    .service-card:hover {
        transform: translateY(-10px);
    }
    
    .service-icon {
        transition: all 0.3s ease;
    }
    
    .service-card:hover .service-icon {
        transform: scale(1.1);
    }
</style>

<?php include 'includes/footer.php'; ?>