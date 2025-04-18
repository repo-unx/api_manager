<?php
/**
 * Database Helper Functions
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get all records from a table with pagination
 */
function getAllRecords($table, $page = 1, $itemsPerPage = ITEMS_PER_PAGE, $where = null, $params = [], $orderBy = null) {
    $db = getDbConnection();
    
    // Calculate offset
    $offset = ($page - 1) * $itemsPerPage;
    
    // Build query
    $sql = "SELECT * FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
    }
    
    if ($orderBy) {
        $sql .= " ORDER BY $orderBy";
    } else {
        $sql .= " ORDER BY id DESC";
    }
    
    $sql .= " LIMIT :limit OFFSET :offset";
    
    // Prepare and execute query
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    // Bind where parameters if any
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get total count of records in a table
 */
function getTotalRecords($table, $where = null, $params = []) {
    $db = getDbConnection();
    
    $sql = "SELECT COUNT(*) as total FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
    }
    
    $stmt = $db->prepare($sql);
    
    // Bind where parameters if any
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $result = $stmt->fetch();
    
    return $result['total'];
}

/**
 * Get a single record by ID
 */
function getRecordById($table, $id, $idField = 'id') {
    $db = getDbConnection();
    
    $sql = "SELECT * FROM $table WHERE $idField = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    
    return $stmt->fetch();
}

/**
 * Insert a new record
 */
function insertRecord($table, $data) {
    $db = getDbConnection();
    
    $fields = array_keys($data);
    $placeholders = array_map(function($field) {
        return ":$field";
    }, $fields);
    
    $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
    
    $stmt = $db->prepare($sql);
    
    foreach ($data as $field => $value) {
        $stmt->bindValue(":$field", $value);
    }
    
    $stmt->execute();
    
    return $db->lastInsertId();
}

/**
 * Update an existing record
 */
function updateRecord($table, $data, $id, $idField = 'id') {
    $db = getDbConnection();
    
    $setStatements = array_map(function($field) {
        return "$field = :$field";
    }, array_keys($data));
    
    $sql = "UPDATE $table SET " . implode(', ', $setStatements) . " WHERE $idField = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    
    foreach ($data as $field => $value) {
        $stmt->bindValue(":$field", $value);
    }
    
    return $stmt->execute();
}

/**
 * Delete a record
 */
function deleteRecord($table, $id, $idField = 'id') {
    $db = getDbConnection();
    
    $sql = "DELETE FROM $table WHERE $idField = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    
    return $stmt->execute();
}

/**
 * Execute a raw SQL query
 */
function executeQuery($sql, $params = []) {
    $db = getDbConnection();
    
    $stmt = $db->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    
    return $stmt;
}

/**
 * Get aggregate data from a table
 */
function getAggregateData($table, $field, $function = 'COUNT', $where = null, $params = []) {
    $db = getDbConnection();
    
    $sql = "SELECT $function($field) as result FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
    }
    
    $stmt = $db->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $result = $stmt->fetch();
    
    return $result['result'];
}

/**
 * Begin a transaction
 */
function beginTransaction() {
    $db = getDbConnection();
    return $db->beginTransaction();
}

/**
 * Commit a transaction
 */
function commitTransaction() {
    $db = getDbConnection();
    return $db->commit();
}

/**
 * Rollback a transaction
 */
function rollbackTransaction() {
    $db = getDbConnection();
    return $db->rollBack();
}

/**
 * Check if database tables exist, if not create them
 */
function initializeDatabaseTables() {
    $db = getDbConnection();
    
    // Get database driver
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    // Check if tables exist
    try {
        $stmt = $db->query("SELECT 1 FROM " . TABLE_AGGREGATORS . " LIMIT 1");
        // If we get here, table exists
        return true;
    } catch (PDOException $e) {
        // Table doesn't exist, create it
        
        // Create tables based on driver
        if ($driver === 'mysql') {
            createMySQLTables($db);
        } elseif ($driver === 'pgsql') {
            createPostgreSQLTables($db);
        } else {
            die("Unsupported database driver: $driver");
        }
        
        return true;
    }
}

/**
 * Create tables for MySQL
 */
