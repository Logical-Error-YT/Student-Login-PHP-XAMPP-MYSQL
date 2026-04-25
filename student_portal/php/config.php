<?php
/**
 * StudentPortal — Database Configuration
 * Update these credentials to match your XAMPP/MySQL setup.
 */

define('DB_HOST',   'localhost');
define('DB_USER',   'root');       // Default XAMPP user
define('DB_PASS',   '');           // Default XAMPP password (empty)
define('DB_NAME',   'student_portal');
define('DB_CHARSET','utf8mb4');

/**
 * Returns a MySQLi connection using prepared statements.
 * Exits with an error if connection fails.
 */
function getDB(): mysqli {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($db->connect_error) {
        // In production you'd log this; here we show a friendly message
        die('<div style="font-family:sans-serif;padding:40px;color:#c0392b;">
            <h2>Database Connection Error</h2>
            <p>Could not connect to MySQL. Please check your credentials in <code>php/config.php</code> and ensure XAMPP MySQL is running.</p>
            <p><small>Error: ' . htmlspecialchars($db->connect_error) . '</small></p>
        </div>');
    }

    $db->set_charset(DB_CHARSET);
    return $db;
}
?>
