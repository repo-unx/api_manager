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
    
    <a href="/aggregators.php" class="btn btn-secondary flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to List
    </a>
</div>

<?php displayMessage(); ?>

<!-- Form -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-800">
            <?= $isEdit ? 'Edit Aggregator Details' : 'New Aggregator Details' ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?= $isEdit ? 'Update the information for this API aggregator' : 'Fill in the details to create a new API aggregator' ?>
        </p>
    </div>

    <form action="" method="POST" class="needs-validation" novalidate>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name Section -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <h3 class="text-md font-medium text-gray-700 mb-3">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="form-label flex items-center">
                                <span>Aggregator Name</span>
                                <span class="text-red-500 ml-1">*</span>
                                <span class="ml-2 text-xs text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded">Required</span>
                            </label>
                            <input type="text" id="name" name="name" class="form-input" 
                                value="<?= htmlspecialchars($aggregator['name']) ?>" 
                                placeholder="e.g. Payment Gateway API"
                                required>
                            <p class="text-xs text-gray-500 mt-1">A descriptive name for this API provider</p>
                        </div>
                        
                        <!-- API Version -->
                        <div>
                            <label for="api_version" class="form-label flex items-center">
                                <span>API Version</span>
                                <span class="ml-2 text-xs text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded">Optional</span>
                            </label>
                            <input type="text" id="api_version" name="api_version" class="form-input" 
                                value="<?= htmlspecialchars($aggregator['api_version']) ?>" 
                                placeholder="e.g. v1 or 2.0">
                            <p class="text-xs text-gray-500 mt-1">Version of the API (if applicable)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Connection Details Section -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <h3 class="text-md font-medium text-gray-700 mb-3">Connection Details</h3>
                    
                    <!-- API Base URL -->
                    <div class="mb-4">
                        <label for="api_base_url" class="form-label flex items-center">
                            <span>API Base URL</span>
                            <span class="text-red-500 ml-1">*</span>
                            <span class="ml-2 text-xs text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded">Required</span>
                        </label>
                        <div class="flex">
                            <div class="bg-gray-100 flex items-center px-3 border border-r-0 border-gray-300 rounded-l-md text-gray-500">
                                <i class="fas fa-link"></i>
                            </div>
                            <input type="url" id="api_base_url" name="api_base_url" 
                                class="form-input rounded-l-none w-full" 
                                value="<?= htmlspecialchars($aggregator['api_base_url']) ?>" 
                                placeholder="https://api.example.com/"
                                required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">The base URL for all API endpoints (including trailing slash if needed)</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Agent Code -->
                        <div>
                            <label for="agent_code" class="form-label flex items-center">
                                <span>Agent Code</span>
                                <span class="text-red-500 ml-1">*</span>
                                <span class="ml-2 text-xs text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded">Required</span>
                            </label>
                            <div class="flex">
                                <div class="bg-gray-100 flex items-center px-3 border border-r-0 border-gray-300 rounded-l-md text-gray-500">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <input type="text" id="agent_code" name="agent_code" 
                                    class="form-input rounded-l-none w-full" 
                                    value="<?= htmlspecialchars($aggregator['agent_code']) ?>"
                                    placeholder="e.g. agent123" 
                                    required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Agent or username identifier provided by API vendor</p>
                        </div>
                        
                        <!-- Agent Token -->
                        <div>
                            <label for="agent_token" class="form-label flex items-center">
                                <span>Agent Token</span>
                                <span class="text-red-500 ml-1">*</span>
                                <span class="ml-2 text-xs text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded">Required</span>
                            </label>
                            <div class="flex">
                                <div class="bg-gray-100 flex items-center px-3 border border-r-0 border-gray-300 rounded-l-md text-gray-500">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="relative flex-1">
                                    <input type="password" id="agent_token" name="agent_token" 
                                        class="form-input rounded-l-none pr-10 w-full" 
                                        value="<?= htmlspecialchars($aggregator['agent_token']) ?>"
                                        placeholder="Enter authentication token" 
                                        required>
                                    <button type="button" id="toggle-token" 
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Authentication token or password provided by API vendor</p>
                        </div>
                    </div>
                </div>
                
                <!-- Status Section -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 md:col-span-2">
                    <h3 class="text-md font-medium text-gray-700 mb-3">Status</h3>
                    
                    <div class="flex items-center p-2">
                        <div class="form-control">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="status" name="status" class="sr-only peer" <?= $aggregator['status'] == STATUS_ACTIVE ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900">
                                    <span id="status-text"><?= $aggregator['status'] == STATUS_ACTIVE ? 'Active' : 'Inactive' ?></span>
                                </span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 ml-4">
                            <?= $aggregator['status'] == STATUS_ACTIVE 
                                ? 'This aggregator is active and available for use' 
                                : 'This aggregator is inactive and will not be available for use' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <a href="/aggregators.php" class="btn btn-secondary">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-2"></i>
                <?= $isEdit ? 'Update Aggregator' : 'Add Aggregator' ?>
            </button>
        </div>
    </form>
