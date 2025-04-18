<?php
/**
 * API Manager - API Aggregators List
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
$currentPage = 'aggregators';
$pageTitle = 'API Aggregators';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        // Check if there are dependent endpoints
        $dependentEndpoints = getTotalRecords(TABLE_ENDPOINTS, 'aggregator_id = :id', [':id' => $id]);
        
        if ($dependentEndpoints > 0) {
            setMessage("Cannot delete: This aggregator has $dependentEndpoints dependent endpoints. Delete them first.", 'error');
        } else {
            if (deleteRecord(TABLE_AGGREGATORS, $id)) {
                setMessage('API Aggregator deleted successfully.', 'success');
            } else {
                setMessage('Unable to delete API Aggregator.', 'error');
            }
        }
    } catch (Exception $e) {
        setMessage('Error: ' . $e->getMessage(), 'error');
    }
    
    // Redirect to remove the delete parameter
    header('Location: aggregators.php');
    exit;
}

// Handle status toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $aggregator = getRecordById(TABLE_AGGREGATORS, $id);
    
    if ($aggregator) {
        $newStatus = $aggregator['status'] == STATUS_ACTIVE ? STATUS_INACTIVE : STATUS_ACTIVE;
        $statusText = $newStatus == STATUS_ACTIVE ? 'activated' : 'deactivated';
        
        if (updateRecord(TABLE_AGGREGATORS, ['status' => $newStatus], $id)) {
            setMessage("API Aggregator $statusText successfully.", 'success');
        } else {
            setMessage('Unable to update API Aggregator status.', 'error');
        }
    } else {
        setMessage('API Aggregator not found.', 'error');
    }
    
    // Redirect to remove the toggle parameter
    header('Location: aggregators.php');
    exit;
}

// Get current page for pagination
$page = getCurrentPage();

// Get search term if provided
$searchTerm = isset($_GET['search']) ? cleanInput($_GET['search']) : '';

// Prepare WHERE clause and parameters for search
$where = null;
$params = [];
if (!empty($searchTerm)) {
    $where = "(name LIKE :search OR api_base_url LIKE :search OR agent_code LIKE :search)";
    $params[':search'] = "%$searchTerm%";
}

// Get total records and calculate total pages
$totalRecords = getTotalRecords(TABLE_AGGREGATORS, $where, $params);
$totalPages = ceil($totalRecords / ITEMS_PER_PAGE);

// Get aggregators with pagination
$aggregators = getAllRecords(TABLE_AGGREGATORS, $page, ITEMS_PER_PAGE, $where, $params, 'name ASC');

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Aggregators Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">API Aggregators</h1>
        <p class="text-gray-600">Manage API aggregator services</p>
    </div>
    
    <a href="/aggregators_form.php" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add New
    </a>
</div>

<?php displayMessage(); ?>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-grow">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" id="search" class="form-input pl-10" placeholder="Search by name, URL or agent code..." value="<?= htmlspecialchars($searchTerm) ?>">
            </div>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-search mr-2"></i> Search
            </button>
            
            <?php if (!empty($searchTerm)): ?>
                <a href="/aggregators.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Aggregators List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (count($aggregators) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base URL</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API Version</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($aggregators as $aggregator): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($aggregator['name']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($aggregator['api_base_url']) ?>">
                                    <?= htmlspecialchars($aggregator['api_base_url']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($aggregator['agent_code']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($aggregator['api_version'] ?: 'Default') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= getStatusBadge($aggregator['status']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= formatDate($aggregator['created_at'], 'M d, Y') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="/aggregators.php?toggle=<?= $aggregator['id'] ?>" class="text-indigo-600 hover:text-indigo-900" title="<?= $aggregator['status'] == STATUS_ACTIVE ? 'Deactivate' : 'Activate' ?>">
                                        <i class="fas fa-<?= $aggregator['status'] == STATUS_ACTIVE ? 'toggle-on' : 'toggle-off' ?>"></i>
                                    </a>
                                    <a href="/endpoints.php?aggregator=<?= $aggregator['id'] ?>" class="text-blue-600 hover:text-blue-900" title="View Endpoints">
                                        <i class="fas fa-link"></i>
                                    </a>
                                    <a href="/aggregators_form.php?id=<?= $aggregator['id'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/aggregators.php?delete=<?= $aggregator['id'] ?>" class="text-red-600 hover:text-red-900 delete-confirm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
            $urlPattern = 'aggregators.php?page={page}' . (!empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '');
            echo displayPagination($page, $totalPages, $urlPattern);
        }
        ?>
        
    <?php else: ?>
        <div class="py-8 text-center">
            <?php if (!empty($searchTerm)): ?>
                <p class="text-gray-500 mb-4">No aggregators found matching "<?= htmlspecialchars($searchTerm) ?>".</p>
                <a href="/aggregators.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear Search
                </a>
            <?php else: ?>
                <p class="text-gray-500 mb-4">No API aggregators have been added yet.</p>
                <a href="/aggregators_form.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Add New Aggregator
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
