<?php
// HMIS Demo Configuration
// For XAMPP on Windows, root user normally has an empty password.
// Change these values if your MySQL/MariaDB settings are different.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'HMIS Practical Demo');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hmis_demo');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_BASE', '/hmis_demo');

// Show beginner-friendly errors during local learning.
// Turn this off on real/public systems.
ini_set('display_errors', '1');
error_reporting(E_ALL);
