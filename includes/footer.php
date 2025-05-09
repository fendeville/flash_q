</main>
    
    <footer class="bg-white dark:bg-gray-800 shadow-inner pt-12 pb-6 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Company Info -->
                <div class="flex flex-col items-start">
                    <div class="flex items-center space-x-2 mb-3">
                        <img src="assets/images/logo.svg" alt="Flash.Q Logo" class="h-10">
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">Flash.Q</span>
                    </div>
                    <p class="mb-6 text-gray-600 dark:text-gray-400">The best queue management system</p>
                    <div class="flex space-x-4 text-gray-500 dark:text-gray-400">
                        <a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <i class="fab fa-twitter text-lg"></i>
                        </a>
                        <a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <i class="fab fa-linkedin-in text-lg"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Get In Touch</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">For more info get in touch</p>
                    <div class="flex flex-col space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="text-primary-600 dark:text-primary-400 mt-1">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span class="text-gray-600 dark:text-gray-400">MOLYKO, BUEA, CAMEROON</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="text-primary-600 dark:text-primary-400 mt-1">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <span class="text-gray-600 dark:text-gray-400">+237 651 990 298</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="text-primary-600 dark:text-primary-400 mt-1">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="text-gray-600 dark:text-gray-400">flashq@gmail.com</span>
                        </div>
                    </div>
                </div>
                
                <!-- Message Form -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">SEND A MESSAGE</h3>
                    <form id="contactForm" class="space-y-4">
                        <div>
                            <input type="text" placeholder="Your Name" class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-400">
                        </div>
                        <div>
                            <input type="email" placeholder="Your Email" class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-400">
                        </div>
                        <div>
                            <textarea placeholder="Your Message" rows="3" class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-400"></textarea>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition-colors">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-200 dark:border-gray-700 mt-10 pt-6 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    &copy; <?= date('Y') ?> FLASHQ. All Rights Reserved. Designed by WatchDog.E
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Main JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Dark mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;
            
            // Check for user preference in localStorage or system preference
            const darkMode = localStorage.getItem('darkMode') || 
                             (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (darkMode === 'true' || darkMode === true) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            
            darkModeToggle.addEventListener('click', function() {
                htmlElement.classList.toggle('dark');
                localStorage.setItem('darkMode', htmlElement.classList.contains('dark'));
            });
            
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Contact form submission
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Message sent! We will get back to you soon.');
                    contactForm.reset();
                });
            }
        });
    </script>
</body>
</html>