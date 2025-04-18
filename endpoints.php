<?php
/**
 * API Manager - API Endpoints List
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
$currentPage = 'endpoints';
$pageTitle = 'API Endpoints';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        if (deleteRecord(TABLE_ENDPOINTS, $id)) {
            setMessage('API Endpoint deleted successfully.', 'success');
        } else {
            setMessage('Unable to delete API Endpoint.', 'error');
        }
    } catch (Exception $e) {
        setMessage('Error: ' . $e->getMessage(), 'error');
    }
    
    // Redirect to remove the delete parameter
    $redirectUrl = 'endpoints.php';
    if (isset($_GET['aggregator'])) {
        $redirectUrl .= '?aggregator=' . urlencode($_GET['aggregator']);
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// Handle status toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $endpoint = getRecordById(TABLE_ENDPOINTS, $id);
    
    if ($endpoint) {
        $newStatus = $endpoint['status'] == STATUS_ACTIVE ? STATUS_INACTIVE : STATUS_ACTIVE;
        $statusText = $newStatus == STATUS_ACTIVE ? 'activated' : 'deactivated';
        
        if (updateRecord(TABLE_ENDPOINTS, ['status' => $newStatus], $id)) {
            setMessage("API Endpoint $statusText successfully.", 'success');
        } else {
            setMessage('Unable to update API Endpoint status.', 'error');
        }
    } else {
        setMessage('API Endpoint not found.', 'error');
    }
    
    // Redirect to remove the toggle parameter
    $redirectUrl = 'endpoints.php';
    if (isset($_GET['aggregator'])) {
        $redirectUrl .= '?aggregator=' . urlencode($_GET['aggregator']);
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// Get current page for pagination
$page = getCurrentPage();

// Get filter parameters
$aggregatorId = isset($_GET['aggregator']) && is_numeric($_GET['aggregator']) ? (int)$_GET['aggregator'] : null;
$searchTerm = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$method = isset($_GET['method']) ? cleanInput($_GET['method']) : '';

// Prepare WHERE clause and parameters for filtering
$where = [];
$params = [];

if ($aggregatorId) {
    $where[] = "aggregator_id = :aggregator_id";
    $params[':aggregator_id'] = $aggregatorId;
    
    // Get aggregator info for display
    $aggregator = getRecordById(TABLE_AGGREGATORS, $aggregatorId);
    if ($aggregator) {
        $pageTitle .= ' - ' . htmlspecialchars($aggregator['name']);
    }
}

if (!empty($searchTerm)) {
    $where[] = "(name LIKE :search OR endpoint_url LIKE :search)";
    $params[':search'] = "%$searchTerm%";
}

if (!empty($method)) {
    $where[] = "method = :method";
    $params[':method'] = $method;
}

$whereClause = !empty($where) ? implode(' AND ', $where) : null;

// Get total records and calculate total pages
$totalRecords = getTotalRecords(TABLE_ENDPOINTS, $whereClause, $params);
$totalPages = ceil($totalRecords / ITEMS_PER_PAGE);

// Get endpoints with pagination
$endpoints = getAllRecords(TABLE_ENDPOINTS, $page, ITEMS_PER_PAGE, $whereClause, $params, 'name ASC');

// Get all aggregators for dropdown
$aggregators = getOptionsFromTable(TABLE_AGGREGATORS, 'id', 'name', 'status = ' . STATUS_ACTIVE);

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Endpoints Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= $pageTitle ?></h1>
        <p class="text-gray-600">Manage API endpoints for your aggregators</p>
    </div>
    
    <a href="/endpoints_form.php<?= $aggregatorId ? '?aggregator_id=' . $aggregatorId : '' ?>" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add New
    </a>
</div>

<?php displayMessage(); ?>

<?php if ($aggregatorId && $aggregator): ?>
<div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-md">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-indigo-600"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-indigo-800">Filtered by Aggregator</h3>
            <div class="mt-2 text-sm text-indigo-700">
                <p>Showing endpoints for: <strong><?= htmlspecialchars($aggregator['name']) ?></strong></p>
                <p class="mt-1 text-xs">Base URL: <?= htmlspecialchars($aggregator['api_base_url']) ?></p>
            </div>
            <div class="mt-3">
                <a href="/endpoints.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-times-circle mr-1"></i> Clear filter
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
        <?php if ($aggregatorId): ?>
            <input type="hidden" name="aggregator" value="<?= $aggregatorId ?>">
        <?php else: ?>
            <div class="md:w-1/4">
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
        <?php endif; ?>
        
        <div class="md:w-1/4">
            <label for="method" class="form-label">HTTP Method</label>
            <select name="method" id="method" class="form-input">
                <option value="">Any Method</option>
                <?php foreach (HTTP_METHODS as $httpMethod): ?>
                    <option value="<?= $httpMethod ?>" <?= ($method == $httpMethod) ? 'selected' : '' ?>>
                        <?= $httpMethod ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="flex-grow">
            <label for="search" class="form-label">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" id="search" class="form-input pl-10" placeholder="Search by name or URL..." value="<?= htmlspecialchars($searchTerm) ?>">
            </div>
        </div>
        
        <div class="flex items-end gap-2">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            <?php if (!empty($searchTerm) || !empty($method) || $aggregatorId): ?>
                <a href="/endpoints.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Endpoints List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (count($endpoints) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <?php if (!$aggregatorId): ?>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aggregator</th>
                        <?php endif; ?>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint URL</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($endpoints as $endpoint): 
                        // Get aggregator info if not filtered by aggregator
                        if (!$aggregatorId) {
                            $endpointAggregator = getRecordById(TABLE_AGGREGATORS, $endpoint['aggregator_id']);
                        }
                    ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($endpoint['name']) ?></div>
                            </td>
                            <?php if (!$aggregatorId): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        <?php if ($endpointAggregator): ?>
                                            <a href="/endpoints.php?aggregator=<?= $endpointAggregator['id'] ?>" class="text-indigo-600 hover:text-indigo-900">
                                                <?= htmlspecialchars($endpointAggregator['name']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-red-500">Unknown</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($endpoint['endpoint_url']) ?>">
                                    <?= htmlspecialchars(truncateText($endpoint['endpoint_url'], 50)) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($endpoint['method']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= getStatusBadge($endpoint['status']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="/endpoints.php?toggle=<?= $endpoint['id'] ?><?= $aggregatorId ? '&aggregator=' . $aggregatorId : '' ?>" class="text-indigo-600 hover:text-indigo-900" title="<?= $endpoint['status'] == STATUS_ACTIVE ? 'Deactivate' : 'Activate' ?>">
                                        <i class="fas fa-<?= $endpoint['status'] == STATUS_ACTIVE ? 'toggle-on' : 'toggle-off' ?>"></i>
                                    </a>
                                    <a href="#" class="text-blue-600 hover:text-blue-900" title="View Details" onclick="toggleJsonViewer('json-viewer-<?= $endpoint['id'] ?>'); return false;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/endpoints_form.php?id=<?= $endpoint['id'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/endpoints.php?delete=<?= $endpoint['id'] ?><?= $aggregatorId ? '&aggregator=' . $aggregatorId : '' ?>" class="text-red-600 hover:text-red-900 delete-confirm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                
                                <!-- JSON Details Viewer (Hidden by default) -->
                                <div id="json-viewer-<?= $endpoint['id'] ?>" class="hidden mt-4 text-left bg-gray-50 p-3 rounded-md border border-gray-200">
                                    <div class="mb-2">
                                        <h4 class="text-sm font-medium text-gray-700">Request Body:</h4>
                                        <?php if (!empty($endpoint['request_body'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($endpoint['request_body']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No request body defined</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <h4 class="text-sm font-medium text-gray-700">Headers:</h4>
                                        <?php if (!empty($endpoint['headers'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($endpoint['headers']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No headers defined</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Query Parameters:</h4>
                                        <?php if (!empty($endpoint['query_parameters'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($endpoint['query_parameters']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No query parameters defined</p>
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
            $urlPattern = 'endpoints.php?page={page}';
            if ($aggregatorId) {
                $urlPattern .= '&aggregator=' . $aggregatorId;
            }
            if (!empty($searchTerm)) {
                $urlPattern .= '&search=' . urlencode($searchTerm);
            }
            if (!empty($method)) {
                $urlPattern .= '&method=' . urlencode($method);
            }
            echo displayPagination($page, $totalPages, $urlPattern);
        }
        ?>
        
    <?php else: ?>
        <div class="py-8 text-center">
            <?php if (!empty($searchTerm) || !empty($method) || $aggregatorId): ?>
                <p class="text-gray-500 mb-4">No endpoints found matching your criteria.</p>
                <a href="/endpoints.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear Filters
                </a>
            <?php else: ?>
                <p class="text-gray-500 mb-4">No API endpoints have been added yet.</p>
                <a href="/endpoints_form.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Add New Endpoint
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
