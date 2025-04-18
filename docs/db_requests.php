<?php
/**
 * API Manager - Documentation: Database Interaction for API Requests
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Set current page for sidebar highlight
$currentPage = 'docs';
$pageTitle = 'Documentation - Database Requests';

// Include header
require_once __DIR__ . '/../includes/head.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Documentation Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Database Interaction for API Requests</h1>
        <p class="text-gray-600">Learn how to use database functions for API requests and data management</p>
    </div>
    
    <nav class="text-sm breadcrumbs">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="/docs/index.php" class="text-indigo-600 hover:text-indigo-800">Documentation</a>
                <svg class="fill-current w-3 h-3 mx-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/>
                </svg>
            </li>
            <li>Database Interaction</li>
        </ol>
    </nav>
</div>

<!-- Documentation Content -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Understanding Database Interactions</h2>
        <p class="mb-4">
            The API Manager application provides a standardized way to interact with the database for storing and retrieving API configuration data, as well as logging API requests. This documentation covers the database functions that are used for API aggregator requests.
        </p>
        
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-3">Database Structure for API Interactions</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key Fields</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800"><?= TABLE_AGGREGATORS ?></td>
                            <td class="px-6 py-4">
                                Stores information about API aggregators
                            </td>
                            <td class="px-6 py-4">
                                <code>id</code>, <code>name</code>, <code>base_url</code>, <code>auth_type</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800"><?= TABLE_ENDPOINTS ?></td>
                            <td class="px-6 py-4">
                                Stores information about API endpoints
                            </td>
                            <td class="px-6 py-4">
                                <code>id</code>, <code>aggregator_id</code>, <code>name</code>, <code>path</code>, <code>method</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800"><?= TABLE_LOGS ?></td>
                            <td class="px-6 py-4">
                                Stores API request logs
                            </td>
                            <td class="px-6 py-4">
                                <code>id</code>, <code>aggregator_id</code>, <code>endpoint_id</code>, <code>timestamp</code>, <code>status_code</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800"><?= TABLE_TEMPLATES ?></td>
                            <td class="px-6 py-4">
                                Stores API request templates
                            </td>
                            <td class="px-6 py-4">
                                <code>id</code>, <code>name</code>, <code>endpoint_id</code>, <code>params</code>, <code>headers</code>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Example 1: Querying API Aggregators -->
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-3">Example 1: Querying API Aggregators</h3>
            <p class="mb-4">This example demonstrates how to retrieve a list of API aggregators from the database:</p>
            
            <div class="bg-gray-800 rounded-lg overflow-hidden mb-4">
                <div class="px-4 py-2 bg-gray-700">
                    <span class="text-xs text-gray-200">PHP Code</span>
                </div>
                <pre class="p-4 text-white text-sm overflow-x-auto"><code>// Get all active aggregators
$aggregators = getAllRecords(
    TABLE_AGGREGATORS,
    1,                // Page number
    100,              // Records per page
    'status = :status', // Where clause
    [':status' => STATUS_ACTIVE], // Parameters
    'name ASC'        // Order by
);

// Display aggregators
foreach ($aggregators as $aggregator) {
    echo "Aggregator: {$aggregator['name']} ({$aggregator['base_url']})\n";
}</code></pre>
            </div>
            
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                The <code>getAllRecords()</code> function provides a convenient way to query records with pagination, filtering, and sorting.
            </p>
        </div>
        
        <!-- Example 2: Making an API Request with Database Logging -->
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-3">Example 2: Making an API Request with Database Logging</h3>
            <p class="mb-4">This example shows how to make an API request using the configuration from the database and log the request:</p>
            
            <div class="bg-gray-800 rounded-lg overflow-hidden mb-4">
                <div class="px-4 py-2 bg-gray-700">
                    <span class="text-xs text-gray-200">PHP Code</span>
                </div>
                <pre class="p-4 text-white text-sm overflow-x-auto"><code>/**
 * Make an API request using database configuration
 * 
 * @param int $endpointId The endpoint ID
 * @param array $params Request parameters
 * @param array $headers Additional headers
 * @return array Response data
 */
