<!-- Sidebar -->
<aside id="sidebar" class="bg-white shadow-lg w-64 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30 md:z-10 pt-16 pb-4">
    <div class="h-full overflow-y-auto flex flex-col justify-between">
        <nav class="px-4 py-4">
            <div class="mb-4 px-2">
                <div class="text-xs text-gray-400 uppercase font-semibold tracking-wider">MENU UTAMA</div>
            </div>
            <ul class="space-y-2">
                <li>
                    <a href="/" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'dashboard' ? 'bg-indigo-50 font-medium text-indigo-700 border-l-4 border-indigo-500' : '' ?>">
                        <i class="fas fa-tachometer-alt w-5 h-5 mr-3 <?= $currentPage === 'dashboard' ? 'text-indigo-600' : 'text-gray-500' ?>"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/aggregators.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'aggregators' ? 'bg-indigo-50 font-medium text-indigo-700 border-l-4 border-indigo-500' : '' ?>">
                        <i class="fas fa-server w-5 h-5 mr-3 <?= $currentPage === 'aggregators' ? 'text-indigo-600' : 'text-gray-500' ?>"></i>
                        <span>API Aggregators</span>
                    </a>
                </li>
                <li>
                    <a href="/endpoints.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'endpoints' ? 'bg-indigo-50 font-medium text-indigo-700 border-l-4 border-indigo-500' : '' ?>">
                        <i class="fas fa-link w-5 h-5 mr-3 <?= $currentPage === 'endpoints' ? 'text-indigo-600' : 'text-gray-500' ?>"></i>
                        <span>API Endpoints</span>
                    </a>
                </li>
                <li>
                    <a href="/templates.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'templates' ? 'bg-indigo-50 font-medium text-indigo-700 border-l-4 border-indigo-500' : '' ?>">
                        <i class="fas fa-file-code w-5 h-5 mr-3 <?= $currentPage === 'templates' ? 'text-indigo-600' : 'text-gray-500' ?>"></i>
                        <span>Request Templates</span>
                    </a>
                </li>
                <li>
                    <a href="/logs.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'logs' ? 'bg-indigo-50 font-medium text-indigo-700 border-l-4 border-indigo-500' : '' ?>">
                        <i class="fas fa-history w-5 h-5 mr-3 <?= $currentPage === 'logs' ? 'text-indigo-600' : 'text-gray-500' ?>"></i>
                        <span>API Request Logs</span>
                    </a>
                </li>
                <li>
                    <a href="/docs/index.php" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 <?= $currentPage === 'docs' ? 'bg-indigo-50 font-medium text-indigo-700 border-l-4 border-indigo-500' : '' ?>">
                        <i class="fas fa-book w-5 h-5 mr-3 <?= $currentPage === 'docs' ? 'text-indigo-600' : 'text-gray-500' ?>"></i>
                        <span>Documentation</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Sidebar footer with collapse button for desktop -->
        <div class="mt-auto px-4 py-2 hidden md:block">
            <button id="desktop-sidebar-collapse" class="w-full flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-left mr-2"></i>
                <span>Collapse</span>
            </button>
        </div>
    </div>
</aside>

<!-- Main content -->
<main class="flex-1 md:ml-64 p-4 min-h-screen pt-20" id="main-content">
    <div class="container mx-auto">
