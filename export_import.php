<?php
/**
 * API Manager - Export/Import Configuration
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'export_import';
$pageTitle = 'Export/Import Configuration';

// Handle export request
if (isset($_POST['action']) && $_POST['action'] === 'export') {
    // Get configuration data
    $exportData = [];
    
    // Export aggregators
    if (isset($_POST['export_aggregators'])) {
        $aggregators = getAllRecords(TABLE_AGGREGATORS);
        $exportData['aggregators'] = $aggregators;
    }
    
    // Export endpoints
    if (isset($_POST['export_endpoints'])) {
        $endpoints = getAllRecords(TABLE_ENDPOINTS);
        $exportData['endpoints'] = $endpoints;
    }
    
    // Export templates
    if (isset($_POST['export_templates'])) {
        $templates = getAllRecords(TABLE_TEMPLATES);
        $exportData['templates'] = $templates;
    }
    
    // Add metadata
    $exportData['metadata'] = [
        'exported_at' => date('Y-m-d H:i:s'),
        'version' => '1.0',
        'application' => APP_NAME
    ];
    
    // Generate export JSON
    $exportJson = json_encode($exportData, JSON_PRETTY_PRINT);
    
    // Generate file name
    $fileName = 'api_manager_export_' . date('Y-m-d_H-i-s') . '.json';
    
    // Set headers for download
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($exportJson));
    
    // Output export data and exit
    echo $exportJson;
    exit;
}

// Handle import request
if (isset($_POST['action']) && $_POST['action'] === 'import' && isset($_FILES['import_file'])) {
    $importedCount = ['aggregators' => 0, 'endpoints' => 0, 'templates' => 0];
    $errors = [];
    
    // Check if file is valid JSON
    $file = $_FILES['import_file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        setMessage('Error uploading file. Please try again.', 'error');
    } else {
        // Read the file content
        $importJson = file_get_contents($file['tmp_name']);
        
        try {
            // Decode JSON
            $importData = json_decode($importJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON file. Please provide a valid export file.');
            }
            
            // Verify metadata
            if (!isset($importData['metadata']) || !isset($importData['metadata']['application']) || $importData['metadata']['application'] !== APP_NAME) {
                throw new Exception('Invalid export file. The file is not a valid API Manager export.');
            }
            
            // Begin transaction (if supported by DB driver)
            if (method_exists($pdo, 'beginTransaction')) {
                $pdo->beginTransaction();
            }
            
            // Import aggregators
            if (isset($_POST['import_aggregators']) && isset($importData['aggregators'])) {
                foreach ($importData['aggregators'] as $aggregator) {
                    // Remove ID to create a new record
                    if (!isset($_POST['preserve_ids'])) {
                        unset($aggregator['id']);
                    }
                    
                    // Check if aggregator with the same name already exists
                    if (!recordExists(TABLE_AGGREGATORS, 'name', $aggregator['name'])) {
                        // Insert new aggregator
                        if (insertRecord(TABLE_AGGREGATORS, $aggregator)) {
                            $importedCount['aggregators']++;
                        }
                    } else if (isset($_POST['overwrite_existing'])) {
                        // Update existing aggregator
                        $existingAggregator = getRecordByField(TABLE_AGGREGATORS, 'name', $aggregator['name']);
                        if ($existingAggregator) {
                            if (updateRecord(TABLE_AGGREGATORS, $aggregator, $existingAggregator['id'])) {
                                $importedCount['aggregators']++;
                            }
                        }
                    }
                }
            }
            
            // Import endpoints
            if (isset($_POST['import_endpoints']) && isset($importData['endpoints'])) {
                foreach ($importData['endpoints'] as $endpoint) {
                    // Remove ID to create a new record
                    if (!isset($_POST['preserve_ids'])) {
                        $originalId = $endpoint['id'];
                        unset($endpoint['id']);
                    }
                    
                    // Check if endpoint with the same name already exists
                    if (!recordExists(TABLE_ENDPOINTS, 'name', $endpoint['name'])) {
                        // Insert new endpoint
                        if (insertRecord(TABLE_ENDPOINTS, $endpoint)) {
                            $importedCount['endpoints']++;
                        }
                    } else if (isset($_POST['overwrite_existing'])) {
                        // Update existing endpoint
                        $existingEndpoint = getRecordByField(TABLE_ENDPOINTS, 'name', $endpoint['name']);
                        if ($existingEndpoint) {
                            if (updateRecord(TABLE_ENDPOINTS, $endpoint, $existingEndpoint['id'])) {
                                $importedCount['endpoints']++;
                            }
                        }
                    }
                }
            }
            
            // Import templates
            if (isset($_POST['import_templates']) && isset($importData['templates'])) {
                foreach ($importData['templates'] as $template) {
                    // Remove ID to create a new record
                    if (!isset($_POST['preserve_ids'])) {
                        unset($template['id']);
                    }
                    
                    // Check if template with the same name already exists
                    if (!recordExists(TABLE_TEMPLATES, 'name', $template['name'])) {
                        // Insert new template
                        if (insertRecord(TABLE_TEMPLATES, $template)) {
                            $importedCount['templates']++;
                        }
                    } else if (isset($_POST['overwrite_existing'])) {
                        // Update existing template
                        $existingTemplate = getRecordByField(TABLE_TEMPLATES, 'name', $template['name']);
                        if ($existingTemplate) {
                            if (updateRecord(TABLE_TEMPLATES, $template, $existingTemplate['id'])) {
                                $importedCount['templates']++;
                            }
                        }
                    }
                }
            }
            
            // Commit transaction (if supported by DB driver)
            if (method_exists($pdo, 'commit')) {
                $pdo->commit();
            }
            
            // Set success message
            $successMessage = 'Import completed: ';
            $successMessage .= $importedCount['aggregators'] . ' aggregators, ';
            $successMessage .= $importedCount['endpoints'] . ' endpoints, ';
            $successMessage .= $importedCount['templates'] . ' templates imported.';
            
            setMessage($successMessage, 'success');
            
        } catch (Exception $e) {
            // Rollback transaction (if supported by DB driver)
            if (method_exists($pdo, 'rollBack')) {
                $pdo->rollBack();
            }
            
            setMessage('Error: ' . $e->getMessage(), 'error');
        }
    }
}

// Include header
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Export/Import Configuration</h1>
        <p class="text-gray-600">Backup, restore, or transfer your API configurations</p>
    </div>
</div>

<?php displayMessage(); ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Export Configuration -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-200">
            <h2 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-file-export text-indigo-500 mr-2"></i>
                Export Configuration
            </h2>
            <p class="text-sm text-gray-600 mt-1">Export your API configurations for backup or transfer</p>
        </div>
        
        <div class="p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="export">
                
                <div class="space-y-4">
                    <h3 class="text-md font-medium text-gray-700 border-b border-gray-200 pb-2">Select Components to Export</h3>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="export_aggregators" name="export_aggregators" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                        <label for="export_aggregators" class="ml-2 block text-sm text-gray-900">
                            API Aggregators
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="export_endpoints" name="export_endpoints" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                        <label for="export_endpoints" class="ml-2 block text-sm text-gray-900">
                            API Endpoints
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="export_templates" name="export_templates" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                        <label for="export_templates" class="ml-2 block text-sm text-gray-900">
                            Request Templates
                        </label>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Export Information</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>The exported file will contain:</p>
                                <ul class="list-disc list-inside space-y-1 mt-1">
                                    <li>Selected configuration components</li>
                                    <li>Metadata about the export (date, version)</li>
                                    <li>No sensitive data like real API keys will be exported</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary flex items-center">
                        <i class="fas fa-download mr-2"></i> Export Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Import Configuration -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-200">
            <h2 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-file-import text-emerald-500 mr-2"></i>
                Import Configuration
            </h2>
            <p class="text-sm text-gray-600 mt-1">Import previously exported API configurations</p>
        </div>
        
        <div class="p-6">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="import">
                
                <div class="space-y-4">
                    <h3 class="text-md font-medium text-gray-700 border-b border-gray-200 pb-2">Select File to Import</h3>
                    
                    <div>
                        <label for="import_file" class="form-label">Configuration File</label>
                        <input type="file" id="import_file" name="import_file" class="form-input" accept=".json" required>
                        <p class="text-xs text-gray-500 mt-1">Select a previously exported JSON configuration file</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="text-md font-medium text-gray-700 border-b border-gray-200 pb-2">Import Options</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="import_aggregators" name="import_aggregators" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                            <label for="import_aggregators" class="ml-2 block text-sm text-gray-900">
                                Import Aggregators
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="import_endpoints" name="import_endpoints" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                            <label for="import_endpoints" class="ml-2 block text-sm text-gray-900">
                                Import Endpoints
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="import_templates" name="import_templates" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                            <label for="import_templates" class="ml-2 block text-sm text-gray-900">
                                Import Templates
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="overwrite_existing" name="overwrite_existing" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="overwrite_existing" class="ml-2 block text-sm text-gray-900">
                                Overwrite Existing
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="preserve_ids" name="preserve_ids" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="preserve_ids" class="ml-2 block text-sm text-gray-900">
                                Preserve IDs
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Important Information</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p>Please note the following:</p>
                                <ul class="list-disc list-inside space-y-1 mt-1">
                                    <li>Existing items won't be overwritten unless "Overwrite Existing" is checked</li>
                                    <li>New IDs will be generated unless "Preserve IDs" is checked</li>
                                    <li>Related items (e.g., endpoints for aggregators) will maintain their relationships</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary flex items-center">
                        <i class="fas fa-upload mr-2"></i> Import Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Usage Instructions -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-800">Usage Instructions</h2>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-file-export text-indigo-500 mr-2"></i>
                    Exporting Configurations
                </h3>
                
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li class="mb-2">
                        <span class="font-medium">Select components</span> to export (Aggregators, Endpoints, Templates)
                    </li>
                    <li class="mb-2">
                        <span class="font-medium">Click the Export button</span> to generate and download a JSON file
                    </li>
                    <li class="mb-2">
                        <span class="font-medium">Store the file safely</span> for future imports or transfers
                    </li>
                </ol>
                
                <div class="mt-4">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                        <span class="font-medium">Tip:</span> Regular exports are recommended for backup purposes. Consider automating this process for production environments.
                    </p>
                </div>
            </div>
            
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-file-import text-emerald-500 mr-2"></i>
                    Importing Configurations
                </h3>
                
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li class="mb-2">
                        <span class="font-medium">Select a configuration file</span> to import (JSON format)
                    </li>
                    <li class="mb-2">
                        <span class="font-medium">Choose import options</span> based on your needs:
                        <ul class="list-disc list-inside ml-6 mt-1 text-gray-600">
                            <li>Select which components to import</li>
                            <li>Decide whether to overwrite existing items</li>
                            <li>Choose whether to preserve original IDs</li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <span class="font-medium">Click Import</span> to process the file
                    </li>
                </ol>
                
                <div class="mt-4">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-exclamation-circle text-red-500 mr-1"></i>
                        <span class="font-medium">Warning:</span> Preserving IDs may cause conflicts if the target system already has records with the same IDs.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>