function makeApiRequestWithLogging($endpointId, $params = [], $headers = []) {
    global $pdo;
    
    // Get endpoint data
    $endpoint = getRecordById(TABLE_ENDPOINTS, $endpointId);
    if (!$endpoint) {
        throw new Exception("Endpoint not found");
    }
    
    // Get aggregator data
    $aggregator = getRecordById(TABLE_AGGREGATORS, $endpoint['aggregator_id']);
    if (!$aggregator) {
        throw new Exception("Aggregator not found");
    }
    
    // Build request URL
    $url = rtrim($aggregator['base_url'], '/') . '/' . ltrim($endpoint['path'], '/');
    
    // Add authentication headers based on aggregator config
    if ($aggregator['auth_type'] === 'bearer') {
        $headers['Authorization'] = 'Bearer ' . $aggregator['auth_token'];
    } elseif ($aggregator['auth_type'] === 'basic') {
        $headers['Authorization'] = 'Basic ' . base64_encode($aggregator['auth_username'] . ':' . $aggregator['auth_password']);
    } elseif ($aggregator['auth_type'] === 'api_key') {
        if ($aggregator['auth_location'] === 'header') {
            $headers[$aggregator['auth_name']] = $aggregator['auth_value'];
        } else {
            $params[$aggregator['auth_name']] = $aggregator['auth_value'];
        }
    }
    
    // Set content type if not specified
    if (!isset($headers['Content-Type'])) {
        $headers['Content-Type'] = 'application/json';
    }
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options based on HTTP method
    if ($endpoint['method'] === 'GET') {
        // For GET requests, add parameters to URL
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        // For POST, PUT, DELETE requests
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if ($endpoint['method'] === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $endpoint['method']);
        }
        
        // Set request body
        if ($headers['Content-Type'] === 'application/json') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
    }
    
    // Set common cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Set headers
    $headerArr = [];
    foreach ($headers as $key => $value) {
        $headerArr[] = "$key: $value";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
    
    // Record start time for response time tracking
    $startTime = microtime(true);
    
    // Execute request
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $responseTime = round((microtime(true) - $startTime) * 1000, 2); // Convert to milliseconds
    $error = curl_error($ch);
    
    // Close cURL session
    curl_close($ch);
    
    // Log the request
    $logData = [
        'aggregator_id' => $aggregator['id'],
        'endpoint_id' => $endpoint['id'],
        'timestamp' => date('Y-m-d H:i:s'),
        'request_url' => $url,
        'request_method' => $endpoint['method'],
        'request_headers' => json_encode($headers),
        'request_body' => json_encode($params),
        'status_code' => $statusCode,
        'response_body' => $response,
        'response_time' => $responseTime,
        'error' => $error
    ];
    
    // Insert log record
    insertRecord(TABLE_LOGS, $logData);
    
    // Process response
    $result = [
        'status_code' => $statusCode,
        'response_time' => $responseTime,
        'error' => $error,
        'response' => $response ? json_decode($response, true) : null
    ];
    
    return $result;
}

