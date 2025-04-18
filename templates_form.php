<?php
/**
 * API Manager - API Request Template Form (Add/Edit)
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

// Initialize variables
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $id !== null;
$template = [
    'name' => '',
    'method' => 'GET',
    'url_pattern' => '',
    'default_headers' => '{}',
    'query_parameters' => '{}',
    'request_body' => '{}'
];

// Set page title
$pageTitle = $isEdit ? 'Edit Request Template' : 'Add Request Template';

// Get template data if editing
if ($isEdit) {
    $templateData = getRecordById(TABLE_TEMPLATES, $id);
    
    if (!$templateData) {
        setMessage('API Request Template not found.', 'error');
        header('Location: templates.php');
        exit;
    }
    
    $template = $templateData;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = cleanInput($_POST['name']);
    $method = cleanInput($_POST['method']);
    $urlPattern = cleanInput($_POST['url_pattern']);
    $defaultHeaders = $_POST['default_headers'];
    $queryParameters = $_POST['query_parameters'];
    $requestBody = $_POST['request_body'];
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Template name is required.';
    }
    
    if (empty($urlPattern)) {
        $errors[] = 'URL Pattern is required.';
    }
    
    if (empty($method) || !in_array($method, HTTP_METHODS)) {
        $errors[] = 'Valid HTTP Method is required.';
    }
    
    // Validate JSON fields
    if (!empty($defaultHeaders) && !isValidJson($defaultHeaders)) {
        $errors[] = 'Default Headers must be valid JSON.';
    }
    
    if (!empty($queryParameters) && !isValidJson($queryParameters)) {
        $errors[] = 'Query Parameters must be valid JSON.';
    }
    
    if (!empty($requestBody) && !isValidJson($requestBody)) {
        $errors[] = 'Request Body must be valid JSON.';
    }
    
    // Check if name is unique
    if (recordExists(TABLE_TEMPLATES, 'name', $name, 'id', $id)) {
        $errors[] = 'A template with this name already exists.';
    }
    
    if (empty($errors)) {
        // Prepare data
        $data = [
            'name' => $name,
            'method' => $method,
            'url_pattern' => $urlPattern,
            'default_headers' => $defaultHeaders ?: null,
            'query_parameters' => $queryParameters ?: null,
            'request_body' => $requestBody ?: null
        ];
        
        try {
            if ($isEdit) {
                // Update existing template
                if (updateRecord(TABLE_TEMPLATES, $data, $id)) {
                    setMessage('API Request Template updated successfully.', 'success');
                    header('Location: templates.php');
                    exit;
                } else {
                    setMessage('Unable to update API Request Template.', 'error');
                }
            } else {
                // Insert new template
                if ($newId = insertRecord(TABLE_TEMPLATES, $data)) {
                    setMessage('API Request Template added successfully.', 'success');
                    header('Location: templates.php');
                    exit;
                } else {
                    setMessage('Unable to add API Request Template.', 'error');
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
        $template = [
            'name' => $name,
            'method' => $method,
            'url_pattern' => $urlPattern,
            'default_headers' => $defaultHeaders,
            'query_parameters' => $queryParameters,
            'request_body' => $requestBody
        ];
    }
}

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Template Form Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= $pageTitle ?></h1>
        <p class="text-gray-600"><?= $isEdit ? 'Update existing API request template' : 'Create a new API request template' ?></p>
    </div>
    
    <a href="/templates.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i> Back to List
    </a>
</div>

<?php displayMessage(); ?>

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
                            <label for="name" class="form-label">Template Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" class="form-input" value="<?= htmlspecialchars($template['name']) ?>" required>
                        </div>
                        
                        <!-- HTTP Method -->
                        <div>
                            <label for="method" class="form-label">HTTP Method <span class="text-red-500">*</span></label>
                            <select id="method" name="method" class="form-input" required>
                                <?php foreach (HTTP_METHODS as $httpMethod): ?>
                                    <option value="<?= $httpMethod ?>" <?= ($template['method'] == $httpMethod) ? 'selected' : '' ?>>
                                        <?= $httpMethod ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- URL Pattern -->
                        <div class="md:col-span-2">
                            <label for="url_pattern" class="form-label">URL Pattern <span class="text-red-500">*</span></label>
                            <input type="text" id="url_pattern" name="url_pattern" class="form-input" value="<?= htmlspecialchars($template['url_pattern']) ?>" required>
                            <p class="text-sm text-gray-500 mt-1">The URL pattern with placeholders (e.g., "/api/{{resource}}/{{id}}")</p>
                        </div>
                    </div>
                </div>
                
                <!-- Default Headers -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Default Headers (JSON)</h2>
                    <input type="hidden" id="default_headers-input" name="default_headers" value="<?= htmlspecialchars($template['default_headers']) ?>">
                    <div id="default_headers-editor" class="json-editor" data-field="default_headers" style="height: <?= JSON_EDITOR_HEIGHT ?>;"></div>
                    <p class="text-sm text-gray-500 mt-2">Enter default headers as JSON key-value pairs. Use {{variable_name}} for placeholders.</p>
                </div>
                
                <!-- Query Parameters -->
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Query Parameters (JSON)</h2>
                    <input type="hidden" id="query_parameters-input" name="query_parameters" value="<?= htmlspecialchars($template['query_parameters']) ?>">
                    <div id="query_parameters-editor" class="json-editor" data-field="query_parameters" style="height: <?= JSON_EDITOR_HEIGHT ?>;"></div>
                    <p class="text-sm text-gray-500 mt-2">Enter query parameters as JSON key-value pairs. Use {{variable_name}} for placeholders.</p>
                </div>
                
                <!-- Request Body -->
                <div>
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Request Body (JSON)</h2>
                    <input type="hidden" id="request_body-input" name="request_body" value="<?= htmlspecialchars($template['request_body']) ?>">
                    <div id="request_body-editor" class="json-editor" data-field="request_body" style="height: <?= JSON_EDITOR_HEIGHT ?>;"></div>
                    <p class="text-sm text-gray-500 mt-2">Enter the JSON request body template. Use {{variable_name}} for placeholders.</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 text-right">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-2"></i>
                <?= $isEdit ? 'Update Template' : 'Add Template' ?>
            </button>
        </div>
    </form>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
