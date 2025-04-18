<?php
/**
 * API Manager - Dashboard
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Initialize database if needed
initializeDatabaseTables();

// Set current page for sidebar highlight
$currentPage = 'dashboard';
$pageTitle = 'Dashboard';

// Get statistics
$aggregatorCount = getAggregateData(TABLE_AGGREGATORS, 'id');
$endpointCount = getAggregateData(TABLE_ENDPOINTS, 'id');
$logCount = getAggregateData(TABLE_LOGS, 'id');
$templateCount = getAggregateData(TABLE_TEMPLATES, 'id');

// Get recent logs
$recentLogs = getAllRecords(TABLE_LOGS, 1, 5, null, [], 'created_at DESC');

// Get recent endpoints
$recentEndpoints = getAllRecords(TABLE_ENDPOINTS, 1, 5, null, [], 'created_at DESC');

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Dashboard Content -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard</h1>
    <p class="text-gray-600">Welcome to the API Manager dashboard</p>
</div>

<?php displayMessage(); ?>

<!-- Documentation/Tutorial -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <div class="flex-shrink-0 mt-0.5">
            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Dokumentasi Penggunaan Dashboard</h3>
            <div class="mt-2 text-sm text-blue-700">
                <p class="mb-2">Dashboard menampilkan gambaran umum API Manager Anda, termasuk:</p>
                <ul class="list-disc list-inside space-y-1 mb-2">
                    <li>Jumlah API Aggregator, Endpoint, Template, dan Log</li>
                    <li>Endpoint API terbaru yang telah dibuat</li>
                    <li>Log request API terbaru</li>
                </ul>
                <p>Untuk mulai menggunakan sistem:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Buat API Aggregator baru (penyedia API)</li>
                    <li>Tambahkan Endpoint API untuk setiap layanan</li>
                    <li>Buat Template untuk request yang sering digunakan</li>
                    <li>Pantau aktivitas API melalui Log Request</li>
                </ol>
                <p class="mt-2 text-blue-600 cursor-pointer" onclick="this.parentElement.classList.toggle('!h-auto'); this.textContent = this.textContent === 'Lihat lebih banyak...' ? 'Sembunyikan' : 'Lihat lebih banyak...'">Lihat lebih banyak...</p>
            </div>
        </div>
        <button class="ml-auto flex-shrink-0 text-blue-500 hover:text-blue-700" onclick="this.closest('.bg-blue-50').classList.add('hidden')">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                <i class="fas fa-server text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">API Aggregators</p>
                <p class="text-2xl font-bold"><?= $aggregatorCount ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="/aggregators.php" class="text-indigo-500 hover:text-indigo-600 text-sm font-medium">View all <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                <i class="fas fa-link text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">API Endpoints</p>
                <p class="text-2xl font-bold"><?= $endpointCount ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="/endpoints.php" class="text-green-500 hover:text-green-600 text-sm font-medium">View all <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <i class="fas fa-file-code text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Request Templates</p>
                <p class="text-2xl font-bold"><?= $templateCount ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="/templates.php" class="text-blue-500 hover:text-blue-600 text-sm font-medium">View all <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                <i class="fas fa-history text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">API Request Logs</p>
                <p class="text-2xl font-bold"><?= $logCount ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="/logs.php" class="text-purple-500 hover:text-purple-600 text-sm font-medium">View all <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Endpoints -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Recent Endpoints</h2>
        </div>
        
        <div class="p-4">
            <?php if (count($recentEndpoints) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentEndpoints as $endpoint): ?>
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800">
                                        <a href="/endpoints_form.php?id=<?= $endpoint['id'] ?>" class="text-indigo-600 hover:text-indigo-700">
                                            <?= htmlspecialchars($endpoint['name']) ?>
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?= htmlspecialchars($endpoint['method']) ?>
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                        <?= getStatusBadge($endpoint['status']) ?>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                        <?= formatDate($endpoint['created_at'], 'M d, Y') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="/endpoints.php" class="text-indigo-500 hover:text-indigo-600 text-sm font-medium">
                        View all endpoints <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-gray-50 rounded-md p-4 text-center">
                    <p class="text-gray-500">No endpoints have been created yet.</p>
                    <a href="/endpoints_form.php" class="mt-2 inline-block btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Create Endpoint
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent API Logs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Recent API Request Logs</h2>
        </div>
        
        <div class="p-4">
            <?php if (count($recentLogs) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentLogs as $log): 
                                $endpoint = getRecordById(TABLE_ENDPOINTS, $log['endpoint_id']);
                                $statusClass = $log['response_code'] >= 200 && $log['response_code'] < 300 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800';
                            ?>
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800">
                                        <?= $endpoint ? htmlspecialchars($endpoint['name']) : 'Unknown' ?>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?= htmlspecialchars($log['request_method']) ?>
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                            <?= $log['response_code'] ?: 'N/A' ?>
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                        <?= formatDate($log['created_at'], 'H:i:s') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="/logs.php" class="text-indigo-500 hover:text-indigo-600 text-sm font-medium">
                        View all logs <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-gray-50 rounded-md p-4 text-center">
                    <p class="text-gray-500">No API request logs have been recorded yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-medium text-gray-800 mb-4">Quick Actions</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/aggregators_form.php" class="btn btn-primary flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> New Aggregator
        </a>
        <a href="/endpoints_form.php" class="btn btn-primary flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> New Endpoint
        </a>
        <a href="/templates_form.php" class="btn btn-primary flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> New Template
        </a>
        <a href="/logs.php" class="btn btn-secondary flex items-center justify-center">
            <i class="fas fa-search mr-2"></i> Search Logs
        </a>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
