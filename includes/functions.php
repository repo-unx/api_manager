<?php
/**
 * Common functions used throughout the application
 */

/**
 * Format dates for display
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Clean input data
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate JSON string
 */
function isValidJson($string) {
    if (!is_string($string) || empty($string)) {
        return false;
    }
    
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Format JSON for display
 */
function formatJson($json) {
    if (!isValidJson($json)) {
        return $json;
    }
    
    $data = json_decode($json);
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

/**
 * Display pagination controls
 */
function displayPagination($currentPage, $totalPages, $urlPattern) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="flex justify-center mt-4">';
    $html .= '<nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">';
    
    // Previous page link
    if ($currentPage > 1) {
        $prevUrl = str_replace('{page}', $currentPage - 1, $urlPattern);
        $html .= '<a href="' . $prevUrl . '" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
        $html .= '<span class="sr-only">Previous</span>';
        $html .= '<i class="fas fa-chevron-left"></i>';
        $html .= '</a>';
    } else {
        $html .= '<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">';
        $html .= '<span class="sr-only">Previous</span>';
        $html .= '<i class="fas fa-chevron-left"></i>';
        $html .= '</span>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $url = str_replace('{page}', 1, $urlPattern);
        $html .= '<a href="' . $url . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
        if ($startPage > 2) {
            $html .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-indigo-50 text-sm font-medium text-indigo-600">' . $i . '</span>';
        } else {
            $url = str_replace('{page}', $i, $urlPattern);
            $html .= '<a href="' . $url . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $i . '</a>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
        }
        $url = str_replace('{page}', $totalPages, $urlPattern);
        $html .= '<a href="' . $url . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $totalPages . '</a>';
    }
    
    // Next page link
    if ($currentPage < $totalPages) {
        $nextUrl = str_replace('{page}', $currentPage + 1, $urlPattern);
        $html .= '<a href="' . $nextUrl . '" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">';
        $html .= '<span class="sr-only">Next</span>';
        $html .= '<i class="fas fa-chevron-right"></i>';
        $html .= '</a>';
    } else {
        $html .= '<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400">';
        $html .= '<span class="sr-only">Next</span>';
        $html .= '<i class="fas fa-chevron-right"></i>';
        $html .= '</span>';
    }
    
    $html .= '</nav>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Get current page number from query string
 */
function getCurrentPage() {
    return isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
}

/**
 * Display notification message
 */
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        $bgColor = match($type) {
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            default => 'bg-blue-100 border-blue-400 text-blue-700',
        };
        
        echo '<div class="' . $bgColor . ' px-4 py-3 rounded relative mb-4 border" role="alert">';
        echo '<span class="block sm:inline">' . $message . '</span>';
        echo '<button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3 close-alert">';
        echo '<span class="sr-only">Close</span>';
        echo '<i class="fas fa-times"></i>';
        echo '</button>';
        echo '</div>';
    }
}

/**
 * Set notification message
 */
function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

/**
 * Check if record exists
 */
function recordExists($table, $field, $value, $idField = null, $idValue = null) {
    $db = getDbConnection();
    
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $field = :value";
    $params = [':value' => $value];
    
    // Exclude current record when updating
    if ($idField && $idValue) {
        $sql .= " AND $idField != :id";
        $params[':id'] = $idValue;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    
    return $result['count'] > 0;
}

/**
 * Get options for dropdown from database table
 */
function getOptionsFromTable($table, $valueField, $labelField, $where = null, $params = []) {
    $db = getDbConnection();
    
    $sql = "SELECT $valueField, $labelField FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
    }
    
    $sql .= " ORDER BY $labelField";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $options = [];
    
    while ($row = $stmt->fetch()) {
        $options[$row[$valueField]] = $row[$labelField];
    }
    
    return $options;
}

/**
 * Generate dropdown HTML
 */
function generateDropdown($name, $options, $selectedValue = '', $required = false, $class = 'form-input') {
    $html = '<select name="' . $name . '" id="' . $name . '" class="' . $class . '"' . ($required ? ' required' : '') . '>';
    $html .= '<option value="">Select an option</option>';
    
    foreach ($options as $value => $label) {
        $selected = $selectedValue == $value ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($value) . '"' . $selected . '>' . htmlspecialchars($label) . '</option>';
    }
    
    $html .= '</select>';
    
    return $html;
}

/**
 * Convert database type to friendly status
 */
function getStatusBadge($status) {
    if ($status == STATUS_ACTIVE) {
        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
    } else {
        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
    }
}

/**
 * Truncate long text for display
 */
function truncateText($text, $length = 50) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . '...';
}
