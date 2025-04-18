<?php
/**
 * API Manager - API Request Templates List
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
$currentPage = 'templates';
$pageTitle = 'API Request Templates';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        if (deleteRecord(TABLE_TEMPLATES, $id)) {
            setMessage('API Request Template deleted successfully.', 'success');
        } else {
            setMessage('Unable to delete API Request Template.', 'error');
        }
    } catch (Exception $e) {
        setMessage('Error: ' . $e->getMessage(), 'error');
    }
    
    // Redirect to remove the delete parameter
    header('Location: templates.php');
    exit;
}

// Get current page for pagination
$page = getCurrentPage();

// Get search term if provided
$searchTerm = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$method = isset($_GET['method']) ? cleanInput($_GET['method']) : '';

// Prepare WHERE clause and parameters for search
$where = [];
$params = [];

if (!empty($searchTerm)) {
    $where[] = "(name LIKE :search OR url_pattern LIKE :search)";
    $params[':search'] = "%$searchTerm%";
}

if (!empty($method)) {
    $where[] = "method = :method";
    $params[':method'] = $method;
}

$whereClause = !empty($where) ? implode(' AND ', $where) : null;

// Get total records and calculate total pages
$totalRecords = getTotalRecords(TABLE_TEMPLATES, $whereClause, $params);
$totalPages = ceil($totalRecords / ITEMS_PER_PAGE);

// Get templates with pagination
$templates = getAllRecords(TABLE_TEMPLATES, $page, ITEMS_PER_PAGE, $whereClause, $params, 'name ASC');

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Templates Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= $pageTitle ?></h1>
        <p class="text-gray-600">Manage reusable API request templates</p>
    </div>
    
    <a href="/templates_form.php" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Add New
    </a>
</div>

<?php displayMessage(); ?>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
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
                <input type="text" name="search" id="search" class="form-input pl-10" placeholder="Search by name or URL pattern..." value="<?= htmlspecialchars($searchTerm) ?>">
            </div>
        </div>
        
        <div class="flex items-end gap-2">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            
            <?php if (!empty($searchTerm) || !empty($method)): ?>
                <a href="/templates.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Templates List -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (count($templates) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL Pattern</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($templates as $template): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($template['name']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($template['url_pattern']) ?>">
                                    <?= htmlspecialchars(truncateText($template['url_pattern'], 50)) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($template['method']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= formatDate($template['created_at'], 'M d, Y') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="#" class="text-blue-600 hover:text-blue-900" title="View Details" onclick="toggleJsonViewer('json-viewer-<?= $template['id'] ?>'); return false;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/templates_form.php?id=<?= $template['id'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/templates.php?delete=<?= $template['id'] ?>" class="text-red-600 hover:text-red-900 delete-confirm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                
                                <!-- JSON Details Viewer (Hidden by default) -->
                                <div id="json-viewer-<?= $template['id'] ?>" class="hidden mt-4 text-left bg-gray-50 p-3 rounded-md border border-gray-200">
                                    <div class="mb-2">
                                        <h4 class="text-sm font-medium text-gray-700">Request Body:</h4>
                                        <?php if (!empty($template['request_body'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($template['request_body']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No request body defined</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <h4 class="text-sm font-medium text-gray-700">Headers:</h4>
                                        <?php if (!empty($template['default_headers'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($template['default_headers']) ?></pre>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500">No headers defined</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Query Parameters:</h4>
                                        <?php if (!empty($template['query_parameters'])): ?>
                                            <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><?= formatJson($template['query_parameters']) ?></pre>
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
            $urlPattern = 'templates.php?page={page}';
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
            <?php if (!empty($searchTerm) || !empty($method)): ?>
                <p class="text-gray-500 mb-4">No templates found matching your criteria.</p>
                <a href="/templates.php" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Clear Filters
                </a>
            <?php else: ?>
                <p class="text-gray-500 mb-4">No API request templates have been added yet.</p>
                <a href="/templates_form.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Add New Template
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
