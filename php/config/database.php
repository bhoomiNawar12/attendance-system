<?php
/**
 * MySQL connection (mysqli) — XAMPP default settings.
 * Include once per request: require_once __DIR__ . '/database.php';
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eduattend(1)');
define('DB_CHARSET', 'utf8mb4');

/**
 * @return mysqli|null Shared connection reference
 */
function &dbConnectionRef(): ?mysqli
{
    static $connection = null;
    return $connection;
}

/**
 * Returns a shared mysqli connection instance.
 *
 * @return mysqli
 * @throws RuntimeException if connection fails
 */
function getDBConnection(): mysqli
{
    $connection = &dbConnectionRef();

    if ($connection instanceof mysqli) {
        return $connection;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $connection->set_charset(DB_CHARSET);
    } catch (mysqli_sql_exception $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        throw new RuntimeException(
            'Could not connect to the database. Check that MySQL is running and that "' . DB_NAME . '" exists.',
            0,
            $e
        );
    }

    return $connection;
}

/**
 * Close the shared connection (optional; PHP closes on script end).
 */
function closeDBConnection(): void
{
    $connection = &dbConnectionRef();

    if ($connection instanceof mysqli) {
        $connection->close();
        $connection = null;
    }
}
