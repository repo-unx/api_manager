<?php
/**
 * API Manager - Interactive API Endpoint Playground
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'playground';
$pageTitle = 'API Playground';

// Get all aggregators for the dropdown
$aggregators = getAllRecords(TABLE_AGGREGATORS, 1, 100, 'status = :status', [':status' => STATUS_ACTIVE], 'name ASC');

// Get all endpoints for initial load
$endpoints = [];
if (!empty($aggregators)) {
    $firstAggregatorId = $aggregators[0]['id'];
    $endpoints = getAllRecords(TABLE_ENDPOINTS, 1, 100, 'aggregator_id = :id AND status = :status', 
                            [':id' => $firstAggregatorId, ':status' => STATUS_ACTIVE], 'name ASC');
}

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">API Playground</h1>
        <p class="text-gray-600">Test API endpoints interactively with live response visualization</p>
    </div>
</div>

<?php displayMessage(); ?>

<!-- API Playground -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left panel - Endpoint selection -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800 flex items-center">
                    <i class="fas fa-sitemap text-indigo-500 mr-2"></i>
                    Select Endpoint
                </h2>
            </div>
            <div class="p-6">
                <!-- Aggregator Selection -->
                <div class="mb-6">
                    <label for="aggregator-select" class="form-label">API Aggregator</label>
                    <select id="aggregator-select" class="form-input">
                        <?php foreach ($aggregators as $aggregator): ?>
                            <option value="<?= $aggregator['id'] ?>"><?= htmlspecialchars($aggregator['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Endpoint Selection -->
                <div class="mb-6">
                    <label for="endpoint-select" class="form-label">API Endpoint</label>
                    <select id="endpoint-select" class="form-input">
                        <?php foreach ($endpoints as $endpoint): ?>
                            <option value="<?= $endpoint['id'] ?>"><?= htmlspecialchars($endpoint['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Endpoint Details -->
                <div id="endpoint-details" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-2">
                        <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-800 text-xs font-semibold mr-2 endpoint-method">GET</span>
                        <span class="text-sm text-gray-700 truncate endpoint-url">https://api.example.com/endpoint</span>
                    </div>
                    <div class="text-xs text-gray-500 endpoint-description">
                        Select an endpoint to see details
                    </div>
                </div>
                
                <!-- Load from Saved Templates -->
                <div class="mb-4">
                    <label class="form-label">Templates</label>
                    <div class="flex">
                        <select id="template-select" class="form-input rounded-r-none">
                            <option value="">Load from template...</option>
                            <!-- Templates will be loaded via AJAX -->
                        </select>
                        <button id="load-template" class="btn btn-secondary rounded-l-none border-l-0">
                            Load
                        </button>
                    </div>
                </div>
                
                <!-- Save as Template button -->
                <button id="save-as-template" class="w-full btn btn-primary flex items-center justify-center mb-2">
                    <i class="fas fa-save mr-2"></i> Save Current Request as Template
                </button>
                
                <!-- Documentation Link -->
                <a href="/docs/endpoints.php" class="text-center text-sm text-indigo-600 hover:text-indigo-800 block">
                    <i class="fas fa-question-circle mr-1"></i> How to use the API Playground
                </a>
            </div>
        </div>
        
        <!-- History Panel -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-medium text-gray-700">Request History</h3>
                <button id="clear-history" class="text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-trash-alt mr-1"></i> Clear
                </button>
            </div>
            <div class="p-2 max-h-80 overflow-y-auto" id="history-container">
                <div class="text-center text-sm text-gray-500 py-4">
                    No request history yet
                </div>
                <!-- History items will be added dynamically -->
            </div>
        </div>
    </div>
    
    <!-- Right panel - Request builder and response -->
    <div class="lg:col-span-2">
        <!-- Request Builder -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800 flex items-center">
                    <i class="fas fa-pencil-alt text-indigo-500 mr-2"></i>
                    Request Builder
                </h2>
            </div>
            
            <div class="p-6">
                <form id="api-request-form">
                    <!-- Request URL -->
                    <div class="mb-6">
                        <label for="request-url" class="form-label flex items-center justify-between">
                            <span>Request URL</span>
                            <span id="method-badge" class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-medium">GET</span>
                        </label>
                        <div class="flex">
                            <div class="bg-gray-100 flex items-center px-3 border border-r-0 border-gray-300 rounded-l-md text-gray-500">
                                <span id="base-url" class="text-xs truncate max-w-[120px]">https://api.example.com</span>
                            </div>
                            <input type="text" id="request-url" class="form-input rounded-l-none" placeholder="/endpoint" value="/">
                        </div>
                    </div>
                    
                    <!-- Headers, Query Params, and Body Tabs -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="flex flex-wrap -mb-px">
                                <button type="button" data-tab="headers" class="tab-button mr-4 py-2 px-1 border-b-2 border-indigo-500 text-indigo-600 font-medium text-sm">
                                    Headers
                                </button>
                                <button type="button" data-tab="params" class="tab-button mr-4 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                    Query Parameters
                                </button>
                                <button type="button" data-tab="body" class="tab-button py-2 px-1 border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                    Request Body
                                </button>
                            </nav>
                        </div>
                        
                        <!-- Headers Tab -->
                        <div id="headers-tab" class="tab-content py-4">
                            <div id="headers-editor" class="json-editor h-60" data-field="headers"></div>
                            <input type="hidden" id="headers-input" name="headers" value='{"Content-Type": "application/json"}'>
                        </div>
                        
                        <!-- Query Parameters Tab -->
                        <div id="params-tab" class="tab-content py-4 hidden">
                            <div id="params-editor" class="json-editor h-60" data-field="params"></div>
                            <input type="hidden" id="params-input" name="params" value='{}'>
                        </div>
                        
                        <!-- Request Body Tab -->
                        <div id="body-tab" class="tab-content py-4 hidden">
                            <div id="body-editor" class="json-editor h-60" data-field="body"></div>
                            <input type="hidden" id="body-input" name="body" value='{}'>
                        </div>
                    </div>
                    
                    <!-- Authentication Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-700">Authentication</h3>
                            <div class="flex items-center">
                                <input type="checkbox" id="use-aggregator-auth" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                                <label for="use-aggregator-auth" class="ml-2 block text-sm text-gray-900">
                                    Use Aggregator Credentials
                                </label>
                            </div>
                        </div>
                        
                        <div id="auth-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                            <div>
                                <label for="auth-username" class="form-label">Username/Agent Code</label>
                                <input type="text" id="auth-username" class="form-input" placeholder="Enter username or agent code">
                            </div>
                            <div>
                                <label for="auth-token" class="form-label">Token/Password</label>
                                <input type="password" id="auth-token" class="form-input" placeholder="Enter token or password">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Variable Replacements -->
                    <div class="mb-6" id="variables-container">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Request Variables</h3>
                        <p class="text-xs text-gray-500 mb-4">
                            The following variables were detected in your request. Please provide values:
                        </p>
                        
                        <div id="variables-list" class="space-y-3">
                            <!-- Variables will be added dynamically -->
                        </div>
                    </div>
                    
                    <!-- Send Button -->
                    <div class="flex justify-between items-center">
                        <button type="submit" id="send-request" class="btn btn-primary px-8">
                            <i class="fas fa-paper-plane mr-2"></i> Send Request
                        </button>
                        
                        <div class="text-sm text-gray-500 flex">
                            <div id="loading-indicator" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Sending...
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Response Viewer -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-800 flex items-center">
                    <i class="fas fa-reply text-indigo-500 mr-2"></i>
                    Response
                </h2>
                
                <div class="flex items-center space-x-3">
                    <div id="response-time" class="text-sm text-gray-600">
                        <i class="fas fa-clock"></i> <span>-- ms</span>
                    </div>
                    <div id="response-status" class="hidden">
                        <span class="px-2 py-1 rounded text-xs font-medium">200 OK</span>
                    </div>
                    <button id="copy-response" class="text-gray-500 hover:text-gray-700" title="Copy Response">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button id="clear-response" class="text-gray-500 hover:text-gray-700" title="Clear Response">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Response Tabs -->
                <div class="border-b border-gray-200 mb-4">
                    <nav class="flex flex-wrap -mb-px">
                        <button type="button" data-tab="response-body" class="response-tab-button mr-4 py-2 px-1 border-b-2 border-indigo-500 text-indigo-600 font-medium text-sm">
                            Response Body
                        </button>
                        <button type="button" data-tab="response-headers" class="response-tab-button mr-4 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-700 font-medium text-sm">
                            Response Headers
                        </button>
                        <button type="button" data-tab="request-details" class="response-tab-button py-2 px-1 border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-700 font-medium text-sm">
                            Request Details
                        </button>
                    </nav>
                </div>
                
                <!-- Response Body Tab -->
                <div id="response-body-tab" class="response-tab-content">
                    <div id="response-placeholder" class="text-center py-12">
                        <i class="fas fa-paper-plane text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500">Send a request to see the response here</p>
                    </div>
                    
                    <div id="response-error" class="bg-red-50 p-4 rounded-lg border border-red-200 hidden">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error</h3>
                                <div class="mt-2 text-sm text-red-700" id="error-message">
                                    An error occurred while making the request.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="response-json-container" class="hidden">
                        <div id="response-json-viewer" class="json-editor h-96" data-field="response" data-readonly="true"></div>
                    </div>
                    
                    <div id="response-text-container" class="hidden">
                        <pre id="response-text-content" class="bg-gray-800 text-white p-4 rounded-lg overflow-x-auto max-h-96"></pre>
                    </div>
                </div>
                
                <!-- Response Headers Tab -->
                <div id="response-headers-tab" class="response-tab-content hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Header</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody id="response-headers-container" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500">No response headers yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Request Details Tab -->
                <div id="request-details-tab" class="response-tab-content hidden">
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Request URL</h3>
                        <code id="final-request-url" class="block bg-gray-800 text-white p-3 rounded text-sm overflow-x-auto">https://api.example.com/endpoint</code>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Request Headers</h3>
                        <pre id="final-request-headers" class="block bg-gray-800 text-white p-3 rounded text-sm overflow-x-auto max-h-40">No headers sent</pre>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Request Body</h3>
                        <pre id="final-request-body" class="block bg-gray-800 text-white p-3 rounded text-sm overflow-x-auto max-h-40">No body sent</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template Save Modal -->
<div id="save-template-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Save as Template</h2>
        </div>
        
        <div class="p-6">
            <form id="save-template-form">
                <div class="mb-4">
                    <label for="template-name" class="form-label">Template Name</label>
                    <input type="text" id="template-name" class="form-input" placeholder="Enter a descriptive name" required>
                </div>
                
                <div class="mb-4">
                    <label for="template-description" class="form-label">Description (Optional)</label>
                    <textarea id="template-description" class="form-input" placeholder="Enter a brief description"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancel-save-template" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tabs
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Toggle active state for buttons
            tabButtons.forEach(btn => {
                if (btn.dataset.tab === tabName) {
                    btn.classList.add('border-indigo-500', 'text-indigo-600');
                    btn.classList.remove('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
                } else {
                    btn.classList.remove('border-indigo-500', 'text-indigo-600');
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
                }
            });
            
            // Toggle content visibility
            tabContents.forEach(content => {
                if (content.id === `${tabName}-tab`) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    });
    
    // Initialize response tabs
    const responseTabButtons = document.querySelectorAll('.response-tab-button');
    const responseTabContents = document.querySelectorAll('.response-tab-content');
    
    responseTabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Toggle active state for buttons
            responseTabButtons.forEach(btn => {
                if (btn.dataset.tab === tabName) {
                    btn.classList.add('border-indigo-500', 'text-indigo-600');
                    btn.classList.remove('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
                } else {
                    btn.classList.remove('border-indigo-500', 'text-indigo-600');
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
                }
            });
            
            // Toggle content visibility
            responseTabContents.forEach(content => {
                if (content.id === `${tabName}-tab`) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    });
    
    // Toggle authentication fields
    const useAggregatorAuth = document.getElementById('use-aggregator-auth');
    const authFields = document.getElementById('auth-fields');
    
    useAggregatorAuth.addEventListener('change', function() {
        if (this.checked) {
            authFields.classList.add('hidden');
        } else {
            authFields.classList.remove('hidden');
        }
    });
    
    // Save Template Modal
    const saveTemplateButton = document.getElementById('save-as-template');
    const saveTemplateModal = document.getElementById('save-template-modal');
    const cancelSaveTemplate = document.getElementById('cancel-save-template');
    
    saveTemplateButton.addEventListener('click', function() {
        saveTemplateModal.classList.remove('hidden');
    });
    
    cancelSaveTemplate.addEventListener('click', function() {
        saveTemplateModal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    saveTemplateModal.addEventListener('click', function(e) {
        if (e.target === saveTemplateModal) {
            saveTemplateModal.classList.add('hidden');
        }
    });
    
    // TODO: Implement loadEndpointData, sendRequest functions in a separate JS file
    
    // This is a placeholder for development and demo purposes
    // In a production environment, you would implement full functionality here
    document.getElementById('api-request-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading indicator for a nice demo
        const loadingIndicator = document.getElementById('loading-indicator');
        loadingIndicator.classList.remove('hidden');
        
        // For demo purposes, wait 1 second and show a sample response
        setTimeout(function() {
            // Hide loading indicator
            loadingIndicator.classList.add('hidden');
            
            // Show response elements
            document.getElementById('response-placeholder').classList.add('hidden');
            document.getElementById('response-json-container').classList.remove('hidden');
            
            // Update response status
            const responseStatus = document.getElementById('response-status');
            responseStatus.classList.remove('hidden');
            responseStatus.innerHTML = '<span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">200 OK</span>';
            
            // Update response time
            document.getElementById('response-time').innerHTML = '<i class="fas fa-clock"></i> <span>423 ms</span>';
            
            // Sample JSON response in the viewer
            if (window.jsonEditors && window.jsonEditors.response) {
                const sampleResponse = {
                    "status": "success",
                    "code": 200,
                    "data": {
                        "user_id": "user123",
                        "balance": 1000.50,
                        "currency": "USD",
                        "last_updated": "2023-10-10T15:30:45Z"
                    }
                };
                window.jsonEditors.response.set(sampleResponse);
            }
            
            // Add to history (demo)
            const historyContainer = document.getElementById('history-container');
            historyContainer.innerHTML = `
                <div class="px-3 py-2 hover:bg-gray-50 rounded cursor-pointer border-l-4 border-green-500">
                    <div class="flex items-center">
                        <span class="px-1.5 py-0.5 rounded bg-green-100 text-green-800 text-xs font-semibold mr-2">GET</span>
                        <span class="text-xs text-gray-700 truncate">/api/users/balance</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-500">200 OK</span>
                        <span class="text-xs text-gray-500">Just now</span>
                    </div>
                </div>
            ` + historyContainer.innerHTML;
            
            // Remove the "no history" message if it exists
            const noHistoryMsg = historyContainer.querySelector('.text-center');
            if (noHistoryMsg) {
                noHistoryMsg.remove();
            }
        }, 1000);
    });
});
</script>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>