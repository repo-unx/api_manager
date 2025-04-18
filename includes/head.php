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
    <header class="bg-primary text-white shadow-md fixed top-0 left-0 right-0 z-30">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <!-- Desktop sidebar toggle button -->
                <button id="desktop-sidebar-toggle" class="text-white mr-4 hidden md:flex items-center justify-center w-8 h-8 rounded-full hover:bg-indigo-800 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-300" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-cube mr-2"></i>
                    <?= APP_NAME ?>
                </h1>
            </div>
            
            <div class="flex items-center">
                <!-- Theme toggle button (future implementation) -->
                <button class="text-white mx-3 hover:text-indigo-200 transition-colors hidden md:block" title="Coming Soon: Dark Theme">
                    <i class="fas fa-moon"></i>
                </button>
                
                <!-- Notifications (future implementation) -->
                <button class="text-white mx-3 hover:text-indigo-200 transition-colors hidden md:block relative" title="Coming Soon: Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                </button>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-white p-1 rounded-md hover:bg-indigo-800">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>
    
    <div class="flex flex-1">
        <!-- Mobile sidebar backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>
        <!-- Sidebar overlay for open state -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-30 z-10 hidden"></div>
