<?php
/**
 * Database connection test
 * http://localhost/attendance-system/php/test-db.php
 */
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

try {
    getDBConnection();
    echo 'Database connected successfully';
} catch (RuntimeException $e) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