function createMySQLTables($db) {
    // api_aggregators table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_AGGREGATORS . " (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        api_base_url TEXT NOT NULL,
        agent_code VARCHAR(255) NOT NULL,
        agent_token VARCHAR(255) NOT NULL,
        api_version VARCHAR(50) DEFAULT 'v1',
        status TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // api_endpoints table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_ENDPOINTS . " (
        id INT AUTO_INCREMENT PRIMARY KEY,
        aggregator_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        endpoint_url TEXT NOT NULL,
        method VARCHAR(10) NOT NULL DEFAULT 'GET',
        request_body JSON,
        headers JSON,
        query_parameters JSON,
        status TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (aggregator_id) REFERENCES " . TABLE_AGGREGATORS . "(id) ON DELETE CASCADE
    )");
    
    // api_requests_log table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_LOGS . " (
        id INT AUTO_INCREMENT PRIMARY KEY,
        aggregator_id INT NOT NULL,
        endpoint_id INT NOT NULL,
        request_method VARCHAR(10) NOT NULL,
        request_url TEXT NOT NULL,
        request_body JSON,
        response_code INT,
        response_body JSON,
        status TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (aggregator_id) REFERENCES " . TABLE_AGGREGATORS . "(id) ON DELETE CASCADE,
        FOREIGN KEY (endpoint_id) REFERENCES " . TABLE_ENDPOINTS . "(id) ON DELETE CASCADE
    )");
    
    // api_request_templates table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_TEMPLATES . " (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        method VARCHAR(10) NOT NULL DEFAULT 'GET',
        url_pattern TEXT NOT NULL,
        default_headers JSON,
        query_parameters JSON,
        request_body JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
}

/**
 * Create tables for PostgreSQL
 */
function createPostgreSQLTables($db) {
    // api_aggregators table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_AGGREGATORS . " (
        id SERIAL PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        api_base_url TEXT NOT NULL,
        agent_code VARCHAR(255) NOT NULL,
        agent_token VARCHAR(255) NOT NULL,
        api_version VARCHAR(50) DEFAULT 'v1',
        status BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create trigger for updated_at
    $db->exec("CREATE OR REPLACE FUNCTION update_timestamp() RETURNS TRIGGER AS $$
    BEGIN
        NEW.updated_at = NOW();
        RETURN NEW;
    END;
    $$ LANGUAGE plpgsql;");
    
    $db->exec("DROP TRIGGER IF EXISTS update_" . TABLE_AGGREGATORS . "_timestamp ON " . TABLE_AGGREGATORS);
    $db->exec("CREATE TRIGGER update_" . TABLE_AGGREGATORS . "_timestamp
    BEFORE UPDATE ON " . TABLE_AGGREGATORS . "
    FOR EACH ROW EXECUTE PROCEDURE update_timestamp();");
    
    // api_endpoints table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_ENDPOINTS . " (
        id SERIAL PRIMARY KEY,
        aggregator_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        endpoint_url TEXT NOT NULL,
        method VARCHAR(10) NOT NULL DEFAULT 'GET',
        request_body JSONB,
        headers JSONB,
        query_parameters JSONB,
        status BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (aggregator_id) REFERENCES " . TABLE_AGGREGATORS . "(id) ON DELETE CASCADE
    )");
    
    $db->exec("DROP TRIGGER IF EXISTS update_" . TABLE_ENDPOINTS . "_timestamp ON " . TABLE_ENDPOINTS);
    $db->exec("CREATE TRIGGER update_" . TABLE_ENDPOINTS . "_timestamp
    BEFORE UPDATE ON " . TABLE_ENDPOINTS . "
    FOR EACH ROW EXECUTE PROCEDURE update_timestamp();");
    
    // api_requests_log table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_LOGS . " (
        id SERIAL PRIMARY KEY,
        aggregator_id INT NOT NULL,
        endpoint_id INT NOT NULL,
        request_method VARCHAR(10) NOT NULL,
        request_url TEXT NOT NULL,
        request_body JSONB,
        response_code INT,
        response_body JSONB,
        status BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (aggregator_id) REFERENCES " . TABLE_AGGREGATORS . "(id) ON DELETE CASCADE,
        FOREIGN KEY (endpoint_id) REFERENCES " . TABLE_ENDPOINTS . "(id) ON DELETE CASCADE
    )");
    
    $db->exec("DROP TRIGGER IF EXISTS update_" . TABLE_LOGS . "_timestamp ON " . TABLE_LOGS);
    $db->exec("CREATE TRIGGER update_" . TABLE_LOGS . "_timestamp
    BEFORE UPDATE ON " . TABLE_LOGS . "
    FOR EACH ROW EXECUTE PROCEDURE update_timestamp();");
    
    // api_request_templates table
    $db->exec("CREATE TABLE IF NOT EXISTS " . TABLE_TEMPLATES . " (
        id SERIAL PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        method VARCHAR(10) NOT NULL DEFAULT 'GET',
        url_pattern TEXT NOT NULL,
        default_headers JSONB,
        query_parameters JSONB,
        request_body JSONB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("DROP TRIGGER IF EXISTS update_" . TABLE_TEMPLATES . "_timestamp ON " . TABLE_TEMPLATES);
    $db->exec("CREATE TRIGGER update_" . TABLE_TEMPLATES . "_timestamp
    BEFORE UPDATE ON " . TABLE_TEMPLATES . "
    FOR EACH ROW EXECUTE PROCEDURE update_timestamp();");
}
