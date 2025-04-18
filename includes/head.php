<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- JSON Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.10.0/jsoneditor.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.10.0/jsoneditor.min.css">
    
    <!-- Custom styles -->
    <link rel="stylesheet" href="/assets/css/custom.css">
    
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#0f172a',
                    secondary: '#4f46e5',
                    accent: '#06b6d4'
                }
            }
        },
        darkMode: 'class'
    }
    </script>
    
    <style type="text/tailwindcss">
        @layer components {
            .btn {
                @apply px-4 py-2 rounded font-medium transition-colors;
            }
            .btn-primary {
                @apply bg-secondary text-white hover:bg-indigo-600;
            }
            .btn-secondary {
                @apply bg-gray-200 text-gray-800 hover:bg-gray-300;
            }
            .btn-danger {
                @apply bg-red-500 text-white hover:bg-red-600;
            }
            .form-input {
                @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent;
            }
            .form-label {
                @apply block text-sm font-medium text-gray-700 mb-1;
            }
            .card {
                @apply bg-white rounded-lg shadow-md overflow-hidden;
            }
            .card-header {
                @apply px-4 py-3 bg-gray-50 border-b border-gray-200 font-medium;
            }
            .card-body {
                @apply p-4;
            }
            .table-container {
                @apply overflow-x-auto rounded-lg border border-gray-200;
            }
            .table {
                @apply min-w-full divide-y divide-gray-200;
            }
            .table th {
                @apply px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
            }
            .table td {
                @apply px-6 py-4 whitespace-nowrap text-sm text-gray-500;
            }
            .table tr {
                @apply bg-white border-b border-gray-200;
            }
            .table tr:hover {
                @apply bg-gray-50;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-gradient-to-r from-indigo-900 via-primary to-indigo-800 text-white shadow-md fixed top-0 left-0 right-0 z-30">
        <div class="container mx-auto px-4 py-3">
            <!-- Main Header Content -->
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <!-- Mobile sidebar toggle button -->
                    <button id="desktop-sidebar-toggle" class="text-white mr-4 md:hidden flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-800/40 hover:bg-indigo-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-300" aria-label="Toggle Sidebar">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <!-- App Logo and Name -->
                    <div class="flex items-center">
                        <span class="bg-white text-indigo-800 w-8 h-8 rounded flex items-center justify-center mr-3 shadow-lg">
                            <i class="fas fa-cube text-lg"></i>
                        </span>
                        <h1 class="text-xl font-bold tracking-tight">
                            <?= APP_NAME ?>
                        </h1>
                    </div>
                </div>
                
                <!-- Right Menu -->
                <div class="flex items-center space-x-1">
                    <!-- New Features Menu -->
                    <div class="hidden md:block relative group">
                        <button class="flex items-center bg-indigo-800/30 hover:bg-indigo-800/50 rounded-lg px-3 py-2 text-sm transition-all duration-300">
                            <span class="mr-1">New Features</span>
                            <i class="fas fa-chevron-down text-xs opacity-70"></i>
                        </button>
                        <div class="absolute right-0 top-full mt-1 w-64 bg-white rounded-lg shadow-lg overflow-hidden transform origin-top scale-0 group-hover:scale-100 transition-transform z-50">
                            <div class="p-2 border-b border-gray-100 flex items-center bg-indigo-50">
                                <span class="px-2 py-1 rounded-md bg-indigo-100 text-indigo-800 text-xs font-semibold">NEW FEATURES</span>
                            </div>
                            <div class="py-2">
                                <a href="/export_import.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-file-export w-5 text-indigo-500"></i>
                                    <span class="ml-2 text-sm">Export/Import Configurations</span>
                                </a>
                                <a href="/endpoint_playground.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-vial w-5 text-indigo-500"></i>
                                    <span class="ml-2 text-sm">API Testing Playground</span>
                                </a>
                                <a href="/visualizations.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-chart-bar w-5 text-indigo-500"></i>
                                    <span class="ml-2 text-sm">Usage Statistics</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theme toggle button -->
                    <button class="text-white p-2 rounded-lg hover:bg-indigo-800/50 transition-all duration-300 hidden md:flex items-center justify-center" title="Toggle Theme (Coming Soon)">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- Help button -->
                    <a href="/docs/index.php" class="text-white p-2 rounded-lg hover:bg-indigo-800/50 transition-all duration-300 hidden md:flex items-center justify-center" title="Documentation">
                        <i class="fas fa-question-circle"></i>
                    </a>
                    
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="md:hidden text-white p-2 rounded-lg hover:bg-indigo-800/50 transition-all duration-300">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Desktop Navigation and Search Bar -->
            <div class="hidden md:block mt-2 pb-0">
                <!-- Main Navigation Menu -->
                <div class="flex items-center justify-between mb-2">
                    <nav class="flex space-x-1">
                        <a href="/" class="px-3 py-2 text-sm font-medium rounded-lg <?= $currentPage === 'dashboard' ? 'bg-indigo-700 text-white' : 'text-white/90 hover:bg-indigo-700/40' ?> transition-colors">
                            <i class="fas fa-tachometer-alt mr-1.5"></i> Dashboard
                        </a>
                        <a href="/aggregators.php" class="px-3 py-2 text-sm font-medium rounded-lg <?= $currentPage === 'aggregators' ? 'bg-indigo-700 text-white' : 'text-white/90 hover:bg-indigo-700/40' ?> transition-colors">
                            <i class="fas fa-server mr-1.5"></i> Aggregators
                        </a>
                        <a href="/endpoints.php" class="px-3 py-2 text-sm font-medium rounded-lg <?= $currentPage === 'endpoints' ? 'bg-indigo-700 text-white' : 'text-white/90 hover:bg-indigo-700/40' ?> transition-colors">
                            <i class="fas fa-link mr-1.5"></i> Endpoints
                        </a>
                        <a href="/templates.php" class="px-3 py-2 text-sm font-medium rounded-lg <?= $currentPage === 'templates' ? 'bg-indigo-700 text-white' : 'text-white/90 hover:bg-indigo-700/40' ?> transition-colors">
                            <i class="fas fa-file-code mr-1.5"></i> Templates
                        </a>
                        <a href="/logs.php" class="px-3 py-2 text-sm font-medium rounded-lg <?= $currentPage === 'logs' ? 'bg-indigo-700 text-white' : 'text-white/90 hover:bg-indigo-700/40' ?> transition-colors">
                            <i class="fas fa-history mr-1.5"></i> Logs
                        </a>
                        
                        <!-- Tools Dropdown -->
                        <div class="relative group">
                            <button class="px-3 py-2 text-sm font-medium rounded-lg text-white/90 hover:bg-indigo-700/40 transition-colors group-hover:bg-indigo-700/40">
                                <i class="fas fa-tools mr-1.5"></i> Tools <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div class="absolute left-0 top-full mt-1 w-48 bg-white rounded-lg shadow-lg overflow-hidden transform origin-top scale-0 group-hover:scale-100 transition-transform z-50">
                                <div class="py-1">
                                    <a href="/endpoint_playground.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors <?= $currentPage === 'playground' ? 'bg-indigo-50 text-indigo-700 font-medium' : '' ?>">
                                        <i class="fas fa-flask w-5 text-indigo-500 mr-2"></i> API Playground
                                    </a>
                                    <a href="/export_import.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors <?= $currentPage === 'export_import' ? 'bg-indigo-50 text-indigo-700 font-medium' : '' ?>">
                                        <i class="fas fa-exchange-alt w-5 text-indigo-500 mr-2"></i> Export/Import
                                    </a>
                                    <a href="/visualizations.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors <?= $currentPage === 'visualizations' ? 'bg-indigo-50 text-indigo-700 font-medium' : '' ?>">
                                        <i class="fas fa-chart-bar w-5 text-indigo-500 mr-2"></i> Statistics
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="/docs/index.php" class="flex items-center px-4 py-2 text-gray-800 hover:bg-indigo-50 transition-colors <?= $currentPage === 'docs' ? 'bg-indigo-50 text-indigo-700 font-medium' : '' ?>">
                                        <i class="fas fa-book w-5 text-indigo-500 mr-2"></i> Documentation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </nav>
                    
                    <!-- Quick Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="/aggregators_form.php" class="bg-indigo-700 hover:bg-indigo-600 py-1.5 px-3 text-sm rounded-lg flex items-center transition-colors duration-300">
                            <i class="fas fa-plus text-xs mr-1.5"></i> New Aggregator
                        </a>
                        <a href="/endpoints_form.php" class="bg-indigo-700 hover:bg-indigo-600 py-1.5 px-3 text-sm rounded-lg flex items-center transition-colors duration-300">
                            <i class="fas fa-plus text-xs mr-1.5"></i> New Endpoint
                        </a>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="flex">
                    <div class="relative w-full max-w-lg">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-white/50">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" class="w-full bg-white/10 border border-indigo-800/30 rounded-lg py-1.5 pl-10 pr-4 placeholder-white/60 text-white focus:outline-none focus:ring-2 focus:ring-indigo-300 text-sm" placeholder="Search API endpoints, templates, or logs...">
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <div class="flex flex-1">
        <!-- Mobile sidebar backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>
        <!-- Sidebar overlay for open state -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-30 z-10 hidden"></div>
