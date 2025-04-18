<?php
/**
 * API Manager - API Endpoint Form (Add/Edit)
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

// Initialize variables
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$aggregatorId = isset($_GET['aggregator_id']) ? (int)$_GET['aggregator_id'] : null;
$isEdit = $id !== null;
$endpoint = [
    'aggregator_id' => $aggregatorId ?: '',
    'name' => '',
    'endpoint_url' => '/',
    'method' => 'POST',
    'request_body' => '{}',
    'headers' => '{}',
    'query_parameters' => '{}',
    'status' => STATUS_ACTIVE
];

// Set page title
$pageTitle = $isEdit ? 'Edit API Endpoint' : 'Add API Endpoint';

// Get endpoint data if editing
if ($isEdit) {
    $endpointData = getRecordById(TABLE_ENDPOINTS, $id);
    
    if (!$endpointData) {
        setMessage('API Endpoint not found.', 'error');
        header('Location: endpoints.php');
        exit;
    }
    
    $endpoint = $endpointData;
    $aggregatorId = $endpoint['aggregator_id'];
}

// Get aggregators for dropdown
$aggregators = getOptionsFromTable(TABLE_AGGREGATORS, 'id', 'name');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = cleanInput($_POST['name']);
    $aggregatorId = (int)$_POST['aggregator_id'];
    $endpointUrl = cleanInput($_POST['endpoint_url']);
    $method = cleanInput($_POST['method']);
    $requestBody = $_POST['request_body'];
    $headers = $_POST['headers'];
    $queryParameters = $_POST['query_parameters'];
    $status = isset($_POST['status']) ? STATUS_ACTIVE : STATUS_INACTIVE;
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    
    if (empty($aggregatorId)) {
        $errors[] = 'API Aggregator is required.';
    } elseif (!getRecordById(TABLE_AGGREGATORS, $aggregatorId)) {
        $errors[] = 'Selected API Aggregator does not exist.';
    }
    
    if (empty($endpointUrl)) {
        $errors[] = 'Endpoint URL is required.';
    }
    
    if (empty($method) || !in_array($method, HTTP_METHODS)) {
        $errors[] = 'Valid HTTP Method is required.';
    }
    
    // Validate JSON fields
    if (!empty($requestBody) && !isValidJson($requestBody)) {
        $errors[] = 'Request Body must be valid JSON.';
    }
    
    if (!empty($headers) && !isValidJson($headers)) {
        $errors[] = 'Headers must be valid JSON.';
    }
    
    if (!empty($queryParameters) && !isValidJson($queryParameters)) {
        $errors[] = 'Query Parameters must be valid JSON.';
    }
    
    if (empty($errors)) {
        // Prepare data
        $data = [
            'aggregator_id' => $aggregatorId,
            'name' => $name,
            'endpoint_url' => $endpointUrl,
            'method' => $method,
            'request_body' => $requestBody ?: null,
            'headers' => $headers ?: null,
            'query_parameters' => $queryParameters ?: null,
            'status' => $status
        ];
        
        try {
            if ($isEdit) {
                // Update existing endpoint
                if (updateRecord(TABLE_ENDPOINTS, $data, $id)) {
                    setMessage('API Endpoint updated successfully.', 'success');
                    header('Location: endpoints.php' . ($aggregatorId ? "?aggregator=$aggregatorId" : ''));
                    exit;
                } else {
                    setMessage('Unable to update API Endpoint.', 'error');
                }
            } else {
                // Insert new endpoint
                if ($newId = insertRecord(TABLE_ENDPOINTS, $data)) {
                    setMessage('API Endpoint added successfully.', 'success');
                    header('Location: endpoints.php' . ($aggregatorId ? "?aggregator=$aggregatorId" : ''));
                    exit;
                } else {
                    setMessage('Unable to add API Endpoint.', 'error');
                }
            }
        } catch (Exception $e) {
            setMessage('Error: ' . $e->getMessage(), 'error');
        }
    } else {
        // Display errors
        $errorMessage = implode('<br>', $errors);
        setMessage($errorMessage, 'error');
        
        // Keep submitted data
        $endpoint = [
            'aggregator_id' => $aggregatorId,
            'name' => $name,
            'endpoint_url' => $endpointUrl,
            'method' => $method,
            'request_body' => $requestBody,
            'headers' => $headers,
            'query_parameters' => $queryParameters,
            'status' => $status
        ];
    }
}

// Get aggregator info if aggregator_id is set
$aggregatorInfo = null;
if ($aggregatorId) {
    $aggregatorInfo = getRecordById(TABLE_AGGREGATORS, $aggregatorId);
}

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Endpoint Form Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= $pageTitle ?></h1>
        <p class="text-gray-600"><?= $isEdit ? 'Update existing API endpoint' : 'Create a new API endpoint' ?></p>
    </div>
    
    <a href="<?= $aggregatorId ? "/endpoints.php?aggregator=$aggregatorId" : '/endpoints.php' ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i> Back to List
    </a>
</div>

<?php displayMessage(); ?>

<?php if ($aggregatorInfo): ?>
<div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-md">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-indigo-600"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-indigo-800">Selected Aggregator</h3>
            <div class="mt-2 text-sm text-indigo-700">
                <p>Creating endpoint for: <strong><?= htmlspecialchars($aggregatorInfo['name']) ?></strong></p>
                <p class="mt-1 text-xs">Base URL: <?= htmlspecialchars($aggregatorInfo['api_base_url']) ?></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Form -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <form action="" method="POST" class="needs-validation" novalidate>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="form-label">Endpoint Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" class="form-input" value="<?= htmlspecialchars($endpoint['name']) ?>" required>
                        </div>
                        
                        <!-- API Aggregator -->
                        <div>
                            <label for="aggregator_id" class="form-label">API Aggregator <span class="text-red-500">*</span></label>
                            <select id="aggregator_id" name="aggregator_id" class="form-input" required <?= $aggregatorId && !$isEdit ? 'disabled' : '' ?>>
                                <option value="">Select API Aggregator</option>
                                <?php foreach ($aggregators as $id => $name): ?>
                                    <option value="<?= $id ?>" <?= ($endpoint['aggregator_id'] == $id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($aggregatorId && !$isEdit): ?>
                                <input type="hidden" name="aggregator_id" value="<?= $aggregatorId ?>">
                            <?php endif; ?>
                        </div>
                        
                        <!-- HTTP Method -->
                        <div>
                            <label for="method" class="form-label">HTTP Method <span class="text-red-500">*</span></label>
                            <select id="method" name="method" class="form-input" required>
                                <?php foreach (HTTP_METHODS as $httpMethod): ?>
                                    <option value="<?= $httpMethod ?>" <?= ($endpoint['method'] == $httpMethod) ? 'selected' : '' ?>>
                                        <?= $httpMethod ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Endpoint URL -->
                        <div>
                            <label for="endpoint_url" class="form-label">Endpoint URL <span class="text-red-500">*</span></label>
                            <input type="text" id="endpoint_url" name="endpoint_url" class="form-input" value="<?= htmlspecialchars($endpoint['endpoint_url']) ?>" required>
                            <p class="text-sm text-gray-500 mt-1">Relative to the API Base URL (e.g., "/auth/login")</p>
                        </div>
                        
                        <!-- Status -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="status" name="status" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" <?= $endpoint['status'] == STATUS_ACTIVE ? 'checked' : '' ?>>
                                <label for="status" class="ml-2 block text-sm text-gray-900">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Request Body -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Request Body (JSON)</h2>
                    <input type="hidden" id="request_body-input" name="request_body" value="<?= htmlspecialchars($endpoint['request_body']) ?>">
                    <div id="request_body-editor" class="json-editor" data-field="request_body" style="height: <?= JSON_EDITOR_HEIGHT ?>;"></div>
                    <p class="text-sm text-gray-500 mt-2">Enter the JSON request body template. Use {{variable_name}} for placeholders.</p>
                </div>
                
                <!-- Headers -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Headers (JSON)</h2>
                    <input type="hidden" id="headers-input" name="headers" value="<?= htmlspecialchars($endpoint['headers']) ?>">
                    <div id="headers-editor" class="json-editor" data-field="headers" style="height: <?= JSON_EDITOR_HEIGHT ?>;"></div>
                    <p class="text-sm text-gray-500 mt-2">Enter headers as JSON key-value pairs. Use {{variable_name}} for placeholders.</p>
                </div>
                
                <!-- Query Parameters -->
                <div>
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Query Parameters (JSON)</h2>
                    <input type="hidden" id="query_parameters-input" name="query_parameters" value="<?= htmlspecialchars($endpoint['query_parameters']) ?>">
                    <div id="query_parameters-editor" class="json-editor" data-field="query_parameters" style="height: <?= JSON_EDITOR_HEIGHT ?>;"></div>
                    <p class="text-sm text-gray-500 mt-2">Enter query parameters as JSON key-value pairs. Use {{variable_name}} for placeholders.</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 text-right">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-2"></i>
                <?= $isEdit ? 'Update Endpoint' : 'Add Endpoint' ?>
            </button>
        </div>
    </form>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
