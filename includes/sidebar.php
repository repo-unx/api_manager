<!-- Sidebar -->
<aside id="sidebar" class="bg-white shadow-lg w-64 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30 md:z-0 pt-16">
    <div class="h-full overflow-y-auto">
        <nav class="px-4 py-4">
            <ul class="space-y-2">
                <li>
                    <a href="/" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'dashboard' ? 'bg-gray-100 font-medium' : '' ?>">
                        <i class="fas fa-tachometer-alt w-5 h-5 mr-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/aggregators.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'aggregators' ? 'bg-gray-100 font-medium' : '' ?>">
                        <i class="fas fa-server w-5 h-5 mr-2"></i>
                        <span>API Aggregators</span>
                    </a>
                </li>
                <li>
                    <a href="/endpoints.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'endpoints' ? 'bg-gray-100 font-medium' : '' ?>">
                        <i class="fas fa-link w-5 h-5 mr-2"></i>
                        <span>API Endpoints</span>
                    </a>
                </li>
                <li>
                    <a href="/templates.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'templates' ? 'bg-gray-100 font-medium' : '' ?>">
                        <i class="fas fa-file-code w-5 h-5 mr-2"></i>
                        <span>Request Templates</span>
                    </a>
                </li>
                <li>
                    <a href="/logs.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'logs' ? 'bg-gray-100 font-medium' : '' ?>">
                        <i class="fas fa-history w-5 h-5 mr-2"></i>
                        <span>API Request Logs</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Main content -->
<main class="flex-1 md:ml-64 p-4 min-h-screen pt-20">
    <div class="container mx-auto">