</div>

<!-- Help Card -->
<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-indigo-500 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-indigo-800">Need Help?</h3>
            <div class="mt-2 text-sm text-indigo-700">
                <p class="mb-2">To properly configure an API Aggregator, you'll need:</p>
                <ul class="list-disc list-inside space-y-1 mb-2">
                    <li>API documentation from the provider</li>
                    <li>Valid credentials (agent code and token)</li>
                    <li>Correct base URL for the API service</li>
                </ul>
                <p class="mb-1">Contact your API provider if you're missing any of these details.</p>
                <a href="/docs/aggregators.php" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    View detailed documentation <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <button class="ml-auto flex-shrink-0 text-indigo-500 hover:text-indigo-700" onclick="this.closest('.bg-indigo-50').classList.add('hidden')">
            <i class="fas fa-times"></i>
        </button>
    </div>
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
    
    // Status toggle UI update
    const statusCheckbox = document.getElementById('status');
    const statusText = document.getElementById('status-text');
    const statusDescription = statusText.parentElement.parentElement.nextElementSibling;
    
    if (statusCheckbox && statusText) {
        statusCheckbox.addEventListener('change', function() {
            if (this.checked) {
                statusText.textContent = 'Active';
                statusDescription.textContent = 'This aggregator is active and available for use';
            } else {
                statusText.textContent = 'Inactive';
                statusDescription.textContent = 'This aggregator is inactive and will not be available for use';
            }
        });
    }
    
    // Form validation with better UX
    const form = document.querySelector('form.needs-validation');
    
    if (form) {
        // Highlight fields on focus
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            // Add focus effect
            input.addEventListener('focus', function() {
                this.closest('div').classList.add('ring-2', 'ring-indigo-200');
            });
            
            // Remove focus effect
            input.addEventListener('blur', function() {
                this.closest('div').classList.remove('ring-2', 'ring-indigo-200');
                
                // Validate on blur
                if (this.value.trim() === '') {
                    this.classList.add('border-red-300');
                    
                    // Add error message if it doesn't exist
                    let errorMsg = this.parentElement.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('p');
                        errorMsg.className = 'text-xs text-red-500 mt-1 error-message';
                        errorMsg.textContent = 'This field is required';
                        
                        // Find the right place to insert
                        const parent = this.closest('div');
                        const helpText = parent.querySelector('.text-gray-500');
                        if (helpText) {
                            parent.insertBefore(errorMsg, helpText);
                        } else {
                            parent.appendChild(errorMsg);
                        }
                    }
                } else {
                    this.classList.remove('border-red-300');
                    
                    // Remove error message if it exists
                    const errorMsg = this.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Clear error on input
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('border-red-300');
                    
                    // Remove error message
                    const errorMsg = this.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
        });
    }
});
</script>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
