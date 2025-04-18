    </div> <!-- End container -->
</main>

</div> <!-- End flex container -->

<footer class="bg-gradient-to-r from-indigo-900 via-primary to-indigo-800 text-white py-6 shadow-inner mt-8">
    <div class="container mx-auto px-4 max-w-screen-xl">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="bg-white text-indigo-800 w-8 h-8 rounded flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-cube text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg"><?= APP_NAME ?></h3>
                    <p class="text-xs text-indigo-200">Version <?= APP_VERSION ?></p>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                <a href="/docs/index.php" class="text-white hover:text-indigo-200 transition-colors text-sm">
                    <i class="fas fa-book mr-1"></i> Documentation
                </a>
                <a href="/endpoint_playground.php" class="text-white hover:text-indigo-200 transition-colors text-sm">
                    <i class="fas fa-flask mr-1"></i> API Playground
                </a>
                <a href="https://github.com/your-repo/api-manager" target="_blank" class="text-white hover:text-indigo-200 transition-colors text-sm">
                    <i class="fab fa-github mr-1"></i> GitHub
                </a>
            </div>
        </div>
        
        <div class="border-t border-indigo-700 mt-6 pt-6 text-center text-xs text-indigo-200">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Main JavaScript -->
<script src="/assets/js/main.js"></script>

</body>
</html>