// Example usage:
try {
    $result = makeApiRequestWithLogging(
        1,           // Endpoint ID
        ['limit' => 10, 'offset' => 0],  // Parameters
        ['X-Custom-Header' => 'Value']   // Headers
    );
    
    if ($result['status_code'] >= 200 && $result['status_code'] < 300) {
        echo "Success! Response: " . print_r($result['response'], true);
    } else {
        echo "Error! Status code: {$result['status_code']}";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}</code></pre>
            </div>
            
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                This example demonstrates a complete workflow for making API requests with your stored configurations, including authentication handling and automatic logging.
            </p>
        </div>
        
        <!-- Example 3: Retrieving and Analyzing API Logs -->
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-medium text-gray-800 mb-3">Example 3: Retrieving and Analyzing API Logs</h3>
            <p class="mb-4">This example demonstrates how to retrieve and analyze API request logs from the database:</p>
            
            <div class="bg-gray-800 rounded-lg overflow-hidden mb-4">
                <div class="px-4 py-2 bg-gray-700">
                    <span class="text-xs text-gray-200">PHP Code</span>
                </div>
                <pre class="p-4 text-white text-sm overflow-x-auto"><code>/**
 * Get API usage statistics for an endpoint
 * 
 * @param int $endpointId The endpoint ID
 * @param string $startDate Start date (Y-m-d format)
 * @param string $endDate End date (Y-m-d format)
 * @return array Statistics data
 */
function getEndpointStatistics($endpointId, $startDate, $endDate) {
    global $pdo;
    
    // Prepare the date range condition
    $dateRangeCondition = "endpoint_id = :endpoint_id AND DATE(timestamp) BETWEEN :start_date AND :end_date";
    $params = [
        ':endpoint_id' => $endpointId,
        ':start_date' => $startDate,
        ':end_date' => $endDate
    ];
    
    // Get all logs for the endpoint in the date range
    $logs = getAllRecords(TABLE_LOGS, 1, 1000, $dateRangeCondition, $params, 'timestamp ASC');
    
    // Calculate statistics
    $totalRequests = count($logs);
    $successRequests = 0;
    $failedRequests = 0;
    $totalResponseTime = 0;
    $maxResponseTime = 0;
    $minResponseTime = PHP_INT_MAX;
    $responseTimes = [];
    $statusCodes = [];
    
    foreach ($logs as $log) {
        // Count success/error requests
        if ($log['status_code'] >= 200 && $log['status_code'] < 400) {
            $successRequests++;
        } else {
            $failedRequests++;
        }
        
        // Track response time
        $responseTime = $log['response_time'];
        $totalResponseTime += $responseTime;
        $maxResponseTime = max($maxResponseTime, $responseTime);
        $minResponseTime = min($minResponseTime, $responseTime);
        $responseTimes[] = $responseTime;
        
        // Count status codes
        $statusCode = $log['status_code'];
        if (!isset($statusCodes[$statusCode])) {
            $statusCodes[$statusCode] = 0;
        }
        $statusCodes[$statusCode]++;
    }
    
    // Calculate average response time
    $avgResponseTime = $totalRequests > 0 ? $totalResponseTime / $totalRequests : 0;
    
    // Calculate success rate
    $successRate = $totalRequests > 0 ? ($successRequests / $totalRequests) * 100 : 0;
    
    // Get endpoint details
    $endpoint = getRecordById(TABLE_ENDPOINTS, $endpointId);
    $endpointName = $endpoint ? $endpoint['name'] : 'Unknown';
    
    // Get aggregator details
    $aggregatorId = $endpoint ? $endpoint['aggregator_id'] : 0;
    $aggregator = getRecordById(TABLE_AGGREGATORS, $aggregatorId);
    $aggregatorName = $aggregator ? $aggregator['name'] : 'Unknown';
    
    return [
        'endpoint_id' => $endpointId,
        'endpoint_name' => $endpointName,
        'aggregator_id' => $aggregatorId,
        'aggregator_name' => $aggregatorName,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'total_requests' => $totalRequests,
        'success_requests' => $successRequests,
        'failed_requests' => $failedRequests,
        'success_rate' => round($successRate, 2),
        'avg_response_time' => round($avgResponseTime, 2),
        'min_response_time' => $minResponseTime === PHP_INT_MAX ? 0 : round($minResponseTime, 2),
        'max_response_time' => round($maxResponseTime, 2),
        'status_codes' => $statusCodes
    ];
}

// Example usage:
$statistics = getEndpointStatistics(
    1,                    // Endpoint ID
    date('Y-m-d', strtotime('-30 days')), // Start date (30 days ago)
    date('Y-m-d')         // End date (today)
);

echo "Statistics for endpoint: {$statistics['endpoint_name']}\n";
echo "Total requests: {$statistics['total_requests']}\n";
echo "Success rate: {$statistics['success_rate']}%\n";
echo "Average response time: {$statistics['avg_response_time']} ms\n";</code></pre>
            </div>
            
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                This function calculates useful statistics from your API logs, which can be used for monitoring performance, identifying issues, or creating visualizations.
            </p>
        </div>
        
        <!-- Best Practices -->
        <div class="bg-indigo-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-3">Best Practices for Database Interactions</h3>
            
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span><strong>Use parameterized queries</strong> to prevent SQL injection attacks. The <code>getAllRecords()</code> function handles this automatically.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span><strong>Cache frequently accessed data</strong> to reduce database load. Consider implementing a caching layer for API configurations.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span><strong>Log all API requests</strong> to enable monitoring, debugging, and analytics. The <code>makeApiRequestWithLogging()</code> function does this automatically.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span><strong>Handle database errors gracefully</strong> and provide meaningful error messages to users.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span><strong>Regularly clean up old log records</strong> to prevent the database from growing too large.</span>
                </li>
            </ul>
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-between mt-8">
            <a href="/docs/index.php" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Documentation Index
            </a>
            <a href="/docs/api_testing.php" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                API Testing Guide <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>