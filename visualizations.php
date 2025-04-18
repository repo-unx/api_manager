<?php
/**
 * API Manager - Visualization Tools for API Usage Statistics
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'visualizations';
$pageTitle = 'API Usage Statistics';

// Get all aggregators for filter dropdown
$aggregators = getAllRecords(TABLE_AGGREGATORS, 1, 100, 'status = :status', [':status' => STATUS_ACTIVE], 'name ASC');

// Get all endpoints for filter dropdown
$endpoints = getAllRecords(TABLE_ENDPOINTS, 1, 100, 'status = :status', [':status' => STATUS_ACTIVE], 'name ASC');

// Process filtering
$whereClause = 'id IS NOT NULL';
$params = [];
$filterActive = false;
$dateFrom = date('Y-m-d', strtotime('-30 days'));
$dateTo = date('Y-m-d');
$timeFrame = '30days';

if (isset($_GET['filter'])) {
    // Date filtering
    if (isset($_GET['time_frame'])) {
        $timeFrame = $_GET['time_frame'];
        
        switch ($timeFrame) {
            case '7days':
                $dateFrom = date('Y-m-d', strtotime('-7 days'));
                break;
            case '30days':
                $dateFrom = date('Y-m-d', strtotime('-30 days'));
                break;
            case '90days':
                $dateFrom = date('Y-m-d', strtotime('-90 days'));
                break;
            case 'custom':
                if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                    $dateFrom = $_GET['date_from'];
                }
                if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                    $dateTo = $_GET['date_to'];
                }
                break;
        }
        
        $whereClause .= ' AND DATE(timestamp) BETWEEN :date_from AND :date_to';
        $params[':date_from'] = $dateFrom;
        $params[':date_to'] = $dateTo;
        $filterActive = true;
    }
    
    // Aggregator filtering
    if (isset($_GET['aggregator_id']) && !empty($_GET['aggregator_id'])) {
        $whereClause .= ' AND aggregator_id = :aggregator_id';
        $params[':aggregator_id'] = $_GET['aggregator_id'];
        $filterActive = true;
    }
    
    // Endpoint filtering
    if (isset($_GET['endpoint_id']) && !empty($_GET['endpoint_id'])) {
        $whereClause .= ' AND endpoint_id = :endpoint_id';
        $params[':endpoint_id'] = $_GET['endpoint_id'];
        $filterActive = true;
    }
    
    // Status code filtering
    if (isset($_GET['status_code']) && !empty($_GET['status_code'])) {
        $whereClause .= ' AND status_code = :status_code';
        $params[':status_code'] = $_GET['status_code'];
        $filterActive = true;
    }
}

// Get logs data for visualization
$logs = getAllRecords(TABLE_LOGS, 1, 10000, $whereClause, $params, 'timestamp DESC');

// Process data for charts
// 1. Daily Request Counts
$dailyRequestsData = [];
$dailyDates = [];
$successCount = 0;
$errorCount = 0;
$totalResponseTime = 0;
$maxResponseTime = 0;
$minResponseTime = PHP_INT_MAX;
$endpointUsage = [];
$aggregatorUsage = [];
$statusCodes = [];

// Process logs data
foreach ($logs as $log) {
    // Format date for daily chart
    $date = date('Y-m-d', strtotime($log['timestamp']));
    if (!isset($dailyRequestsData[$date])) {
        $dailyRequestsData[$date] = 0;
    }
    $dailyRequestsData[$date]++;
    
    // Add to the array of dates
    if (!in_array($date, $dailyDates)) {
        $dailyDates[] = $date;
    }
    
    // Count success/error requests
    if ($log['status_code'] >= 200 && $log['status_code'] < 400) {
        $successCount++;
    } else {
        $errorCount++;
    }
    
    // Process response time
    $responseTime = $log['response_time'];
    $totalResponseTime += $responseTime;
    if ($responseTime > $maxResponseTime) {
        $maxResponseTime = $responseTime;
    }
    if ($responseTime < $minResponseTime) {
        $minResponseTime = $responseTime;
    }
    
    // Count endpoint usage
    $endpointId = $log['endpoint_id'];
    if (!isset($endpointUsage[$endpointId])) {
        $endpointUsage[$endpointId] = 0;
    }
    $endpointUsage[$endpointId]++;
    
    // Count aggregator usage
    $aggregatorId = $log['aggregator_id'];
    if (!isset($aggregatorUsage[$aggregatorId])) {
        $aggregatorUsage[$aggregatorId] = 0;
    }
    $aggregatorUsage[$aggregatorId]++;
    
    // Count status codes
    $statusCode = $log['status_code'];
    if (!isset($statusCodes[$statusCode])) {
        $statusCodes[$statusCode] = 0;
    }
    $statusCodes[$statusCode]++;
}

// Sort dates chronologically
sort($dailyDates);

// Prepare data for daily requests chart
$dailyRequestsChartData = [];
foreach ($dailyDates as $date) {
    $dailyRequestsChartData[] = $dailyRequestsData[$date];
}

// Calculate average response time
$avgResponseTime = count($logs) > 0 ? $totalResponseTime / count($logs) : 0;
if ($minResponseTime === PHP_INT_MAX) {
    $minResponseTime = 0;
}

// Get endpoint names for the chart
$endpointNames = [];
foreach ($endpoints as $endpoint) {
    $endpointNames[$endpoint['id']] = $endpoint['name'];
}

// Get aggregator names for the chart
$aggregatorNames = [];
foreach ($aggregators as $aggregator) {
    $aggregatorNames[$aggregator['id']] = $aggregator['name'];
}

// Prepare data for endpoint usage pie chart
$endpointLabels = [];
$endpointValues = [];
arsort($endpointUsage); // Sort by usage (highest first)
foreach ($endpointUsage as $id => $count) {
    $endpointLabels[] = isset($endpointNames[$id]) ? $endpointNames[$id] : "Unknown ($id)";
    $endpointValues[] = $count;
}

// Prepare data for aggregator usage pie chart
$aggregatorLabels = [];
$aggregatorValues = [];
arsort($aggregatorUsage); // Sort by usage (highest first)
foreach ($aggregatorUsage as $id => $count) {
    $aggregatorLabels[] = isset($aggregatorNames[$id]) ? $aggregatorNames[$id] : "Unknown ($id)";
    $aggregatorValues[] = $count;
}

// Prepare data for status code chart
$statusLabels = [];
$statusValues = [];
ksort($statusCodes); // Sort by status code
foreach ($statusCodes as $code => $count) {
    $statusLabels[] = $code;
    $statusValues[] = $count;
}

// Convert data to JSON for chart.js
$chartData = [
    'dates' => json_encode($dailyDates),
    'dailyRequests' => json_encode($dailyRequestsChartData),
    'endpointLabels' => json_encode($endpointLabels),
    'endpointValues' => json_encode($endpointValues),
    'aggregatorLabels' => json_encode($aggregatorLabels),
    'aggregatorValues' => json_encode($aggregatorValues),
    'statusLabels' => json_encode($statusLabels),
    'statusValues' => json_encode($statusValues)
];

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">API Usage Statistics</h1>
        <p class="text-gray-600">Visualize and analyze API performance and usage patterns</p>
    </div>
</div>

<?php displayMessage(); ?>

<!-- Filters Section -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-800 flex items-center">
            <i class="fas fa-filter text-indigo-500 mr-2"></i>
            Filter Data
        </h2>
    </div>
    
    <div class="p-6">
        <form method="GET" class="space-y-4">
            <input type="hidden" name="filter" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Time Frame Selection -->
                <div>
                    <label for="time_frame" class="form-label">Time Frame</label>
                    <select id="time_frame" name="time_frame" class="form-input" onchange="toggleCustomDateFields()">
                        <option value="7days" <?= $timeFrame === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                        <option value="30days" <?= $timeFrame === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
                        <option value="90days" <?= $timeFrame === '90days' ? 'selected' : '' ?>>Last 90 Days</option>
                        <option value="custom" <?= $timeFrame === 'custom' ? 'selected' : '' ?>>Custom Range</option>
                    </select>
                </div>
                
                <!-- Custom Date Range (initially hidden) -->
                <div id="custom_date_container" class="<?= $timeFrame !== 'custom' ? 'hidden' : '' ?> md:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" id="date_from" name="date_from" class="form-input" value="<?= $dateFrom ?>">
                    </div>
                    <div>
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" id="date_to" name="date_to" class="form-input" value="<?= $dateTo ?>">
                    </div>
                </div>
                
                <!-- Aggregator Filter -->
                <div>
                    <label for="aggregator_id" class="form-label">Aggregator</label>
                    <select id="aggregator_id" name="aggregator_id" class="form-input">
                        <option value="">All Aggregators</option>
                        <?php foreach ($aggregators as $aggregator): ?>
                            <option value="<?= $aggregator['id'] ?>" <?= isset($_GET['aggregator_id']) && $_GET['aggregator_id'] == $aggregator['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($aggregator['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Endpoint Filter -->
                <div>
                    <label for="endpoint_id" class="form-label">Endpoint</label>
                    <select id="endpoint_id" name="endpoint_id" class="form-input">
                        <option value="">All Endpoints</option>
                        <?php foreach ($endpoints as $endpoint): ?>
                            <option value="<?= $endpoint['id'] ?>" <?= isset($_GET['endpoint_id']) && $_GET['endpoint_id'] == $endpoint['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($endpoint['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Status Code Filter -->
                <div>
                    <label for="status_code" class="form-label">Status Code</label>
                    <select id="status_code" name="status_code" class="form-input">
                        <option value="">All Status Codes</option>
                        <option value="200" <?= isset($_GET['status_code']) && $_GET['status_code'] == '200' ? 'selected' : '' ?>>200 (Success)</option>
                        <option value="201" <?= isset($_GET['status_code']) && $_GET['status_code'] == '201' ? 'selected' : '' ?>>201 (Created)</option>
                        <option value="400" <?= isset($_GET['status_code']) && $_GET['status_code'] == '400' ? 'selected' : '' ?>>400 (Bad Request)</option>
                        <option value="401" <?= isset($_GET['status_code']) && $_GET['status_code'] == '401' ? 'selected' : '' ?>>401 (Unauthorized)</option>
                        <option value="404" <?= isset($_GET['status_code']) && $_GET['status_code'] == '404' ? 'selected' : '' ?>>404 (Not Found)</option>
                        <option value="500" <?= isset($_GET['status_code']) && $_GET['status_code'] == '500' ? 'selected' : '' ?>>500 (Server Error)</option>
                    </select>
                </div>
                
                <!-- Apply Filters Button -->
                <div class="md:col-span-1 flex items-end">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-search mr-2"></i> Apply Filters
                    </button>
                </div>
                
                <!-- Clear Filters Button -->
                <?php if ($filterActive): ?>
                <div class="md:col-span-1 flex items-end">
                    <a href="visualizations.php" class="btn btn-secondary w-full text-center">
                        <i class="fas fa-times mr-2"></i> Clear Filters
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Key Metrics Section -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Requests -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-indigo-50 px-4 py-2 border-b border-indigo-100">
            <h3 class="font-medium text-gray-700">Total Requests</h3>
        </div>
        <div class="p-6 flex items-center">
            <div class="bg-indigo-100 rounded-full p-3 mr-4">
                <i class="fas fa-exchange-alt text-indigo-600 text-xl"></i>
            </div>
            <div>
                <span class="block text-3xl font-bold text-gray-800"><?= count($logs) ?></span>
                <span class="text-sm text-gray-600">
                    From <?= date('M j, Y', strtotime($dateFrom)) ?> to <?= date('M j, Y', strtotime($dateTo)) ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Success Rate -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-green-50 px-4 py-2 border-b border-green-100">
            <h3 class="font-medium text-gray-700">Success Rate</h3>
        </div>
        <div class="p-6 flex items-center">
            <div class="bg-green-100 rounded-full p-3 mr-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <div>
                <span class="block text-3xl font-bold text-gray-800">
                    <?= count($logs) > 0 ? round(($successCount / count($logs)) * 100) : 0 ?>%
                </span>
                <span class="text-sm text-gray-600">
                    <?= $successCount ?> successful / <?= $errorCount ?> failed
                </span>
            </div>
        </div>
    </div>
    
    <!-- Average Response Time -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-50 px-4 py-2 border-b border-blue-100">
            <h3 class="font-medium text-gray-700">Avg. Response Time</h3>
        </div>
        <div class="p-6 flex items-center">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
                <i class="fas fa-clock text-blue-600 text-xl"></i>
            </div>
            <div>
                <span class="block text-3xl font-bold text-gray-800"><?= round($avgResponseTime, 2) ?> ms</span>
                <span class="text-sm text-gray-600">
                    Min: <?= round($minResponseTime, 2) ?> ms, Max: <?= round($maxResponseTime, 2) ?> ms
                </span>
            </div>
        </div>
    </div>
    
    <!-- Most Used Endpoint -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-purple-50 px-4 py-2 border-b border-purple-100">
            <h3 class="font-medium text-gray-700">Most Used Endpoint</h3>
        </div>
        <div class="p-6 flex items-center">
            <div class="bg-purple-100 rounded-full p-3 mr-4">
                <i class="fas fa-star text-purple-600 text-xl"></i>
            </div>
            <div>
                <span class="block text-xl font-bold text-gray-800 truncate max-w-[180px]">
                    <?= count($endpointLabels) > 0 ? $endpointLabels[0] : 'N/A' ?>
                </span>
                <span class="text-sm text-gray-600">
                    <?= count($endpointValues) > 0 ? $endpointValues[0] . ' requests' : 'No data' ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Daily Requests Chart -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Daily Request Volume</h2>
        </div>
        <div class="p-6">
            <canvas id="dailyRequestsChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Status Code Chart -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Status Code Distribution</h2>
        </div>
        <div class="p-6">
            <canvas id="statusCodeChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Additional Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Endpoint Usage Chart -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Endpoint Usage</h2>
        </div>
        <div class="p-6">
            <canvas id="endpointUsageChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Aggregator Usage Chart -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Aggregator Usage</h2>
        </div>
        <div class="p-6">
            <canvas id="aggregatorUsageChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Add Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Visualization Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle custom date fields when time frame is changed
    window.toggleCustomDateFields = function() {
        const timeFrame = document.getElementById('time_frame').value;
        const customDateContainer = document.getElementById('custom_date_container');
        
        if (timeFrame === 'custom') {
            customDateContainer.classList.remove('hidden');
        } else {
            customDateContainer.classList.add('hidden');
        }
    };
    
    // Parse data from PHP
    const dates = <?= $chartData['dates'] ?>;
    const dailyRequests = <?= $chartData['dailyRequests'] ?>;
    const endpointLabels = <?= $chartData['endpointLabels'] ?>;
    const endpointValues = <?= $chartData['endpointValues'] ?>;
    const aggregatorLabels = <?= $chartData['aggregatorLabels'] ?>;
    const aggregatorValues = <?= $chartData['aggregatorValues'] ?>;
    const statusLabels = <?= $chartData['statusLabels'] ?>;
    const statusValues = <?= $chartData['statusValues'] ?>;
    
    // Generate color arrays
    function generateColors(count, alpha = 0.7) {
        const colors = [];
        const baseColors = [
            `rgba(99, 102, 241, ${alpha})`,    // Indigo
            `rgba(16, 185, 129, ${alpha})`,    // Emerald
            `rgba(245, 158, 11, ${alpha})`,    // Amber
            `rgba(239, 68, 68, ${alpha})`,     // Red
            `rgba(59, 130, 246, ${alpha})`,    // Blue
            `rgba(217, 70, 239, ${alpha})`,    // Fuchsia
            `rgba(14, 165, 233, ${alpha})`,    // Sky
            `rgba(20, 184, 166, ${alpha})`,    // Teal
            `rgba(168, 85, 247, ${alpha})`,    // Purple
            `rgba(236, 72, 153, ${alpha})`     // Pink
        ];
        
        for (let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        
        return colors;
    }
    
    // Status code color mapping
    function getStatusCodeColor(code) {
        if (code >= 200 && code < 300) {
            return 'rgba(16, 185, 129, 0.7)';  // Green for 2xx
        } else if (code >= 300 && code < 400) {
            return 'rgba(245, 158, 11, 0.7)';  // Yellow for 3xx
        } else if (code >= 400 && code < 500) {
            return 'rgba(239, 68, 68, 0.7)';   // Red for 4xx
        } else {
            return 'rgba(99, 102, 241, 0.7)';  // Indigo for other codes
        }
    }
    
    // Status code colors
    const statusColors = statusLabels.map(code => getStatusCodeColor(code));
    
    // Daily Requests Chart
    const dailyRequestsChart = new Chart(
        document.getElementById('dailyRequestsChart').getContext('2d'),
        {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Requests',
                        data: dailyRequests,
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return context.raw + ' requests';
                            }
                        }
                    }
                }
            }
        }
    );
    
    // Status Code Chart
    const statusCodeChart = new Chart(
        document.getElementById('statusCodeChart').getContext('2d'),
        {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [
                    {
                        label: 'Count',
                        data: statusValues,
                        backgroundColor: statusColors,
                        borderColor: statusColors.map(color => color.replace('0.7', '1')),
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const code = context[0].label;
                                let title = `Status ${code}`;
                                
                                if (code >= 200 && code < 300) {
                                    title += ' (Success)';
                                } else if (code >= 300 && code < 400) {
                                    title += ' (Redirection)';
                                } else if (code >= 400 && code < 500) {
                                    title += ' (Client Error)';
                                } else if (code >= 500) {
                                    title += ' (Server Error)';
                                }
                                
                                return title;
                            },
                            label: function(context) {
                                return context.raw + ' requests';
                            }
                        }
                    }
                }
            }
        }
    );
    
    // Endpoint Usage Chart
    const endpointUsageChart = new Chart(
        document.getElementById('endpointUsageChart').getContext('2d'),
        {
            type: 'pie',
            data: {
                labels: endpointLabels,
                datasets: [
                    {
                        data: endpointValues,
                        backgroundColor: generateColors(endpointLabels.length),
                        borderColor: generateColors(endpointLabels.length, 1),
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    );
    
    // Aggregator Usage Chart
    const aggregatorUsageChart = new Chart(
        document.getElementById('aggregatorUsageChart').getContext('2d'),
        {
            type: 'doughnut',
            data: {
                labels: aggregatorLabels,
                datasets: [
                    {
                        data: aggregatorValues,
                        backgroundColor: generateColors(aggregatorLabels.length),
                        borderColor: generateColors(aggregatorLabels.length, 1),
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    );
});
</script>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>