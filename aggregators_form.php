<?php
/**
 * API Manager - API Aggregator Form (Add/Edit)
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

// Initialize variables
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $id !== null;
$aggregator = [
    'name' => '',
    'api_base_url' => '',
    'agent_code' => '',
    'agent_token' => '',
    'api_version' => 'v1',
    'status' => STATUS_ACTIVE
];

// Set page title
$pageTitle = $isEdit ? 'Edit API Aggregator' : 'Add API Aggregator';

// Get aggregator data if editing
if ($isEdit) {
    $aggregatorData = getRecordById(TABLE_AGGREGATORS, $id);
    
    if (!$aggregatorData) {
        setMessage('API Aggregator not found.', 'error');
        header('Location: aggregators.php');
        exit;
    }
    
    $aggregator = $aggregatorData;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = cleanInput($_POST['name']);
    $apiBaseUrl = cleanInput($_POST['api_base_url']);
    $agentCode = cleanInput($_POST['agent_code']);
    $agentToken = cleanInput($_POST['agent_token']);
    $apiVersion = cleanInput($_POST['api_version']);
    $status = isset($_POST['status']) ? STATUS_ACTIVE : STATUS_INACTIVE;
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    
    if (empty($apiBaseUrl)) {
        $errors[] = 'API Base URL is required.';
    } elseif (!filter_var($apiBaseUrl, FILTER_VALIDATE_URL)) {
        $errors[] = 'API Base URL must be a valid URL.';
    }
    
    if (empty($agentCode)) {
        $errors[] = 'Agent Code is required.';
    }
    
    if (empty($agentToken)) {
        $errors[] = 'Agent Token is required.';
    }
    
    // Check if name is unique
    if (recordExists(TABLE_AGGREGATORS, 'name', $name, 'id', $id)) {
        $errors[] = 'An aggregator with this name already exists.';
    }
    
    if (empty($errors)) {
        // Prepare data
        $data = [
            'name' => $name,
            'api_base_url' => $apiBaseUrl,
            'agent_code' => $agentCode,
            'agent_token' => $agentToken,
            'api_version' => $apiVersion,
            'status' => $status
        ];
        
        try {
            if ($isEdit) {
                // Update existing aggregator
                if (updateRecord(TABLE_AGGREGATORS, $data, $id)) {
                    setMessage('API Aggregator updated successfully.', 'success');
                    header('Location: aggregators.php');
                    exit;
                } else {
                    setMessage('Unable to update API Aggregator.', 'error');
                }
            } else {
                // Insert new aggregator
                if ($newId = insertRecord(TABLE_AGGREGATORS, $data)) {
                    setMessage('API Aggregator added successfully.', 'success');
                    header('Location: aggregators.php');
                    exit;
                } else {
                    setMessage('Unable to add API Aggregator.', 'error');
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
        $aggregator = [
            'name' => $name,
            'api_base_url' => $apiBaseUrl,
            'agent_code' => $agentCode,
            'agent_token' => $agentToken,
            'api_version' => $apiVersion,
            'status' => $status
        ];
    }
}

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Aggregator Form Content -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= $pageTitle ?></h1>
        <p class="text-gray-600"><?= $isEdit ? 'Update existing API aggregator' : 'Create a new API aggregator service' ?></p>
    </div>
    
    <a href="/aggregators.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i> Back to List
    </a>
</div>

<?php displayMessage(); ?>

<!-- Form -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <form action="" method="POST" class="needs-validation" novalidate>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="form-label">Aggregator Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" value="<?= htmlspecialchars($aggregator['name']) ?>" required>
                </div>
                
                <!-- API Version -->
                <div>
                    <label for="api_version" class="form-label">API Version</label>
                    <input type="text" id="api_version" name="api_version" class="form-input" value="<?= htmlspecialchars($aggregator['api_version']) ?>" placeholder="e.g. v1">
                </div>
                
                <!-- API Base URL -->
                <div class="md:col-span-2">
                    <label for="api_base_url" class="form-label">API Base URL <span class="text-red-500">*</span></label>
                    <input type="url" id="api_base_url" name="api_base_url" class="form-input" value="<?= htmlspecialchars($aggregator['api_base_url']) ?>" required placeholder="https://api.example.com/">
                    <p class="text-sm text-gray-500 mt-1">The base URL for the API service (including trailing slash if needed)</p>
                </div>
                
                <!-- Agent Code -->
                <div>
                    <label for="agent_code" class="form-label">Agent Code <span class="text-red-500">*</span></label>
                    <input type="text" id="agent_code" name="agent_code" class="form-input" value="<?= htmlspecialchars($aggregator['agent_code']) ?>" required>
                </div>
                
                <!-- Agent Token -->
                <div>
                    <label for="agent_token" class="form-label">Agent Token <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="agent_token" name="agent_token" class="form-input pr-10" value="<?= htmlspecialchars($aggregator['agent_token']) ?>" required>
                        <button type="button" id="toggle-token" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Status -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="status" name="status" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" <?= $aggregator['status'] == STATUS_ACTIVE ? 'checked' : '' ?>>
                        <label for="status" class="ml-2 block text-sm text-gray-900">Active</label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 text-right">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-2"></i>
                <?= $isEdit ? 'Update Aggregator' : 'Add Aggregator' ?>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButton = document.getElementById('toggle-token');
    const tokenInput = document.getElementById('agent_token');
    
    if (toggleButton && tokenInput) {
        toggleButton.addEventListener('click', function() {
            const type = tokenInput.getAttribute('type') === 'password' ? 'text' : 'password';
            tokenInput.setAttribute('type', type);
            
            // Toggle icon
            const icon = toggleButton.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
});
</script>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
