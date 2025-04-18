<?php
/**
 * Application Constants
 */

// Application info
define('APP_NAME', 'API Manager');
define('APP_VERSION', '1.0.0');

// Database table names
define('TABLE_AGGREGATORS', 'api_aggregators');
define('TABLE_ENDPOINTS', 'api_endpoints');
define('TABLE_LOGS', 'api_requests_log');
define('TABLE_TEMPLATES', 'api_request_templates');

// Pagination settings
define('ITEMS_PER_PAGE', 10);

// Status codes
define('STATUS_ACTIVE', 1);
define('STATUS_INACTIVE', 0);

// HTTP Methods
define('HTTP_METHODS', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS']);

// JSON editor settings
define('JSON_EDITOR_HEIGHT', '300px');
