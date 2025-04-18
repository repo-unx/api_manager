<?php
/**
 * API Manager - API Request Logs Viewer
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
$currentPage = 'logs';
$pageTitle = 'API Request Logs';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        if (deleteRecord(TABLE_LOGS, $id)) {
            setMessage('Log entry deleted successfully.', 'success');
        } else {
            setMessage('Unable to delete log entry.', 'error');
        }
    } catch (Exception $e) {
        setMessage('Error: ' . $e->getMessage(), 'error');
    }
    
    // Redirect to remove the delete parameter
    header('Location: logs.php');
    exit;
}

// Handle clear logs request
if (isset($_GET['clear']) && $_GET['clear'] === 'all') {
    try {
        $db = getDbConnection();
        $stmt = $db->prepare("TRUNCATE TABLE " . TABLE_LOGS);
        $stmt->execute();
        setMessage('All logs cleared successfully.', 'success');
    } catch (Exception $e) {
        setMessage('Error clearing logs: ' . $e->getMessage(), 'error');
    }
    
    header('Location: logs.php');
    exit;
}

// Get current page for pagination
$page = getCurrentPage();

// Get filter parameters
$aggregatorId = isset($_GET['aggregator']) && is_numeric($_GET['aggregator']) ? (int)$_GET['aggregator'] : null;
$endpointId = isset($_GET['endpoint']) && is_numeric($_GET['endpoint']) ? (int)$_GET['endpoint'] : null;
$method = isset($_GET['method']) ? cleanInput($_GET['method']) : '';
$statusCode = isset($_GET['status_code']) ? cleanInput($_GET['status_code']) : '';
$startDate = isset($_GET['start_date']) ? cleanInput($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? cleanInput($_GET['end_date']) : '';

// Prepare WHERE clause and parameters for filtering
$where = [];
$params = [];

if ($aggregatorId) {
    $where[] = "aggregator_id = :aggregator_id";
    $params[':aggregator_id'] = $aggregatorId;
}

if ($endpointId) {
    $where[] = "endpoint_id = :endpoint_id";
    $params[':endpoint_id'] = $endpointId;
}

if (!empty($method)) {
    $where[] = "request_method = :method";
    $params[':method'] = $method;
}

if (!empty($statusCode)) {
    if ($statusCode === 'success') {
        $where[] = "response_code >= 200 AND response_code < 300";
    } elseif ($statusCode === 'error') {
        $where[] = "(response_code < 200 OR response_code >= 300)";
    } else {
        $where[] = "response_code = :status_code";
        $params[':status_code'] = (int)$statusCode;
    }
}

if (!empty($startDate)) {
    $where[] = "created_at >= :start_date";
    $params[':start_date'] = $startDate . ' 00:00:00';
}

if (!empty($endDate)) {
    $where[] = "created_at <= :end_date";
    $params[':end_date'] = $endDate . ' 23:59:59';
}

$whereClause = !empty($where) ? implode(' AND ', $where) : null;

// Get total records and calculate total pages
$totalRecords = getTotalRecords(TABLE_LOGS, $whereClause, $params);
$totalPages = ceil($totalRecords / ITEMS_PER_PAGE);

// Get logs with pagination
$logs = getAllRecords(TABLE_LOGS, $page, ITEMS_PER_PAGE, $whereClause, $params, 'created_at DESC');

// Get aggregators for dropdown
$dbDriver = getenv('DB_DRIVER');
if ($dbDriver == 'pgsql') {
    $aggregators = getOptionsFromTable(TABLE_AGGREGATORS, 'id', 'name');
} else {
    $aggregators = getOptionsFromTable(TABLE_AGGREGATORS, 'id', 'name');
}

// Get endpoints for dropdown (filtered by aggregator if selected)
$endpointWhere = $aggregatorId ? "aggregator_id = :aggregator_id" : null;
$endpointParams = $aggregatorId ? [':aggregator_id' => $aggregatorId] : [];
$endpoints = getOptionsFromTable(TABLE_ENDPOINTS, 'id', 'name', $endpointWhere, $endpointParams);

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Logs Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= $pageTitle ?></h1>
        <p class="text-gray-600">View and analyze API request logs</p>
    </div>
    
    <?php if ($totalRecords > 0): ?>
    <div class="flex gap-2">
        <button id="clear-filters" class="btn btn-secondary" <?= (!$aggregatorId && !$endpointId && empty($method) && empty($statusCode) && empty($startDate) && empty($endDate)) ? 'disabled' : '' ?>>
            <i class="fas fa-filter-circle-xmark mr-2"></i> Clear Filters
        </button>
        <a href="/logs.php?clear=all" class="btn btn-danger delete-confirm" data-confirm-message="Are you sure you want to clear all logs? This action cannot be undone.">
            <i class="fas fa-trash-alt mr-2"></i> Clear All Logs
        </a>
    </div>
    <?php endif; ?>
</div>

<?php displayMessage(); ?>

<!-- Filter Panel -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="" method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Aggregator Filter -->
        <div>
            <label for="aggregator" class="form-label">Aggregator</label>
            <select name="aggregator" id="aggregator" class="form-input">
                <option value="">All Aggregators</option>
                <?php foreach ($aggregators as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($aggregatorId == $id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Endpoint Filter -->
        <div>
            <label for="endpoint" class="form-label">Endpoint</label>
            <select name="endpoint" id="endpoint" class="form-input">
                <option value="">All Endpoints</option>
                <?php foreach ($endpoints as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($endpointId == $id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Method Filter -->
        <div>
            <label for="method" class="form-label">HTTP Method</label>
            <select name="method" id="method" class="form-input">
                <option value="">All Methods</option>
                <?php foreach (HTTP_METHODS as $httpMethod): ?>
                    <option value="<?= $httpMethod ?>" <?= ($method == $httpMethod) ? 'selected' : '' ?>>
                        <?= $httpMethod ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Status Code Filter -->
        <div>
            <label for="status_code" class="form-label">Status</label>
            <select name="status_code" id="status_code" class="form-input">
                <option value="">All Status Codes</option>
                <option value="success" <?= ($statusCode === 'success') ? 'selected' : '' ?>>Success (2xx)</option>
                <option value="error" <?= ($statusCode === 'error') ? 'selected' : '' ?>>Error (non-2xx)</option>
                <option value="200" <?= ($statusCode === '200') ? 'selected' : '' ?>>200 OK</option>
                <option value="201" <?= ($statusCode === '201') ? 'selected' : '' ?>>201 Created</option>
                <option value="400" <?= ($statusCode === '400') ? 'selected' : '' ?>>400 Bad Request</option>
                <option value="401" <?= ($statusCode === '401') ? 'selected' : '' ?>>401 Unauthorized</option>
                <option value="403" <?= ($statusCode === '403') ? 'selected' : '' ?>>403 Forbidden</option>
                <option value="404" <?= ($statusCode === '404') ? 'selected' : '' ?>>404 Not Found</option>
                <option value="500" <?= ($statusCode === '500') ? 'selected' : '' ?>>500 Server Error</option>
            </select>
        </div>
        
        <!-- Date Range Filters -->
        <div>
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-input" value="<?= htmlspecialchars($startDate) ?>">
        </div>
        
        <div>
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-input" value="<?= htmlspecialchars($endDate) ?>">
        </div>
        
        <!-- Filter Controls -->
        <div class="md:col-span-3 lg:col-span-6 flex justify-end gap-2">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter mr-2"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Logs List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (count($logs) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aggregator/Endpoint</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($logs as $log): 
                        // Get related data
                        $aggregator = getRecordById(TABLE_AGGREGATORS, $log['aggregator_id']);
                        $endpoint = getRecordById(TABLE_ENDPOINTS, $log['endpoint_id']);
                        
                        // Determine status color
                        $statusClass = '';
                        if ($log['response_code']) {
                            $statusClass = ($log['response_code'] >= 200 && $log['response_code'] < 300) 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-red-100 text-red-800';
                        } else {
                            $statusClass = 'bg-gray-100 text-gray-800';
                        }
                    ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= formatDate($log['created_at']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= $aggregator ? htmlspecialchars($aggregator['name']) : 'Unknown' ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= $endpoint ? htmlspecialchars($endpoint['name']) : 'Unknown' ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($log['request_method']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($log['request_url']) ?>">
                                    <?= htmlspecialchars(truncateText($log['request_url'], 50)) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= $log['response_code'] ?: 'N/A' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="#" class="text-blue-600 hover:text-blue-900" title="View Details" onclick="toggleJsonViewer('json-viewer-<?= $log['id'] ?>'); return false;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/logs.php?delete=<?= $log['id'] ?>" class="text-red-600 hover:text-red-900 delete-confirm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                
                                <!-- JSON Details Viewer (Hidden by default) -->
                                <div id="json-viewer-<?= $log['id'] ?>" class="hidden mt-4 text-left bg-gray-50 p-3 rounded-md border border-gray-200">
                                    <div class="mb-2">
                                        <h4 class="text-sm font-medium text-gray-700">Request URL:</h4>
                                        <div class="text-xs bg-gray-100 p-2 rounded overflow-x-auto">
                                            <?= htmlspecialchars($log['request_url']) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <h4 class="text-sm font-medium text-gray-700">Request Body:</h4>
                                        <?php if (!empty($log['request_body'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($log['request_body']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No request body</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Response Body:</h4>
                                        <?php if (!empty($log['response_body'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($log['response_body']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No response body</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php
        if ($totalPages > 1) {
            $urlPattern = 'logs.php?page={page}';
            if ($aggregatorId) {
                $urlPattern .= '&aggregator=' . $aggregatorId;
            }
            if ($endpointId) {
                $urlPattern .= '&endpoint=' . $endpointId;
            }
            if (!empty($method)) {
                $urlPattern .= '&method=' . urlencode($method);
            }
            if (!empty($statusCode)) {
                $urlPattern .= '&status_code=' . urlencode($statusCode);
            }
            if (!empty($startDate)) {
                $urlPattern .= '&start_date=' . urlencode($startDate);
            }
            if (!empty($endDate)) {
                $urlPattern .= '&end_date=' . urlencode($endDate);
            }
            echo displayPagination($page, $totalPages, $urlPattern);
        }
        ?>
        
    <?php else: ?>
        <div class="py-8 text-center">
            <?php if (!empty($method) || !empty($statusCode) || $aggregatorId || $endpointId || !empty($startDate) || !empty($endDate)): ?>
                <p class="text-gray-500 mb-4">No logs found matching your criteria.</p>
                <a href="/logs.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear Filters
                </a>
            <?php else: ?>
                <p class="text-gray-500 mb-4">No API request logs have been recorded yet.</p>
                <p class="text-sm text-gray-500">Logs will appear here when API requests are made through the system.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle aggregator change to filter endpoints
    const aggregatorSelect = document.getElementById('aggregator');
    const endpointSelect = document.getElementById('endpoint');
    const clearFiltersBtn = document.getElementById('clear-filters');
    
    if (aggregatorSelect && endpointSelect) {
        aggregatorSelect.addEventListener('change', function() {
            const aggregatorId = this.value;
            
            // Clear the endpoint select
            endpointSelect.innerHTML = '<option value="">All Endpoints</option>';
            
            if (aggregatorId) {
                // Fetch endpoints for this aggregator using fetch API
                fetch(`/endpoints.php?aggregator=${aggregatorId}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        // Add options
                        data.forEach(endpoint => {
                            const option = document.createElement('option');
                            option.value = endpoint.id;
                            option.textContent = endpoint.name;
                            endpointSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching endpoints:', error);
                    });
            }
        });
    }
    
    // Clear filters button
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            window.location.href = '/logs.php';
        });
    }
    
    // Date range validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const filterForm = document.getElementById('filter-form');
    
    if (filterForm && startDateInput && endDateInput) {
        filterForm.addEventListener('submit', function(e) {
            if (startDateInput.value && endDateInput.value) {
                if (new Date(startDateInput.value) > new Date(endDateInput.value)) {
                    e.preventDefault();
                    alert('Start date cannot be after end date');
                }
            }
        });
    }
});
</script>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
